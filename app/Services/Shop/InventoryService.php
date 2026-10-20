<?php

declare(strict_types=1);

namespace App\Services\Shop;

use App\Models\Shop\ShopInventoryMovement;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopOrderItem;
use App\Models\Shop\ShopProduct;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    /**
     * Reserva o deduce stock de manera transaccional con bloqueo pesimista en PostgreSQL.
     * Previene sobreventa en concurrencia (Race Conditions).
     */
    public function deductStockForOrder(ShopOrder $order, ?int $userId = null): void
    {
        DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $item) {
                // Si es bajo pedido (on_demand), no descuenta existencias físicas
                if ($item->inventory_type === 'on_demand') {
                    ShopInventoryMovement::create([
                        'product_id' => $item->product_id,
                        'order_id' => $order->id,
                        'movement_type' => 'out',
                        'quantity' => $item->quantity,
                        'stock_before' => 0,
                        'stock_after' => 0,
                        'reason' => "Venta bajo pedido (On Demand) - Pedido #{$order->order_number}",
                        'user_id' => $userId,
                    ]);
                    continue;
                }

                // Si es stock controlado, bloquea la fila pesimistamente con lockForUpdate()
                /** @var ShopProduct|null $product */
                $product = ShopProduct::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$product) {
                    throw new RuntimeException("El producto SKU [{$item->sku}] ya no existe en el catálogo.");
                }

                if ($product->stock_quantity < $item->quantity) {
                    throw new RuntimeException(
                        "Stock insuficiente para [{$product->name}]. Disponible: {$product->stock_quantity}, Solicitado: {$item->quantity}."
                    );
                }

                $stockBefore = $product->stock_quantity;
                $stockAfter = $stockBefore - $item->quantity;

                $product->update(['stock_quantity' => $stockAfter]);

                ShopInventoryMovement::create([
                    'product_id' => $product->id,
                    'order_id' => $order->id,
                    'movement_type' => 'out',
                    'quantity' => $item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reason' => "Venta y deducción confirmada - Pedido #{$order->order_number}",
                    'user_id' => $userId,
                ]);
            }
        }, 5); // 5 intentos de transacción en concurrencia
    }

    /**
     * Restaura inventario en caso de cancelación de orden o reembolso.
     */
    public function releaseStockForOrder(ShopOrder $order, ?int $userId = null): void
    {
        DB::transaction(function () use ($order, $userId) {
            foreach ($order->items as $item) {
                if ($item->inventory_type === 'on_demand' || !$item->product_id) {
                    continue;
                }

                /** @var ShopProduct|null $product */
                $product = ShopProduct::where('id', $item->product_id)->lockForUpdate()->first();

                if ($product) {
                    $stockBefore = $product->stock_quantity;
                    $stockAfter = $stockBefore + $item->quantity;

                    $product->update(['stock_quantity' => $stockAfter]);

                    ShopInventoryMovement::create([
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'movement_type' => 'reservation_release',
                        'quantity' => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reason' => "Restauración de inventario por cancelación de Pedido #{$order->order_number}",
                        'user_id' => $userId,
                    ]);
                }
            }
        }, 5);
    }

    /**
     * Ajuste manual de inventario físico (Ingreso de contenedor, merma o auditoría física).
     */
    public function adjustStock(
        ShopProduct $product,
        int $newQuantity,
        string $movementType,
        string $reason,
        ?int $userId = null
    ): void {
        DB::transaction(function () use ($product, $newQuantity, $movementType, $reason, $userId) {
            /** @var ShopProduct $lockedProduct */
            $lockedProduct = ShopProduct::where('id', $product->id)->lockForUpdate()->firstOrFail();

            $stockBefore = $lockedProduct->stock_quantity;
            $diff = abs($newQuantity - $stockBefore);

            $lockedProduct->update(['stock_quantity' => $newQuantity]);

            ShopInventoryMovement::create([
                'product_id' => $lockedProduct->id,
                'movement_type' => $movementType,
                'quantity' => $diff,
                'stock_before' => $stockBefore,
                'stock_after' => $newQuantity,
                'reason' => $reason,
                'user_id' => $userId,
            ]);
        });
    }
}

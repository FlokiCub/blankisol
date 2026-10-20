<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopInventoryMovement;
use App\Models\Shop\ShopOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = ShopOrder::with(['customer', 'items'])->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                  ->orWhereJsonContains('customer_data->email', $term)
                  ->orWhereJsonContains('customer_data->business_name', $term);
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('shop.admin.orders.index', compact('orders'));
    }

    public function show(ShopOrder $order): View
    {
        $order->load(['customer', 'items.product', 'shippingRate', 'payments']);
        return view('shop.admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, ShopOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        $previousStatus = $order->status;
        $newStatus = $validated['status'];

        $updates = ['status' => $newStatus];

        if ($request->filled('notes')) {
            $updates['notes'] = $validated['notes'];
        }

        // Marcar timestamps según el hito
        if ($newStatus === 'paid' && !$order->paid_at) {
            $updates['paid_at'] = now();
            $updates['payment_status'] = 'paid';
        } elseif ($newStatus === 'shipped' && !$order->shipped_at) {
            $updates['shipped_at'] = now();
        } elseif ($newStatus === 'delivered' && !$order->delivered_at) {
            $updates['delivered_at'] = now();
        } elseif ($newStatus === 'cancelled' && !$order->cancelled_at) {
            $updates['cancelled_at'] = now();

            // Si se cancela un pedido que había descontado stock, devolver unidades al inventario
            foreach ($order->items as $item) {
                if ($item->inventory_type === 'controlled_stock' && $item->product) {
                    $product = $item->product;
                    $stockBefore = $product->stock_quantity;
                    $product->increment('stock_quantity', $item->quantity);

                    ShopInventoryMovement::create([
                        'product_id' => $product->id,
                        'order_id' => $order->id,
                        'movement_type' => 'reservation_release',
                        'quantity' => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $product->stock_quantity,
                        'reason' => "Devolución automática por cancelación de pedido #{$order->order_number}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }

        $order->update($updates);

        return back()->with('success', "Estado del pedido #{$order->order_number} actualizado de '{$previousStatus}' a '{$newStatus}'.");
    }
}

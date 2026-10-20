<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shop\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control ejecutivo comercial de BLANKISOL SHOP.
     */
    public function index(Request $request): View
    {
        // 1. Métricas de Ventas y Facturación en USD
        $totalSales = (float) ShopOrder::where('payment_status', 'paid')->sum('total');
        $ordersCount = ShopOrder::count();
        $paidOrdersCount = ShopOrder::where('payment_status', 'paid')->count();
        $pendingOrdersCount = ShopOrder::where('status', 'pending')->count();
        $processingOrdersCount = ShopOrder::where('status', 'processing')->count();
        $shippedOrdersCount = ShopOrder::where('status', 'shipped')->count();
        $deliveredOrdersCount = ShopOrder::where('status', 'delivered')->count();

        // 2. Métricas de Catálogo e Inventario Mixto
        $activeProductsCount = ShopProduct::where('is_active', true)->count();
        $onDemandProductsCount = ShopProduct::where('inventory_type', 'on_demand')->count();
        $lowStockProducts = ShopProduct::where('inventory_type', 'controlled_stock')
            ->where('stock_quantity', '<=', 5)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();
        $lowStockCount = ShopProduct::where('inventory_type', 'controlled_stock')
            ->where('stock_quantity', '<=', 5)
            ->where('is_active', true)
            ->count();

        // 3. Clientes y Promociones
        $totalCustomers = ShopCustomer::count();
        $b2bCustomersCount = ShopCustomer::where('customer_type', 'business')->count();
        $activeCouponsCount = ShopCoupon::where('is_active', true)->count();

        // 4. Últimos Pedidos Recientes
        $recentOrders = ShopOrder::latest('id')->limit(8)->get();

        return view('shop.admin.dashboard', compact(
            'totalSales',
            'ordersCount',
            'paidOrdersCount',
            'pendingOrdersCount',
            'processingOrdersCount',
            'shippedOrdersCount',
            'deliveredOrdersCount',
            'activeProductsCount',
            'onDemandProductsCount',
            'lowStockProducts',
            'lowStockCount',
            'totalCustomers',
            'b2bCustomersCount',
            'activeCouponsCount',
            'recentOrders'
        ));
    }
}

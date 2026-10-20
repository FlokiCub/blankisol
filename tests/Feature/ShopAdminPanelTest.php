<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopInventoryMovement;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopOrderItem;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $shopAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shopAdmin = User::factory()->create([
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);
    }

    /**
     * Verifica que el Dashboard Comercial renderiza correctamente con métricas y pedidos.
     */
    public function test_admin_can_view_dashboard_with_metrics(): void
    {
        $product = ShopProduct::create([
            'sku' => 'TEST-SKU-01',
            'slug' => 'test-product',
            'name' => 'Producto de Prueba',
            'price' => 100.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 3, // stock bajo
            'is_active' => true,
        ]);

        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'customer_type' => 'individual',
            'customer_data' => ['first_name' => 'Ana', 'email' => 'ana@example.com'],
            'shipping_address' => ['address' => 'Calle 10', 'province' => 'La Habana'],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency' => 'USD',
            'subtotal' => 100.00,
            'total' => 100.00,
        ]);

        $response = $this->actingAs($this->shopAdmin)->get('/shop/admin');

        $response->assertStatus(200);
        $response->assertViewIs('shop.admin.dashboard');
        $response->assertViewHas('totalSales');
        $response->assertViewHas('lowStockCount', 1);
        $response->assertSee('TEST-SKU-01');
        $response->assertSee('#' . $order->order_number);
    }

    /**
     * Verifica el ciclo CRUD completo de Categorías Comerciales.
     */
    public function test_admin_can_manage_categories(): void
    {
        // 1. Index
        $response = $this->actingAs($this->shopAdmin)->get('/shop/admin/categories');
        $response->assertStatus(200);
        $response->assertViewIs('shop.admin.categories.index');

        // 2. Create form
        $responseCreate = $this->actingAs($this->shopAdmin)->get('/shop/admin/categories/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertViewIs('shop.admin.categories.create');

        // 3. Store
        $responseStore = $this->actingAs($this->shopAdmin)->post('/shop/admin/categories', [
            'name' => 'Energía Solar y Baterías',
            'description' => 'Paneles fotovoltaicos e inversores híbridos',
            'icon' => 'fa-solid fa-solar-panel',
            'sort_order' => 2,
            'is_active' => '1',
        ]);

        $responseStore->assertRedirect(route('shop.admin.categories.index'));
        $this->assertDatabaseHas('shop_categories', [
            'name' => 'Energía Solar y Baterías',
            'slug' => 'energia-solar-y-baterias',
            'is_active' => true,
        ]);

        $category = ShopCategory::where('slug', 'energia-solar-y-baterias')->firstOrFail();

        // 4. Edit
        $responseEdit = $this->actingAs($this->shopAdmin)->get("/shop/admin/categories/{$category->id}/edit");
        $responseEdit->assertStatus(200);
        $responseEdit->assertViewIs('shop.admin.categories.edit');

        // 5. Update
        $responseUpdate = $this->actingAs($this->shopAdmin)->put("/shop/admin/categories/{$category->id}", [
            'name' => 'Sistemas Fotovoltaicos',
            'description' => 'Descripción actualizada',
            'sort_order' => 1,
            'is_active' => '1',
        ]);

        $responseUpdate->assertRedirect(route('shop.admin.categories.index'));
        $this->assertEquals('Sistemas Fotovoltaicos', $category->fresh()->name);

        // 6. Toggle Status
        $responseToggle = $this->actingAs($this->shopAdmin)->post("/shop/admin/categories/{$category->id}/toggle-status");
        $responseToggle->assertRedirect();
        $this->assertFalse($category->fresh()->is_active);
    }

    /**
     * Verifica el ciclo CRUD completo de Productos Comerciales (Stock Físico y On Demand).
     */
    public function test_admin_can_manage_products(): void
    {
        $category = ShopCategory::create([
            'name' => 'Maquinaria',
            'slug' => 'maquinaria',
            'is_active' => true,
        ]);

        // 1. Index
        $response = $this->actingAs($this->shopAdmin)->get('/shop/admin/products');
        $response->assertStatus(200);
        $response->assertViewIs('shop.admin.products.index');

        // 2. Create form
        $responseCreate = $this->actingAs($this->shopAdmin)->get('/shop/admin/products/create');
        $responseCreate->assertStatus(200);
        $responseCreate->assertViewIs('shop.admin.products.create');

        // 3. Store producto con stock físico
        $responseStore = $this->actingAs($this->shopAdmin)->post('/shop/admin/products', [
            'category_id' => $category->id,
            'sku' => 'MAQ-IND-01',
            'name' => 'Generador Diesel Silencioso 30kW',
            'price' => 4500.00,
            'sale_price' => 4200.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 12,
            'min_stock_alert' => 3,
            'is_active' => '1',
            'is_featured' => '1',
        ]);

        $responseStore->assertRedirect(route('shop.admin.products.index'));
        $this->assertDatabaseHas('shop_products', [
            'sku' => 'MAQ-IND-01',
            'stock_quantity' => 12,
            'inventory_type' => 'controlled_stock',
            'is_featured' => true,
        ]);

        $product = ShopProduct::where('sku', 'MAQ-IND-01')->firstOrFail();

        // 4. Update a producto On Demand
        $responseUpdate = $this->actingAs($this->shopAdmin)->put("/shop/admin/products/{$product->id}", [
            'category_id' => $category->id,
            'sku' => 'MAQ-IND-01',
            'name' => 'Generador Diesel Silencioso 30kW Pro',
            'price' => 4500.00,
            'inventory_type' => 'on_demand',
            'estimated_delivery_days' => '30 días hábiles',
            'is_active' => '1',
        ]);

        $responseUpdate->assertRedirect(route('shop.admin.products.index'));
        $this->assertTrue($product->fresh()->isOnDemand());
        $this->assertEquals(0, $product->fresh()->stock_quantity);

        // 5. Toggle Status
        $responseToggle = $this->actingAs($this->shopAdmin)->post("/shop/admin/products/{$product->id}/toggle-status");
        $responseToggle->assertRedirect();
        $this->assertFalse($product->fresh()->is_active);

        // 6. Destroy
        $responseDestroy = $this->actingAs($this->shopAdmin)->delete("/shop/admin/products/{$product->id}");
        $responseDestroy->assertRedirect(route('shop.admin.products.index'));
        $this->assertSoftDeleted('shop_products', ['id' => $product->id]);
    }

    /**
     * Verifica la inspección de pedidos, transición de estados y devolución de inventario al cancelar.
     */
    public function test_admin_can_manage_orders_and_status_transitions(): void
    {
        $product = ShopProduct::create([
            'sku' => 'SOLAR-INV-5K',
            'slug' => 'inversor-solar-5kw',
            'name' => 'Inversor Solar 5kW',
            'price' => 950.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'customer_type' => 'business',
            'customer_data' => [
                'business_name' => 'Agropecuaria Central S.A.',
                'tax_id' => 'CIF-8839201',
                'email' => 'compras@agropecuaria.cu',
            ],
            'shipping_address' => [
                'address' => 'Carretera Central km 42',
                'province' => 'Matanzas',
                'municipality' => 'Jovellanos',
            ],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency' => 'USD',
            'subtotal' => 1900.00,
            'total' => 1900.00,
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'sku' => $product->sku,
            'product_name' => $product->name,
            'inventory_type' => 'controlled_stock',
            'quantity' => 2,
            'unit_price' => 950.00,
            'total_price' => 1900.00,
        ]);

        // 1. Index de pedidos
        $responseIndex = $this->actingAs($this->shopAdmin)->get('/shop/admin/orders');
        $responseIndex->assertStatus(200);
        $responseIndex->assertViewIs('shop.admin.orders.index');
        $responseIndex->assertSee($order->order_number);

        // 2. Show detalle
        $responseShow = $this->actingAs($this->shopAdmin)->get("/shop/admin/orders/{$order->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertViewIs('shop.admin.orders.show');
        $responseShow->assertSee('Agropecuaria Central');
        $responseShow->assertSee('SOLAR-INV-5K');

        // 3. Actualizar a Pagado
        $responsePaid = $this->actingAs($this->shopAdmin)->post("/shop/admin/orders/{$order->id}/status", [
            'status' => 'paid',
            'notes' => 'Pago confirmado vía transferencia bancaria internacional',
        ]);
        $responsePaid->assertRedirect();

        $freshOrder = $order->fresh();
        $this->assertEquals('paid', $freshOrder->status);
        $this->assertEquals('paid', $freshOrder->payment_status);
        $this->assertNotNull($freshOrder->paid_at);

        // 4. Cancelar pedido -> Comprueba devolución al inventario y kardex
        $responseCancel = $this->actingAs($this->shopAdmin)->post("/shop/admin/orders/{$order->id}/status", [
            'status' => 'cancelled',
            'notes' => 'Cancelación solicitada por el cliente',
        ]);
        $responseCancel->assertRedirect();

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->cancelled_at);
        $this->assertEquals(12, $product->fresh()->stock_quantity); // 10 + 2 devueltas

        $this->assertDatabaseHas('shop_inventory_movements', [
            'product_id' => $product->id,
            'order_id' => $order->id,
            'movement_type' => 'reservation_release',
            'quantity' => 2,
        ]);
    }

    /**
     * Verifica la gestión de cupones de descuento.
     */
    public function test_admin_can_manage_coupons(): void
    {
        $responseIndex = $this->actingAs($this->shopAdmin)->get('/shop/admin/coupons');
        $responseIndex->assertStatus(200);
        $responseIndex->assertViewIs('shop.admin.coupons.index');

        // Crear cupón
        $responseStore = $this->actingAs($this->shopAdmin)->post('/shop/admin/coupons', [
            'code' => 'BLACKFRIDAY25',
            'name' => 'Black Friday 25%',
            'discount_type' => 'percentage',
            'discount_value' => 25.00,
            'min_order_amount' => 150.00,
            'max_discount_amount' => 100.00,
            'is_active' => '1',
        ]);

        $responseStore->assertRedirect(route('shop.admin.coupons.index'));
        $this->assertDatabaseHas('shop_coupons', ['code' => 'BLACKFRIDAY25']);

        $coupon = ShopCoupon::where('code', 'BLACKFRIDAY25')->firstOrFail();

        // Toggle status
        $this->actingAs($this->shopAdmin)->post("/shop/admin/coupons/{$coupon->id}/toggle-status");
        $this->assertFalse($coupon->fresh()->is_active);

        // Delete
        $this->actingAs($this->shopAdmin)->delete("/shop/admin/coupons/{$coupon->id}");
        $this->assertDatabaseMissing('shop_coupons', ['id' => $coupon->id]);
    }

    /**
     * Verifica la gestión de zonas y tarifas de entrega.
     */
    public function test_admin_can_manage_shipping_zones_and_rates(): void
    {
        $responseIndex = $this->actingAs($this->shopAdmin)->get('/shop/admin/shipping');
        $responseIndex->assertStatus(200);
        $responseIndex->assertViewIs('shop.admin.shipping.index');

        // 1. Crear Zona
        $responseZone = $this->actingAs($this->shopAdmin)->post('/shop/admin/shipping/zones', [
            'name' => 'Zona Central',
            'provinces' => 'Cienfuegos, Villa Clara, Sancti Spíritus',
        ]);
        $responseZone->assertRedirect(route('shop.admin.shipping.index'));

        $zone = ShopShippingZone::where('name', 'Zona Central')->firstOrFail();
        $this->assertContains('Villa Clara', $zone->provinces);

        // 2. Crear Tarifa para esa Zona
        $responseRate = $this->actingAs($this->shopAdmin)->post('/shop/admin/shipping/rates', [
            'zone_id' => $zone->id,
            'name' => 'Entrega Estándar HUB Central',
            'price' => 35.00,
            'estimated_days' => '3 a 5 días hábiles',
        ]);
        $responseRate->assertRedirect(route('shop.admin.shipping.index'));

        $rate = ShopShippingRate::where('name', 'Entrega Estándar HUB Central')->firstOrFail();
        $this->assertEquals(35.00, $rate->price);

        // 3. Eliminar Tarifa
        $this->actingAs($this->shopAdmin)->delete("/shop/admin/shipping/rates/{$rate->id}");
        $this->assertDatabaseMissing('shop_shipping_rates', ['id' => $rate->id]);
    }
}

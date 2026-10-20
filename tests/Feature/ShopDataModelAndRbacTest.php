<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopOrderItem;
use App\Models\Shop\ShopPayment;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShopDataModelAndRbacTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que todas las 12 tablas de BLANKISOL SHOP existen en PostgreSQL.
     */
    public function test_all_shop_database_tables_exist(): void
    {
        $expectedTables = [
            'shop_categories',
            'shop_products',
            'shop_product_images',
            'shop_product_variants',
            'shop_coupons',
            'shop_shipping_zones',
            'shop_shipping_rates',
            'shop_customers',
            'shop_customer_addresses',
            'shop_carts',
            'shop_cart_items',
            'shop_orders',
            'shop_order_items',
            'shop_payments',
            'shop_inventory_movements',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(Schema::hasTable($table), "La tabla [{$table}] no existe en la base de datos.");
        }
    }

    /**
     * Verifica el ciclo de vida del modelo de catálogo e inventario en USD.
     */
    public function test_product_model_creation_and_inventory_modalities(): void
    {
        $category = ShopCategory::create([
            'name' => 'Movilidad Eléctrica',
            'slug' => 'movilidad-electrica',
            'description' => 'Motos y bicis eléctricas de alta autonomía.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('shop_categories', ['slug' => 'movilidad-electrica']);

        // 1. Producto con stock controlado
        $productStock = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'MOTO-E-001',
            'slug' => 'moto-electrica-blankisol-pro',
            'name' => 'Moto Eléctrica Pro 72V',
            'price' => 1250.00,
            'sale_price' => 1100.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        $this->assertEquals(1100.00, $productStock->effective_price);
        $this->assertFalse($productStock->isOutOfStock());
        $this->assertFalse($productStock->isOnDemand());

        // 2. Producto bajo pedido
        $productOnDemand = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'GEN-IND-002',
            'slug' => 'generador-industrial-50kva',
            'name' => 'Generador Industrial 50kVA',
            'price' => 8500.00,
            'inventory_type' => 'on_demand',
            'stock_quantity' => 0,
            'estimated_delivery_days' => '20 a 25 días hábiles',
            'is_active' => true,
        ]);

        $this->assertEquals(8500.00, $productOnDemand->effective_price);
        $this->assertTrue($productOnDemand->isOnDemand());
        $this->assertFalse($productOnDemand->isOutOfStock()); // Bajo pedido no se considera agotado
    }

    /**
     * Verifica el cálculo de descuentos de cupones en USD.
     */
    public function test_coupon_calculations_and_rules(): void
    {
        $couponPercent = ShopCoupon::create([
            'code' => 'VERANO10',
            'name' => '10% de descuento',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'min_order_amount' => 100.00,
            'max_discount_amount' => 50.00,
            'is_active' => true,
        ]);

        // Compra por debajo del mínimo -> $0 descuento
        $this->assertEquals(0.00, $couponPercent->calculateDiscount(80.00));
        // Compra de $200 -> 10% = $20
        $this->assertEquals(20.00, $couponPercent->calculateDiscount(200.00));
        // Compra de $1000 -> 10% = $100, pero tope max es $50 -> $50
        $this->assertEquals(50.00, $couponPercent->calculateDiscount(1000.00));

        $couponFixed = ShopCoupon::create([
            'code' => 'PROMO15USD',
            'name' => 'Rebaja fija de 15 USD',
            'discount_type' => 'fixed',
            'discount_value' => 15.00,
            'min_order_amount' => 50.00,
            'is_active' => true,
        ]);

        $this->assertEquals(15.00, $couponFixed->calculateDiscount(100.00));
        $this->assertEquals(0.00, $couponFixed->calculateDiscount(30.00)); // debajo del mínimo
    }

    /**
     * Verifica clientes B2C y B2B, creación de pedidos y snapshots inmutables.
     */
    public function test_customer_b2c_b2b_and_order_creation_with_snapshots(): void
    {
        $customerB2B = ShopCustomer::create([
            'customer_type' => 'business',
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => 'carlos@hotelcuba.cu',
            'phone' => '+5351234567',
            'business_name' => 'Corporación Hotelera del Caribe S.A.',
            'tax_id' => 'B-987654321',
        ]);

        $this->assertTrue($customerB2B->isBusiness());
        $this->assertStringContainsString('Corporación Hotelera', $customerB2B->full_name);

        $zone = ShopShippingZone::create(['name' => 'La Habana', 'is_active' => true]);
        $rate = ShopShippingRate::create([
            'zone_id' => $zone->id,
            'name' => 'Entrega Express',
            'price' => 25.00,
            'is_active' => true,
        ]);

        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'customer_id' => $customerB2B->id,
            'customer_type' => 'business',
            'customer_data' => [
                'business_name' => $customerB2B->business_name,
                'tax_id' => $customerB2B->tax_id,
                'contact' => $customerB2B->full_name,
                'email' => $customerB2B->email,
            ],
            'shipping_address' => [
                'address' => 'Calle 23 esq. L, Vedado',
                'province' => 'La Habana',
                'municipality' => 'Plaza de la Revolución',
            ],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency' => 'USD',
            'subtotal' => 1500.00,
            'discount_amount' => 50.00,
            'shipping_rate_id' => $rate->id,
            'shipping_fee' => 25.00,
            'tax_amount' => 0.00,
            'total' => 1475.00,
        ]);

        $this->assertDatabaseHas('shop_orders', [
            'id' => $order->id,
            'customer_type' => 'business',
            'total' => 1475.00,
        ]);

        ShopOrderItem::create([
            'order_id' => $order->id,
            'sku' => 'EQUIP-PRO-01',
            'product_name' => 'Bomba Sumergible Industrial',
            'inventory_type' => 'controlled_stock',
            'quantity' => 1,
            'unit_price' => 1500.00,
            'total_price' => 1500.00,
        ]);

        $this->assertCount(1, $order->fresh()->items);
    }

    /**
     * Verifica que el rol ADMINISTRADOR_TIENDA puede ingresar al panel de tienda (/shop/admin).
     */
    public function test_shop_admin_user_can_access_shop_admin_dashboard(): void
    {
        $shopAdmin = User::factory()->create([
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        $this->assertTrue($shopAdmin->isShopAdmin());

        $response = $this->actingAs($shopAdmin)->get('/shop/admin');
        $response->assertStatus(200);
        $response->assertViewIs('shop.admin.dashboard');
        $response->assertSee('BLANKISOL SHOP');
    }

    /**
     * Verifica que el Director General también tiene acceso ejecutivo al panel de SHOP.
     */
    public function test_director_can_access_shop_admin_dashboard(): void
    {
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'is_active' => true,
        ]);

        $response = $this->actingAs($director)->get('/shop/admin');
        $response->assertStatus(200);
    }

    /**
     * REGLA DE AISLAMIENTO ESTRICTO:
     * Verifica que ADMINISTRADOR_TIENDA es RECHAZADO (HTTP 403) en todas las rutas protegidas del SCGI.
     */
    public function test_shop_admin_is_strictly_forbidden_from_scgi_routes(): void
    {
        $shopAdmin = User::factory()->create([
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        // Rutas críticas del SCGI que jamás deben ser accesibles por Administrador_Tienda
        $scgiRoutes = [
            '/admin',                 // Administración General SCGI
            '/dispatch/scanner',      // Despacho Puerto Mariel
            '/cashier',               // Cajas Regionales y Recaudación
            '/customs',               // Inspección Aduana
            '/logistics/routes',      // Hojas de Ruta Logísticas
            '/profitability',         // Factibilidad y Rentabilidad Económica
            '/audit',                 // Auditoría Financiera
            '/manifests/import',      // Importación de Manifiestos
        ];

        foreach ($scgiRoutes as $route) {
            $response = $this->actingAs($shopAdmin)->get($route);
            $this->assertEquals(403, $response->status(), "El usuario Administrador_Tienda pudo acceder indebidamente a la ruta de SCGI: [{$route}]");
        }
    }

    /**
     * Verifica que usuarios con otros roles del SCGI (p. ej. cajero, aduana) no pueden entrar a /shop/admin.
     */
    public function test_scgi_roles_cannot_access_shop_admin(): void
    {
        $cajero = User::factory()->create([
            'role' => UserRole::CAJERO_REGIONAL,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cajero)->get('/shop/admin');
        $response->assertStatus(403);

        $aduana = User::factory()->create([
            'role' => UserRole::ADUANA,
            'is_active' => true,
        ]);

        $response = $this->actingAs($aduana)->get('/shop/admin');
        $response->assertStatus(403);
    }

    /**
     * Verifica que usuarios invitados sin autenticar son redirigidos al login al intentar entrar a /shop/admin.
     */
    public function test_unauthenticated_guests_are_redirected_from_shop_admin(): void
    {
        $response = $this->get('/shop/admin');
        $response->assertRedirect(route('shop.login'));
    }

    /**
     * Verifica que las rutas públicas de la tienda (/shop, /shop/products) están abiertas al público.
     */
    public function test_public_shop_routes_are_accessible_to_everyone(): void
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertViewIs('shop.storefront.index');
        $response->assertSee('BLANKISOL SHOP');

        $responseProducts = $this->get('/shop/products');
        $responseProducts->assertStatus(200);
        $responseProducts->assertViewIs('shop.storefront.products');
    }
}

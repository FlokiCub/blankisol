<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Shop\ShopCart;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopCustomerAddress;
use App\Models\Shop\ShopOrder;
use App\Models\Shop\ShopOrderItem;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopCartCheckoutAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['X-Cart-Session' => 'shop_test_session_' . uniqid()]);
    }

    /**
     * Verifica el ciclo de vida del carrito: agregar, actualizar cantidad, aplicar cupón y remover.
     */
    public function test_cart_lifecycle_add_update_remove_and_coupon(): void
    {
        $category = ShopCategory::create(['name' => 'Solar', 'slug' => 'solar', 'is_active' => true]);

        $product = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'BAT-LIT-48V',
            'slug' => 'bateria-litio-48v',
            'name' => 'Batería de Litio 48V 100Ah',
            'price' => 850.00,
            'sale_price' => 800.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $coupon = ShopCoupon::create([
            'code' => 'SOLAR50',
            'name' => 'Rebaja $50 Solar',
            'discount_type' => 'fixed',
            'discount_value' => 50.00,
            'min_order_amount' => 500.00,
            'is_active' => true,
        ]);

        // 1. Ver carrito vacío
        $responseCart = $this->get('/shop/cart');
        $responseCart->assertStatus(200);
        $responseCart->assertViewIs('shop.cart.index');
        $responseCart->assertSee('Tu carrito está vacío');

        // 2. Agregar ítem vía AJAX / POST
        $responseAdd = $this->postJson('/shop/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $responseAdd->assertStatus(200);
        $responseAdd->assertJson(['success' => true]);

        $this->assertDatabaseHas('shop_cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // 3. Aplicar cupón de descuento
        $responseCoupon = $this->post('/shop/cart/coupon', [
            'coupon_code' => 'SOLAR50',
        ]);
        $responseCoupon->assertRedirect();

        $cart = ShopCart::firstOrFail();
        $this->assertEquals('SOLAR50', $cart->coupon_code);

        // 4. Intentar agregar más unidades de las disponibles físicamente -> Rechazo
        $responseOverStock = $this->postJson('/shop/cart/add', [
            'product_id' => $product->id,
            'quantity' => 10, // Stock total es 4, ya hay 2
        ]);
        $responseOverStock->assertStatus(422);

        // 5. Remover cupón
        $responseRemoveCoupon = $this->delete('/shop/cart/coupon');
        $responseRemoveCoupon->assertRedirect();
        $this->assertNull($cart->fresh()->coupon_code);
    }

    /**
     * Verifica Checkout B2C: Snapshot inmutable, deducción transaccional de stock e inicio de pago.
     */
    public function test_checkout_b2c_creates_order_with_immutable_snapshot_and_deducts_stock(): void
    {
        $zone = ShopShippingZone::create(['name' => 'La Habana', 'is_active' => true]);
        $rate = ShopShippingRate::create([
            'zone_id' => $zone->id,
            'name' => 'Entrega Express Mariel-Habana',
            'price' => 30.00,
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'sku' => 'MOTO-E-72V',
            'slug' => 'moto-electrica-72v',
            'name' => 'Moto Eléctrica 72V 35Ah',
            'price' => 1200.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        // Agregar al carrito
        $this->postJson('/shop/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $idempotencyKey = (string) Str::uuid();

        // Procesar Checkout B2C
        $responseCheckout = $this->post('/shop/checkout', [
            'customer_type' => 'individual',
            'first_name' => 'Roberto',
            'last_name' => 'Hernández',
            'email' => 'roberto@gmail.com',
            'phone' => '+53 5294 1122',
            'address' => 'Calle 42 No. 512 e/ 5ta y 7ma',
            'province' => 'La Habana',
            'municipality' => 'Playa',
            'shipping_rate_id' => $rate->id,
            'idempotency_key' => $idempotencyKey,
        ]);

        // Redirige al simulador sandbox
        $responseCheckout->assertRedirect();

        // Verificar Orden creada en base de datos
        $this->assertDatabaseHas('shop_orders', [
            'customer_type' => 'individual',
            'subtotal' => 2400.00, // 2 x 1200
            'shipping_fee' => 30.00,
            'total' => 2430.00,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'idempotency_key' => $idempotencyKey,
        ]);

        // Verificar deducción de stock: 8 - 2 = 6
        $this->assertEquals(6, $product->fresh()->stock_quantity);

        // Verificar kardex de inventario
        $this->assertDatabaseHas('shop_inventory_movements', [
            'product_id' => $product->id,
            'movement_type' => 'out',
            'quantity' => 2,
            'stock_before' => 8,
            'stock_after' => 6,
        ]);

        // Verificar carrito vacío
        $cart = ShopCart::firstOrFail();
        $this->assertCount(0, $cart->items);
    }

    /**
     * Verifica Checkout B2B: Datos fiscales de empresa y snapshot corporativo.
     */
    public function test_checkout_b2b_creates_business_order(): void
    {
        $zone = ShopShippingZone::create(['name' => 'Zona Mariel', 'is_active' => true]);
        $rate = ShopShippingRate::create([
            'zone_id' => $zone->id,
            'name' => 'Retiro en HUB Mariel',
            'price' => 0.00,
            'is_active' => true,
        ]);

        $productOnDemand = ShopProduct::create([
            'sku' => 'GEN-IND-100K',
            'slug' => 'generador-industrial-100kva',
            'name' => 'Generador Industrial 100kVA',
            'price' => 15000.00,
            'inventory_type' => 'on_demand',
            'stock_quantity' => 0,
            'is_active' => true,
        ]);

        $this->postJson('/shop/cart/add', [
            'product_id' => $productOnDemand->id,
            'quantity' => 1,
        ]);

        $responseCheckout = $this->post('/shop/checkout', [
            'customer_type' => 'business',
            'business_name' => 'BioCuba Soluciones S.A.',
            'tax_id' => 'CIF-B98129381',
            'first_name' => 'Ing. Alejandro',
            'last_name' => 'Valdés',
            'email' => 'compras@biocuba.cu',
            'phone' => '+53 5888 9900',
            'address' => 'Zona Especial de Desarrollo Mariel, Sector A',
            'province' => 'Artemisa',
            'municipality' => 'Mariel',
            'shipping_rate_id' => $rate->id,
        ]);

        $responseCheckout->assertRedirect();

        $order = ShopOrder::where('customer_type', 'business')->firstOrFail();
        $this->assertTrue($order->isBusiness());
        $this->assertEquals('BioCuba Soluciones S.A.', $order->customer_data['business_name']);
        $this->assertEquals('CIF-B98129381', $order->customer_data['tax_id']);
        $this->assertEquals(15000.00, (float) $order->total);
    }

    /**
     * Verifica que la clave de idempotencia previene órdenes duplicadas y sobre-deducciones.
     */
    public function test_checkout_idempotency_prevents_duplicate_orders(): void
    {
        $zone = ShopShippingZone::create(['name' => 'Occidente', 'is_active' => true]);
        $rate = ShopShippingRate::create(['zone_id' => $zone->id, 'name' => 'Envío Estándar', 'price' => 20.00, 'is_active' => true]);

        $product = ShopProduct::create([
            'sku' => 'KIT-SOLAR-01',
            'slug' => 'kit-solar-autonomo',
            'name' => 'Kit Solar Autónomo 3kW',
            'price' => 2500.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $this->postJson('/shop/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

        $idempotencyKey = 'IDEMPOTENT-ORDER-KEY-9999';

        $data = [
            'customer_type' => 'individual',
            'first_name' => 'Elena',
            'last_name' => 'Ramos',
            'email' => 'elena@example.com',
            'phone' => '+53 5111 2233',
            'address' => 'Calle 100 #23',
            'province' => 'La Habana',
            'municipality' => 'Marianao',
            'shipping_rate_id' => $rate->id,
            'idempotency_key' => $idempotencyKey,
        ];

        // Primer envío -> Se crea orden
        $this->post('/shop/checkout', $data);
        $this->assertEquals(9, $product->fresh()->stock_quantity);

        // Segundo envío con la misma clave de idempotencia
        $this->post('/shop/checkout', $data);

        // Debe seguir habiendo solo 1 orden y el stock debe permanecer en 9 (no 8)
        $this->assertCount(1, ShopOrder::where('idempotency_key', $idempotencyKey)->get());
        $this->assertEquals(9, $product->fresh()->stock_quantity);
    }

    /**
     * Verifica que el webhook procesa la confirmación con firma HMAC-SHA256 y marca el pedido como pagado.
     */
    public function test_payment_webhook_with_valid_hmac_marks_order_as_paid(): void
    {
        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'customer_type' => 'individual',
            'customer_data' => ['email' => 'test@example.com'],
            'shipping_address' => ['address' => 'Vedado', 'province' => 'La Habana'],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency' => 'USD',
            'subtotal' => 500.00,
            'total' => 500.00,
        ]);

        $payload = json_encode([
            'event' => 'payment.succeeded',
            'order_number' => $order->order_number,
            'gateway_reference' => 'GATEWAY-REF-8888',
            'amount' => 500.00,
            'currency' => 'USD',
            'status' => 'paid',
        ]);

        $secret = config('app.key', 'blankisol_sandbox_secret_key_2026');
        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/shop/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_BLANKISOL_SHOP_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $freshOrder = $order->fresh();
        $this->assertEquals('paid', $freshOrder->status);
        $this->assertEquals('paid', $freshOrder->payment_status);
        $this->assertNotNull($freshOrder->paid_at);

        $this->assertDatabaseHas('shop_payments', [
            'order_id' => $order->id,
            'transaction_id' => 'GATEWAY-REF-8888',
            'status' => 'paid',
        ]);
    }

    /**
     * Verifica que el webhook rechaza solicitudes sin firma o con firma HMAC falsificada.
     */
    public function test_payment_webhook_rejects_invalid_signature(): void
    {
        $payload = json_encode(['order_number' => 'ORD-BOGUS']);

        // 1. Sin firma
        $responseNoSig = $this->call(
            'POST',
            '/shop/payment/webhook',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );
        $responseNoSig->assertStatus(400);

        // 2. Firma falsificada
        $responseBadSig = $this->call(
            'POST',
            '/shop/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_BLANKISOL_SHOP_SIGNATURE' => 'invalid_forged_hash',
            ],
            $payload
        );
        $responseBadSig->assertStatus(400);
    }

    /**
     * Verifica el simulador de sandbox: aprobación y confirmación de compra.
     */
    public function test_sandbox_simulator_flow(): void
    {
        $order = ShopOrder::create([
            'order_number' => ShopOrder::generateOrderNumber(),
            'customer_type' => 'individual',
            'customer_data' => ['email' => 'sandbox@example.com'],
            'shipping_address' => ['address' => 'Miramar', 'province' => 'La Habana'],
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'currency' => 'USD',
            'subtotal' => 200.00,
            'total' => 200.00,
        ]);

        // Ver simulador
        $responseView = $this->get("/shop/payment/sandbox/simulator?order={$order->order_number}&ref=SBX-123");
        $responseView->assertStatus(200);
        $responseView->assertViewIs('shop.payment.simulator');
        $responseView->assertSee($order->order_number);

        // Simular Aprobación
        $responseApprove = $this->post('/shop/payment/sandbox/process', [
            'order_number' => $order->order_number,
            'action' => 'approve',
            'gateway_reference' => 'SBX-123',
        ]);

        $responseApprove->assertRedirect(route('shop.checkout.success', $order->order_number));

        $fresh = $order->fresh();
        $this->assertEquals('paid', $fresh->status);
        $this->assertEquals('paid', $fresh->payment_status);

        // Ver pantalla de confirmación exitosa
        $responseSuccess = $this->get(route('shop.checkout.success', $order->order_number));
        $responseSuccess->assertStatus(200);
        $responseSuccess->assertViewIs('shop.checkout.success');
        $responseSuccess->assertSee($order->order_number);
    }

    /**
     * Verifica que en checkout un cliente autenticado puede guardar su dirección en favoritos
     * y que en visitas posteriores se carga y renderiza en el selector de direcciones guardadas.
     */
    public function test_checkout_saves_customer_delivery_address_and_loads_in_selector(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente.favorito@gmail.com',
            'role' => \App\Enums\UserRole::CLIENTE_TIENDA,
        ]);

        $customer = ShopCustomer::create([
            'user_id' => $user->id,
            'customer_type' => 'individual',
            'first_name' => 'Ernesto',
            'last_name' => 'Guevara',
            'email' => $user->email,
            'phone' => '+53 5299 8877',
        ]);

        $zone = ShopShippingZone::create([
            'name' => 'Zona Habana Test',
            'provinces' => ['La Habana'],
            'is_active' => true,
        ]);
        $rate = ShopShippingRate::create([
            'zone_id' => $zone->id,
            'name' => 'Envío Estándar',
            'price' => 15.00,
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'sku' => 'PANEL-550W',
            'slug' => 'panel-solar-550w',
            'name' => 'Panel Solar Monocristalino 550W',
            'price' => 250.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // Autenticar cliente con nueva sesión y agregar producto al carrito
        $cartSession = 'saved_addr_sess_' . uniqid();
        $this->withHeaders(['X-Cart-Session' => $cartSession]);
        $this->actingAs($user);
        $this->postJson('/shop/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 1. Procesar checkout marcando 'save_address' => 1 y 'is_default_address' => 1
        $idempotencyKey = (string) Str::uuid();
        $responseCheckout = $this->post('/shop/checkout', [
            'customer_type' => 'individual',
            'first_name' => 'Ernesto',
            'last_name' => 'Guevara',
            'email' => $user->email,
            'phone' => '+53 5299 8877',
            'recipient_name' => 'Familiar Receptor',
            'recipient_phone' => '+53 5333 4444',
            'address' => 'Avenida 31 No. 4412 e/ 44 y 46',
            'province' => 'La Habana',
            'municipality' => 'Playa',
            'shipping_rate_id' => $rate->id,
            'idempotency_key' => $idempotencyKey,
            'save_address' => '1',
            'is_default_address' => '1',
        ]);

        $responseCheckout->assertRedirect();

        // 2. Comprobar que se guardó en la tabla shop_customer_addresses
        $this->assertDatabaseHas('shop_customer_addresses', [
            'customer_id' => $customer->id,
            'recipient_name' => 'Familiar Receptor',
            'phone' => '+53 5333 4444',
            'address_line1' => 'Avenida 31 No. 4412 e/ 44 y 46',
            'province' => 'La Habana',
            'municipality' => 'Playa',
            'is_default' => 1,
        ]);

        // 3. Al volver a agregar un producto y entrar al checkout, la dirección guardada debe cargarse
        $this->postJson('/shop/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $responseCheckoutPage = $this->get('/shop/checkout');
        $responseCheckoutPage->assertStatus(200);
        $responseCheckoutPage->assertViewHas('savedAddresses', function ($addresses) use ($customer) {
            return $addresses->count() === 1 && $addresses->first()->customer_id === $customer->id;
        });
        $responseCheckoutPage->assertSee('Avenida 31 No. 4412 e/ 44 y 46');
        $responseCheckoutPage->assertSee('saved_address_selector');
    }
}

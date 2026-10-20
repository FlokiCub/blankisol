<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Shop\ShopCart;
use App\Models\Shop\ShopCartItem;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCustomer;
use App\Models\Shop\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopCustomerRegistrationAndAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la ruta /shop/cart no arroja "Undefined array key subtotal" cuando hay ítems.
     */
    public function test_cart_page_renders_properly_with_items_without_subtotal_error(): void
    {
        $category = ShopCategory::create([
            'name' => 'Categoría Test',
            'slug' => 'cat-test',
            'is_active' => true,
        ]);

        $product = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'SKU-CART-TEST',
            'slug' => 'producto-cart-test',
            'name' => 'Producto Cart Test',
            'price' => 250.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $sessionId = 'cart_test_session_123';
        $cart = ShopCart::create([
            'session_id' => $sessionId,
            'status' => 'active',
        ]);

        ShopCartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 250.00,
        ]);

        $response = $this->withSession(['_token' => 'dummy'])
            ->withHeaders(['X-Cart-Session' => $sessionId])
            ->get(route('shop.cart.index', ['cart_session' => $sessionId]));

        $response->assertStatus(200);
        $response->assertSee('Tu Carrito de Compras');
        $response->assertSee('500.00'); // 2 x 250
        $response->assertSee('USD');
    }

    /**
     * Verifica que la vista de registro se renderiza correctamente con el logotipo para fondos claros.
     */
    public function test_register_page_renders_with_light_logo_and_google_button(): void
    {
        $response = $this->get(route('shop.register'));

        $response->assertStatus(200);
        $response->assertSee('Crear Cuenta en BLANKISOL SHOP');
        $response->assertSee('favicon-light/apple-touch-icon.png');
        $response->assertSee('Continuar con Cuenta Gmail / Google');
        $response->assertSee('Persona Natural (B2C)');
        $response->assertSee('Empresa / Negocio (B2B)');
    }

    /**
     * Verifica que un nuevo cliente puede registrarse (B2C) y queda autenticado automáticamente.
     */
    public function test_new_customer_can_register_b2c_account(): void
    {
        $response = $this->post(route('shop.register.submit'), [
            'name' => 'Carlos Morales',
            'email' => 'carlos.morales@ejemplo.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+53 5 123 4567',
            'customer_type' => 'individual',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->assertAuthenticated();

        $user = User::where('email', 'carlos.morales@ejemplo.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::CLIENTE_TIENDA, $user->role);

        $customer = ShopCustomer::where('user_id', $user->id)->first();
        $this->assertNotNull($customer);
        $this->assertEquals('individual', $customer->customer_type);
        $this->assertEquals('Carlos', $customer->first_name);
        $this->assertEquals('Morales', $customer->last_name);
    }

    /**
     * Verifica que un cliente empresarial puede registrarse con datos B2B.
     */
    public function test_new_business_customer_can_register_b2b_account(): void
    {
        $response = $this->post(route('shop.register.submit'), [
            'name' => 'Empresa Soluciones S.A.',
            'email' => 'ventas@soluciones-cuba.cu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+53 7 888 9999',
            'customer_type' => 'business',
            'company_name' => 'Soluciones Caribe S.A.',
            'tax_id' => 'NIT-CU-123456',
            'province' => 'Matanzas',
            'municipality' => 'Cárdenas',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->assertAuthenticated();

        $user = User::where('email', 'ventas@soluciones-cuba.cu')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::CLIENTE_TIENDA, $user->role);

        $customer = ShopCustomer::where('user_id', $user->id)->first();
        $this->assertNotNull($customer);
        $this->assertEquals('business', $customer->customer_type);
        $this->assertEquals('Soluciones Caribe S.A.', $customer->company_name);
        $this->assertEquals('NIT-CU-123456', $customer->tax_id);
    }

    /**
     * Verifica el flujo de solicitud de recuperación de contraseña.
     */
    public function test_forgot_password_flow_generates_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente.olvido@ejemplo.com',
            'role' => UserRole::CLIENTE_TIENDA,
        ]);

        $response = $this->get(route('shop.forgot-password'));
        $response->assertStatus(200);
        $response->assertSee('¿Olvidaste tu Contraseña?');

        $submitResponse = $this->post(route('shop.forgot-password.submit'), [
            'email' => 'cliente.olvido@ejemplo.com',
        ]);

        $submitResponse->assertSessionHas('status');
        $submitResponse->assertSessionHas('reset_link');

        $record = DB::table('password_reset_tokens')->where('email', 'cliente.olvido@ejemplo.com')->first();
        $this->assertNotNull($record);
    }

    /**
     * Verifica que un usuario puede definir su nueva contraseña con el token generado.
     */
    public function test_reset_password_updates_password(): void
    {
        $user = User::factory()->create([
            'email' => 'reset.test@ejemplo.com',
            'password' => Hash::make('OldPassword123!'),
            'role' => UserRole::CLIENTE_TIENDA,
        ]);

        $token = Str::random(64);
        DB::table('password_reset_tokens')->insert([
            'email' => 'reset.test@ejemplo.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $viewResponse = $this->get(route('shop.reset-password', [
            'token' => $token,
            'email' => 'reset.test@ejemplo.com',
        ]));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Establecer Nueva Contraseña');

        $response = $this->post(route('shop.reset-password.submit'), [
            'token' => $token,
            'email' => 'reset.test@ejemplo.com',
            'password' => 'NewStrongPassword123!',
            'password_confirmation' => 'NewStrongPassword123!',
        ]);

        $response->assertRedirect(route('shop.login'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('NewStrongPassword123!', $user->password));

        // Token debe ser eliminado
        $this->assertNull(DB::table('password_reset_tokens')->where('email', 'reset.test@ejemplo.com')->first());
    }

    /**
     * Verifica la redirección oficial a Google OAuth y el procesamiento del callback con creación de cliente.
     */
    public function test_google_auth_creates_or_authenticates_customer(): void
    {
        // 1. Redirección oficial hacia Google Accounts con el Client ID registrado
        $responseRedirect = $this->get(route('shop.auth.google'));
        $targetUrl = $responseRedirect->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com/o/oauth2/auth', $targetUrl);
        $this->assertStringContainsString('1074001310916-mm3dd86d8e0i4febre0kjui8laptplfu.apps.googleusercontent.com', $targetUrl);

        // 2. Simular retorno exitoso de Google en el callback
        $abstractUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_uid_99887766');
        $abstractUser->shouldReceive('getName')->andReturn('Alejandro Google');
        $abstractUser->shouldReceive('getEmail')->andReturn('alejandro.oauth@gmail.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar123');

        $provider = \Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($abstractUser);

        \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $responseCallback = $this->get(route('shop.auth.google.callback'));
        $responseCallback->assertRedirect(route('shop.index'));

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertEquals('alejandro.oauth@gmail.com', $user->email);
        $this->assertEquals(UserRole::CLIENTE_TIENDA, $user->role);
        $this->assertEquals('google_uid_99887766', $user->google_id);

        // Verificar que se creó su perfil ShopCustomer
        $this->assertDatabaseHas('shop_customers', [
            'user_id' => $user->id,
            'email' => 'alejandro.oauth@gmail.com',
            'first_name' => 'Alejandro',
        ]);
    }
}

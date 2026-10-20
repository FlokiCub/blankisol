<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShopAuthAndLocationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la vista clara del login comercial (/shop/login) carga correctamente.
     */
    public function test_shop_login_page_renders_with_clean_theme(): void
    {
        $response = $this->get(route('shop.login'));

        $response->assertStatus(200);
        $response->assertViewIs('shop.auth.login');
        $response->assertSee('Iniciar Sesión');
        $response->assertSee('BLANKISOL');
        $response->assertSee('SHOP');
        $response->assertSee('favicon-light/apple-touch-icon.png');
    }

    /**
     * Verifica que si el admin de tienda se autentica desde el login general /login es redirigido a shop.admin.dashboard.
     */
    public function test_shop_admin_logging_in_via_main_login_is_redirected_to_shop_admin(): void
    {
        $shopAdmin = User::factory()->create([
            'email' => 'admin.shop@blankisol.com',
            'username' => 'admin_shop',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'login' => 'admin_shop',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('shop.admin.dashboard'));
        $this->assertAuthenticatedAs($shopAdmin);
    }

    /**
     * Verifica que si un admin de tienda accede a /dashboard es redirigido inmediatamente a /shop/admin.
     */
    public function test_shop_admin_accessing_user_dashboard_is_redirected_to_shop_admin(): void
    {
        $shopAdmin = User::factory()->create([
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        $response = $this->actingAs($shopAdmin)->get(route('dashboard'));

        $response->assertRedirect(route('shop.admin.dashboard'));
    }

    /**
     * Verifica que el usuario administrador de tienda puede autenticarse y es redirigido a /shop/admin.
     */
    public function test_shop_admin_user_can_login_and_is_redirected_to_shop_admin_dashboard(): void
    {
        $shopAdmin = User::factory()->create([
            'email' => 'admin.shop@blankisol.com',
            'username' => 'admin_shop',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        $response = $this->post(route('shop.login.submit'), [
            'login' => 'admin.shop@blankisol.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('shop.admin.dashboard'));
        $this->assertAuthenticatedAs($shopAdmin);
    }

    /**
     * Verifica que un usuario normal que inicia sesión es redirigido a la tienda pública /shop.
     */
    public function test_regular_user_login_redirects_to_storefront(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente@ejemplo.com',
            'username' => 'cliente123',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::ADUANA, // O rol no directivo/tienda
            'is_active' => true,
        ]);

        $response = $this->post(route('shop.login.submit'), [
            'login' => 'cliente@ejemplo.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('shop.index'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Verifica que el intento de acceder a /shop/admin sin autenticación redirige al login claro de la tienda.
     */
    public function test_unauthenticated_access_to_shop_admin_redirects_to_shop_login(): void
    {
        $response = $this->get('/shop/admin');

        $response->assertRedirect(route('shop.login'));
    }

    /**
     * Verifica el logout de la tienda comercial.
     */
    public function test_shop_logout_invalidates_session_and_redirects(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMINISTRADOR_TIENDA,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('shop.logout'));

        $response->assertRedirect(route('shop.login'));
        $this->assertGuest();
    }

    /**
     * Verifica que los productos del catálogo se visualizan independientemente de la ubicación definida.
     */
    public function test_catalog_displays_all_products_regardless_of_province_or_municipality(): void
    {
        $category = ShopCategory::create([
            'name' => 'Movilidad Eléctrica',
            'slug' => 'movilidad-electrica',
            'is_active' => true,
        ]);

        $product1 = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'TEST-PROD-01',
            'slug' => 'moto-electrica-test',
            'name' => 'Moto Eléctrica Test',
            'price' => 1200.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $product2 = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'TEST-PROD-02',
            'slug' => 'panel-solar-test',
            'name' => 'Panel Solar Test',
            'price' => 200.00,
            'inventory_type' => 'on_demand',
            'stock_quantity' => 0,
            'is_active' => true,
        ]);

        // Consulta pasando provincia y municipio en query: no debe filtrar los productos
        $response = $this->get(route('shop.products.index', [
            'province' => 'Santiago de Cuba',
            'municipality' => 'Palma Soriano',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Moto Eléctrica Test');
        $response->assertSee('Panel Solar Test');
    }
}

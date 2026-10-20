<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopStorefrontTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la portada pública de la tienda (/shop) carga con categorías y productos destacados.
     */
    public function test_storefront_home_page_loads_with_featured_and_categories(): void
    {
        $category = ShopCategory::create([
            'name' => 'Movilidad Eléctrica',
            'slug' => 'movilidad-electrica',
            'icon' => 'fa-solid fa-motorcycle',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $featured = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'MOTO-PRO-01',
            'slug' => 'moto-electrica-blankisol-pro',
            'name' => 'Moto Eléctrica Pro 72V',
            'price' => 1350.00,
            'sale_price' => 1200.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 5,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $onDemand = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'GEN-IND-50K',
            'slug' => 'generador-industrial-50kva',
            'name' => 'Generador Industrial 50kVA',
            'price' => 8900.00,
            'inventory_type' => 'on_demand',
            'stock_quantity' => 0,
            'estimated_delivery_days' => '25 días hábiles',
            'is_active' => true,
        ]);

        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertViewIs('shop.storefront.index');
        $response->assertSee('BLANKISOL SHOP');
        $response->assertSee('Movilidad Eléctrica');
        $response->assertSee('Moto Eléctrica Pro 72V');
        $response->assertSee('$1,200.00'); // Precio de oferta en USD
        $response->assertSee('Generador Industrial 50kVA');
    }

    /**
     * Verifica que el catálogo (/shop/products) filtra por categoría, término y modalidad de inventario.
     */
    public function test_catalog_filters_by_category_and_inventory_type(): void
    {
        $catA = ShopCategory::create(['name' => 'Energía Solar', 'slug' => 'energia-solar', 'is_active' => true]);
        $catB = ShopCategory::create(['name' => 'Herramientas', 'slug' => 'herramientas', 'is_active' => true]);

        $solarPanel = ShopProduct::create([
            'category_id' => $catA->id,
            'sku' => 'PANEL-550W',
            'slug' => 'panel-solar-monocristalino-550w',
            'name' => 'Panel Solar Monocristalino 550W',
            'price' => 180.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 50,
            'is_active' => true,
        ]);

        $toolOnDemand = ShopProduct::create([
            'category_id' => $catB->id,
            'sku' => 'TAL-IND-20V',
            'slug' => 'taladro-industrial-percutor',
            'name' => 'Taladro Percutor Industrial',
            'price' => 320.00,
            'inventory_type' => 'on_demand',
            'stock_quantity' => 0,
            'is_active' => true,
        ]);

        // 1. Catálogo completo
        $responseAll = $this->get('/shop/products');
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Panel Solar Monocristalino 550W');
        $responseAll->assertSee('Taladro Percutor Industrial');

        // 2. Filtro por categoría energía solar
        $responseCat = $this->get('/shop/products?category=energia-solar');
        $responseCat->assertStatus(200);
        $responseCat->assertSee('Panel Solar Monocristalino 550W');
        $responseCat->assertDontSee('Taladro Percutor Industrial');

        // 3. Filtro por modalidad on_demand
        $responseOnDemand = $this->get('/shop/products?inventory_type=on_demand');
        $responseOnDemand->assertStatus(200);
        $responseOnDemand->assertSee('Taladro Percutor Industrial');
        $responseOnDemand->assertDontSee('Panel Solar Monocristalino 550W');

        // 4. Búsqueda por texto
        $responseSearch = $this->get('/shop/products?search=PANEL');
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Panel Solar Monocristalino 550W');
        $responseSearch->assertDontSee('Taladro Percutor Industrial');
    }

    /**
     * Verifica que la ruta de categoría (/shop/categories/{slug}) muestra exclusivamente sus productos.
     */
    public function test_category_page_displays_matching_products(): void
    {
        $category = ShopCategory::create(['name' => 'Maquinaria', 'slug' => 'maquinaria', 'is_active' => true]);

        $product = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'MONT-CAR-3T',
            'slug' => 'montacargas-diesel-3-toneladas',
            'name' => 'Montacargas Diesel 3T',
            'price' => 14500.00,
            'inventory_type' => 'on_demand',
            'is_active' => true,
        ]);

        $response = $this->get('/shop/categories/maquinaria');
        $response->assertStatus(200);
        $response->assertViewIs('shop.storefront.category');
        $response->assertSee('Montacargas Diesel 3T');
        $response->assertSee('$14,500.00');
    }

    /**
     * Verifica que la ficha de detalle (/shop/products/{slug}) muestra especificaciones completas.
     */
    public function test_product_detail_page_loads_with_specs_and_related_products(): void
    {
        $category = ShopCategory::create(['name' => 'Movilidad', 'slug' => 'movilidad', 'is_active' => true]);

        $product = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'SCOOTER-E-2026',
            'slug' => 'scooter-electrico-urbano',
            'name' => 'Scooter Eléctrico Urbano 500W',
            'price' => 450.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 12,
            'weight_kg' => 14.5,
            'dimensions' => '110 x 45 x 115',
            'description' => 'Chasis de aluminio aeroespacial con freno de disco regenerativo.',
            'is_active' => true,
        ]);

        $related = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'CASCO-SEG-01',
            'slug' => 'casco-seguridad-scooter',
            'name' => 'Casco Certificado para Scooter',
            'price' => 35.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 30,
            'is_active' => true,
        ]);

        $response = $this->get('/shop/products/scooter-electrico-urbano');

        $response->assertStatus(200);
        $response->assertViewIs('shop.storefront.show');
        $response->assertSee('Scooter Eléctrico Urbano 500W');
        $response->assertSee('SCOOTER-E-2026');
        $response->assertSee('$450.00');
        $response->assertSee('14.50 kg');
        $response->assertSee('Chasis de aluminio aeroespacial');
        $response->assertSee('Casco Certificado para Scooter'); // Producto relacionado
    }

    /**
     * Verifica que un producto inactivo devuelve HTTP 404 para proteger el catálogo público.
     */
    public function test_inactive_product_returns_404(): void
    {
        $product = ShopProduct::create([
            'sku' => 'DRAFT-001',
            'slug' => 'producto-borrador',
            'name' => 'Producto Borrador',
            'price' => 50.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 0,
            'is_active' => false, // Inactivo
        ]);

        $response = $this->get('/shop/products/producto-borrador');
        $response->assertStatus(404);
    }
}

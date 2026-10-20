<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Shop\ShopCategory;
use App\Models\Shop\ShopCoupon;
use App\Models\Shop\ShopProduct;
use App\Models\Shop\ShopProductVariant;
use App\Models\Shop\ShopShippingRate;
use App\Models\Shop\ShopShippingZone;
use App\Services\Shop\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ShopPricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PricingService();
    }

    public function test_calculate_item_with_regular_price_and_sale_price(): void
    {
        $category = ShopCategory::create(['name' => 'Solar', 'slug' => 'solar', 'is_active' => true]);

        // 1. Regular price
        $productRegular = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'PANEL-300W',
            'slug' => 'panel-300w',
            'name' => 'Panel Solar 300W',
            'price' => 150.00,
            'cost_price' => 95.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $lineRegular = $this->service->calculateItem($productRegular, 3);
        $this->assertEquals(150.00, $lineRegular['unit_price']);
        $this->assertEquals(450.00, $lineRegular['total_price']);
        $this->assertEquals(285.00, $lineRegular['total_cost']);

        // 2. Sale price
        $productSale = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'PANEL-500W',
            'slug' => 'panel-500w',
            'name' => 'Panel Solar 500W Promo',
            'price' => 220.00,
            'sale_price' => 195.00,
            'cost_price' => 130.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $lineSale = $this->service->calculateItem($productSale, 2);
        $this->assertEquals(195.00, $lineSale['unit_price']);
        $this->assertEquals(390.00, $lineSale['total_price']);
    }

    public function test_calculate_item_with_variant_override(): void
    {
        $category = ShopCategory::create(['name' => 'Motos', 'slug' => 'motos', 'is_active' => true]);

        $product = ShopProduct::create([
            'category_id' => $category->id,
            'sku' => 'MOTO-BASE',
            'slug' => 'moto-base',
            'name' => 'Moto Eléctrica Base',
            'price' => 1100.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $variantLithium = ShopProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'MOTO-BASE-LITHIUM-72V',
            'name' => 'Batería Litio 72V 45Ah',
            'price' => 1450.00,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $lineVariant = $this->service->calculateItem($product, 1, $variantLithium);
        $this->assertEquals(1450.00, $lineVariant['unit_price']);
        $this->assertEquals('MOTO-BASE-LITHIUM-72V', $lineVariant['sku']);
    }

    public function test_calculate_item_rejects_non_positive_quantity(): void
    {
        $product = ShopProduct::create([
            'sku' => 'TEST-01',
            'slug' => 'test-01',
            'name' => 'Test',
            'price' => 50.00,
            'inventory_type' => 'controlled_stock',
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->calculateItem($product, 0);
    }

    public function test_coupon_validation_and_discount_rules(): void
    {
        // 1. Cupón inactivo
        $inactiveCoupon = ShopCoupon::create([
            'code' => 'INACTIVO',
            'name' => 'Inactivo',
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'is_active' => false,
        ]);
        $resInactive = $this->service->applyCoupon($inactiveCoupon, 200.00);
        $this->assertFalse($resInactive['applied']);

        // 2. Cupón con mínimo de compra
        $minCoupon = ShopCoupon::create([
            'code' => 'MINIMO500',
            'name' => 'Rebaja $50 con compra $500',
            'discount_type' => 'fixed',
            'discount_value' => 50.00,
            'min_order_amount' => 500.00,
            'is_active' => true,
        ]);
        $resUnderMin = $this->service->applyCoupon($minCoupon, 350.00);
        $this->assertFalse($resUnderMin['applied']);

        $resOverMin = $this->service->applyCoupon($minCoupon, 600.00);
        $this->assertTrue($resOverMin['applied']);
        $this->assertEquals(50.00, $resOverMin['discount_amount']);

        // 3. Cupón porcentual con tope máximo
        $percentCapCoupon = ShopCoupon::create([
            'code' => 'PROMO20PCT',
            'name' => '20% hasta $80 max',
            'discount_type' => 'percentage',
            'discount_value' => 20.00,
            'max_discount_amount' => 80.00,
            'is_active' => true,
        ]);
        // 20% de 300 = 60 (< 80)
        $resBelowCap = $this->service->applyCoupon($percentCapCoupon, 300.00);
        $this->assertEquals(60.00, $resBelowCap['discount_amount']);

        // 20% de 1000 = 200 (> 80 tope max) -> 80.00
        $resOverCap = $this->service->applyCoupon($percentCapCoupon, 1000.00);
        $this->assertEquals(80.00, $resOverCap['discount_amount']);
    }

    public function test_calculate_order_totals_full_pipeline(): void
    {
        $zone = ShopShippingZone::create(['name' => 'Occidente', 'is_active' => true]);
        $shippingRate = ShopShippingRate::create([
            'zone_id' => $zone->id,
            'name' => 'Entrega Habana / Artemisa',
            'price' => 45.00,
            'is_active' => true,
        ]);

        $coupon = ShopCoupon::create([
            'code' => 'BIENVENIDA',
            'name' => 'Bienvenida $25 USD',
            'discount_type' => 'fixed',
            'discount_value' => 25.00,
            'min_order_amount' => 100.00,
            'is_active' => true,
        ]);

        $item1 = [
            'unit_price' => 150.00,
            'unit_cost' => 100.00,
            'total_price' => 300.00, // 2 x 150
            'total_cost' => 200.00,
        ];

        $item2 = [
            'unit_price' => 80.00,
            'unit_cost' => 50.00,
            'total_price' => 80.00, // 1 x 80
            'total_cost' => 50.00,
        ];

        $totals = $this->service->calculateOrderTotals(
            [$item1, $item2],
            $coupon,
            $shippingRate,
            0.0 // sin impuestos adicionales
        );

        // Subtotal = 300 + 80 = 380.00
        $this->assertEquals(380.00, $totals['subtotal']);
        // Descuento = 25.00
        $this->assertEquals(25.00, $totals['discount_amount']);
        $this->assertTrue($totals['coupon_applied']);
        // Envío = 45.00
        $this->assertEquals(45.00, $totals['shipping_fee']);
        // Total = 380 - 25 + 45 = 400.00 USD
        $this->assertEquals(400.00, $totals['total']);
        // Margen estimado = 380 - 250 = 130.00 USD
        $this->assertEquals(130.00, $totals['estimated_gross_profit']);
    }
}

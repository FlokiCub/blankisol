<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('shop.*', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('shop_categories')) {
                    $categories = \App\Models\Shop\ShopCategory::where('is_active', true)
                        ->whereNull('parent_id')
                        ->orderBy('sort_order', 'asc')
                        ->take(8)
                        ->get();
                    $view->with('shopNavCategories', $categories);
                }
            } catch (\Throwable $e) {
                // Silently fallback if database is migrating
            }
        });
    }
}

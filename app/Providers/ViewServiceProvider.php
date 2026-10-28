<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $sharedComposer = function ($view) {
            $pendingPOCount = 0;
            $pendingTaxReviewCount = 0;
            
            if (Auth::check() && Schema::hasTable('purchase_orders')) {
                try {
                    $pendingPOCount = \App\Models\PurchaseOrder::where('status', 'pending')->count();
                    $pendingTaxReviewCount = \App\Models\PurchaseOrder::where('tax_review_status', 'pending')->count();
                } catch (\Exception $e) {
                    $pendingPOCount = 0;
                    $pendingTaxReviewCount = 0;
                }
            }
            
            $view->with('pendingPOCount', $pendingPOCount)
                 ->with('pendingTaxReviewCount', $pendingTaxReviewCount);
        };

        View::composer('layouts.app', $sharedComposer);
        View::composer('layouts.partials.sidebar', $sharedComposer);
        View::composer('layouts.partials.navbar', $sharedComposer);
    }
}

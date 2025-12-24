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
        // Share global data with all views
        View::composer('layouts.app', function ($view) {
            $pendingPOCount = 0;
            
            // Only calculate if user is authenticated and table exists
            if (Auth::check() && Schema::hasTable('purchase_orders')) {
                try {
                    $pendingPOCount = \App\Models\PurchaseOrder::where('status', 'pending')->count();
                } catch (\Exception $e) {
                    $pendingPOCount = 0;
                }
            }
            
            $view->with('pendingPOCount', $pendingPOCount);
        });

        // Share with sidebar partial
        View::composer('layouts.partials.sidebar', function ($view) {
            $pendingPOCount = 0;
            
            if (Auth::check() && Schema::hasTable('purchase_orders')) {
                try {
                    $pendingPOCount = \App\Models\PurchaseOrder::where('status', 'pending')->count();
                } catch (\Exception $e) {
                    $pendingPOCount = 0;
                }
            }
            
            $view->with('pendingPOCount', $pendingPOCount);
        });

        // Share with navbar partial
        View::composer('layouts.partials.navbar', function ($view) {
            $pendingPOCount = 0;
            
            if (Auth::check() && Schema::hasTable('purchase_orders')) {
                try {
                    $pendingPOCount = \App\Models\PurchaseOrder::where('status', 'pending')->count();
                } catch (\Exception $e) {
                    $pendingPOCount = 0;
                }
            }
            
            $view->with('pendingPOCount', $pendingPOCount);
        });
    }
}

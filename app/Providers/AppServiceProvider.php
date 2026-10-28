<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ProductRepositoryInterface::class,
            \App\Repositories\Eloquent\ProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\VendorRepositoryInterface::class,
            \App\Repositories\Eloquent\VendorRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\BranchRepositoryInterface::class,
            \App\Repositories\Eloquent\BranchRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PaymentTermRepositoryInterface::class,
            \App\Repositories\Eloquent\PaymentTermRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\DepartmentRepositoryInterface::class,
            \App\Repositories\Eloquent\DepartmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Admin superuser bypass for all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });
    }
}

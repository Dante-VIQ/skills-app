<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
            // Role check directive
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });
        
        // Permission check directive
        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->can($permission);
        });
        
        // Any role directive
        Blade::if('anyrole', function ($roles) {
            if (!auth()->check()) return false;
            
            $rolesArray = is_array($roles) ? $roles : explode(',', $roles);
            return auth()->user()->hasAnyRole($rolesArray);
        });
        
        // All roles directive
        Blade::if('allroles', function ($roles) {
            if (!auth()->check()) return false;
            
            $rolesArray = is_array($roles) ? $roles : explode(',', $roles);
            return auth()->user()->hasAllRoles($rolesArray);
        });
    }
}

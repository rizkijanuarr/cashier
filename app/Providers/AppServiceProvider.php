<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        \Illuminate\Database\Eloquent\Model::unguard();
        // Blade::component('components.index', 'table');          // INDEX
        // Blade::component('components.thead', 'table.thead');   // Untuk thead
        // Blade::component('components.tr', 'table.tr');         // Untuk tr
        // Blade::component('components.th', 'table.th');         // Untuk th
        // Blade::component('components.td', 'table.td');         // Untuk td
    }
}

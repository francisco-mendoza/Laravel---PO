<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use App\Models\Role;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bladeDirectives();
    }

    /**
     * Register the blade directives
     * @param gerente
     * @return void
     */
    private function bladeDirectives()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}

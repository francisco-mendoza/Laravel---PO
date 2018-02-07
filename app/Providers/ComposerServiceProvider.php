<?php

namespace App\Providers;

//use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /** Asignamos la vista y el Composer que trae los datos */
        View::composer(
            ['layout.principal'],'App\Http\ViewComposers\MenuOptions'
        );

        View::composer(
            ['home.index'],'App\Http\ViewComposers\Home'
        );
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

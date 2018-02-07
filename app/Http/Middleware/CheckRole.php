<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MenuOption;
use App\Models\MenuOptionsRole;
use Illuminate\Foundation\Auth\User;
use Auth;
use Session;
use Entrust;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $menu_option)
    {
        $error = false;
        $menu_option_role = false;
        $mensaje = "";
        $user_role = Auth::user()->roles();

        $rol = MenuOption::where("option_route","=",$menu_option)->first();
        
        if($rol == null){
            $mensaje = "La página que está buscando no existe";
            Session::set('opcion_menu', null);
            Session::flash('error_message', $mensaje);
            return redirect(route('home'));
        }

        $menu_option_role = Entrust::can('ver_'.$menu_option);


        if(count($user_role) == 0){
            $error = true;
            $mensaje = "No tienes ningún rol asignado, comunicate con un administrador";
        }

        if(!$menu_option_role){
            $error = true;
            $mensaje = "No tienes permiso para ver esa área";
            Session::set('opcion_menu', null);
        }

        if($error){
            Session::flash('error_message', $mensaje);
            return redirect('/');
        }

        //Asignar opción de navegación
        Session::set('opcion_menu', $rol->name_option);

        return $next($request);
    }
}

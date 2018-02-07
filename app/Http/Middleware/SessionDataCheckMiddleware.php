<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 17-01-17
 * Time: 9:54
 */
namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;
use Illuminate\Contracts\Auth\Guard;

class SessionDataCheckMiddleware {

    /**
     * Check session data, if role is not valid logout the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }


    public function handle($request, Closure $next) {

        $tiempo_expiracion = 3600; //Segundos
        $tiempo_actual = time();

        //Intento traer mi variable de sesion, si no existe aun la dejo igual al tiempo_actual
        $tiempo_login = Session::get('tiempo_login');
        if($tiempo_login == null){
            $tiempo_login = $tiempo_actual;
        }

        //Saco los segundos que han pasado entre la variable de sesion y mi tiempo actual
        $tiempo_diferencia = $tiempo_actual - $tiempo_login;

        //Si el tiempo de diferencia es mayor o igual boto la session y las sesiones existentes
        if($tiempo_diferencia >= $tiempo_expiracion){
            $request->session()->flush(); // Borro todas las sessiones
            Auth::logout();
            return redirect('/login');
        }else{
            //Si no actualizo mi variable de sesion por el tiempo actual
            Session::set('tiempo_login',$tiempo_actual);
        }


        return $next($request);
    }

}
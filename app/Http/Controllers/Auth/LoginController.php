<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use League\Flysystem\Exception;
use Validator;
use Illuminate\Http\Request;
use Session;
use Toastr;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redireccion despues de logear
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function index()
    {
        return view('login.index');
    }

    /**
     * Metodo que llama a Google
     *
     * @return Response
     */
    public function redirectToProvider(Request $request)
    {
        if(isset($_GET['url'])){
            Session::set('url_redirect',$_GET['url']);
        }
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback que trae info del usuario de google
     * @return Response
     */
    public function handleProviderCallback(Request $request)
    {
        try{
            //Traemos el Usuario desde Google
            if (!$request->has('code') || $request->has('denied')) {
                Session::flash('error_message', 'Debes permitir el acceso a tu cuenta de Google.');
                return redirect('/login');
            }else{
                $socialUser = Socialite::driver('google')->user();
            }

            $splitEmail = explode('@',$socialUser->email);

            //Saco el el dominio
            $dominio = $splitEmail[1];
            $dominios_autorizados = config('constants.dominiosAutorizados');
            //Verifico si el dominio no esta en mi array de dominios autorizados
            if(!in_array($dominio, $dominios_autorizados)){
                //Faltaria agregar un error
                Session::flash('error_message', 'Dominio no autorizado.');
                return Redirect('/login');
            }
        }catch (Exception $exception){
            $error = $exception->getMessage();
            Session::flash('error_message', 'Error de servidor');
            return Redirect('/');
        }

        $authUser = $this->findOrCreateUser($socialUser, 'google');
        if($authUser){
            $request->session()->put('avatar_user', $socialUser->getAvatar());
            Auth::login($authUser, true);
        }else{
            $mensaje = "No existe el usuario seleccionado, favor hablar con su gerente de área.";
            Session::flash('error_message', $mensaje);
            return redirect('/login');
        }

        $url_redirect = Session::get('url_redirect') != ""?Session::get('url_redirect'):$this->redirectTo;

        return redirect($url_redirect);
    }

    public function findOrCreateUser($user, $provider)
    {
        //Nos traemos al usuario por su id de google,
        //si devuelve un usuario significa que ya se ha logeado
        $authUser = User::where('social_provider_id', $user->id)->first();

        //Nos traemos el usuario de la BD por su email
        $authUserEmail = User::where('email', $user->email)->first();

        //Sacamos datos de la cuenta google del usuario
        $name = $user->user['name']['givenName'];
        $first_name = explode(' ',$name);
        $familyname = $user->user['name']['familyName'];
        $first_familyname = explode(' ',$familyname);
        $username = strtolower($first_name[0]).'.'.strtolower($first_familyname[0]);

        if ($authUser) { // Si el usuario ya está registrado y ya ha iniciado con Google
            $authUser->url_avatar =$user->avatar;
            $authUser->save();
            return $authUser;
        }elseif ($authUserEmail){ // Si el usuario está registrado pero no ha iniciado con Google
            $authUserEmail->social_provider    = 'google';
            $authUserEmail->social_provider_id = $user->id;
            $authUserEmail->username           = $username;
            $authUserEmail->url_avatar         = $user->avatar;
            return $authUserEmail;
        }else{
            // Si el usuario no está registrado en la BD
            return false;
        }
    }

    public function logout(){
        if (\Auth::check()) \Auth::logout();
        Session::flush();
        return redirect('/login');
    }
}

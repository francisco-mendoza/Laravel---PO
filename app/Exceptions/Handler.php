<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Session;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if(method_exists( $exception, 'getStatusCode') && $exception->getStatusCode()==403){ //El middleware de Entrust detectó que no tiene los permisos necesarios
            $message = config('messages.seccionRestricted');
            $path= '/';

            switch($request->method()){

                case 'GET':
                    $action = $request->path();

                    if(strpos( $action, 'create' ) !== false || strpos( $action, 'edit' ) ){
                        $path = $request->headers->get('referer');
                        $message = config('messages.actionRestricted');
                    }

                    if(strpos( $action, 'deletePurchaseOrder' ) !== false){
                        $response['exception'] = get_class($exception);
                        $response['message'] = config('messages.actionRestricted');
                        $response['trace'] = $exception->getTrace();
                        return response()->json($response,403); //Forbidden
                    }

                    break;

                case 'DELETE':
                    if($request->ajax()){
                        $response['exception'] = get_class($exception);
                        $response['message'] = config('messages.actionRestricted');
                        $response['trace'] = $exception->getTrace();
                        return response()->json($response,403); //Forbidden
                    }
                    break;
                case 'PUT':
                case 'POST':
                case 'PATCH':
                default:
                    $message = config('messages.seccionRestricted');
                    break;
            }

            Session::set('opcion_menu', null);
            Session::flash('error_message', $message);
            return redirect()->guest($path);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }
}

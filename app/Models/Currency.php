<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use App\Models\CurrencyRate;
use Mockery\Exception;
use OwenIt\Auditing\Auditable;

/**
 * App\Models\Currency
 *
 * @mixin \Eloquent
 * @property int $id_currency
 * @property string $name_currency
 * @property string $short_name
 * @property string $code
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereIdCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereNameCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Currency whereCode($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
class Currency extends RossModel
{
    use Auditable;
    
    protected $table = 'currency';
    protected $primaryKey = 'id_currency';
    protected $fillable = [
        'id_currency',
        'name_currency',
        'short_name',
        'code',
    ];

    public $timestamps = false;

    const PESO_CHILENO = 2;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function getCurrencies($start, $len, $search, $column, $dir)
    {
        $currencies = DB::table('currency')->distinct();

        if($start!== null && $len !== null){
            $currencies->skip($start)->limit($len);
        }

        if($search !== null){
            $currencies->where('name_currency','like', '%'.$search.'%');
            $currencies->orWhere('short_name','like', '%'.$search.'%');
            $currencies->orWhere('code','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            $currencies->orderby('name_currency', $dir);
        }else{
            $currencies->orderby('id_currency', 'asc');
        }

        return $currencies->get();
    }
    public static function getCountCurrency(){
        return Currency::all()->count();
    }

    public static function apiConversorMoneda($moneda_origen,$moneda_destino,$cantidad) {

        $valor = 0;
        if($moneda_origen == 'UF' || $moneda_origen == 'uf'){
            $get = file_get_contents('http://mindicador.cl/api');
            $decode = json_decode($get);
            $valor_uf = $decode->uf->valor;
            $valor = $cantidad * $valor_uf;
        }else if($cantidad != 0){

            // Url servicio de Google para conversion
            $url = "https://finance.google.com/finance/converter";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);

            //Vemos si hay un redireccionamiento en la url
            $redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

            $error_message = "";
            $error_title = "Sistema Ordenes de Compra ROSS - Error Currency Service";
            $error = false;

            //Verificamos que la URL devuelva algo
            if(!curl_getinfo($ch,CURLINFO_CONTENT_TYPE)){
                $error_message = "Ha ocurrido un problema con la URL del servicio de conversion de Google";
                $error = true;
            }else if($redirect_url and $redirect_url != $url) {
                //Si existe un redireccionamiento y si el redirecionamiento es distinta a la url
                $error_message = "La URL del servicio de conversion de Google ha cambiado";
                $error = true;
            }
            curl_close($ch);

            //Si hay algún error, mandamos un correo a sistemas
            if($error){
                mail(Config::get('app.email_support'), $error_title, $error_message);
                return false;
            }else{
                $get = file_get_contents("$url?a=$cantidad&from=$moneda_origen&to=$moneda_destino");
                $get = explode("<span class=bld>",$get);
                $get = explode("</span>",$get[1]);
                $valor = preg_replace("/[^0-9\.]/", null, $get[0]);
            }

        }

        return $valor;
    }

    public static function conversorMoneda($moneda_origen,$moneda_destino,$cantidad,$date=null) {
        //Obtenemos el valor unitario en pesos chilenos de moneda_origen
        if(is_null($date)) {
            $valor_moneda_origen = CurrencyRate::getLastCurrencyRate($moneda_origen)->exchange_rate;
        } else {
            $valor_moneda_origen = CurrencyRate::getCurrencyRateByDate($moneda_origen, $date)->exchange_rate;
        }

        //Si moneda_destino es Peso Chileno solo multiplicamos por cantidad moneda_origen
        if($moneda_destino == self::PESO_CHILENO) {
            $valor = $valor_moneda_origen * $cantidad;
        } else {
            //Sino, obtenemos el valor unitario en pesos chilenos de moneda_destino
            if(is_null($date)) {
                $valor_moneda_destino = CurrencyRate::getLastCurrencyRate($moneda_destino)->exchange_rate;
            } else {
                $valor_moneda_destino = CurrencyRate::getCurrencyRateByDate($moneda_destino,$date)->exchange_rate;
            }

            //Obtenemos el valor en la moneda destino
            $valor = $cantidad * $valor_moneda_origen / $valor_moneda_destino;
        }

        return $valor;
    }

}

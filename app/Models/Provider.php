<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\Provider
 *
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property int $id_provider
 * @property string $name_provider
 * @property string $business
 * @property string $rut
 * @property string $address
 * @property string $phone
 * @property string $contact_name
 * @property string $contact_area
 * @property string $contact_email
 * @property string $contact_phone
 * @property int $is_visible
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereIdProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereNameProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereBusiness($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereRut($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereIsVisible($value)
 * @property int $payment_conditions
 * @property int $payment_method
 * @property string $bank
 * @property string $type_account
 * @property int $number_account
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePaymentConditions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider wherePaymentMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereTypeAccount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Provider whereNumberAccount($value)
 */
class Provider extends RossModel
{
    use Auditable;

    protected $table = "provider";
    protected $primaryKey = 'id_provider';
    protected $fillable = [
        'id_provider',
        'name_provider',
        'business',
        'rut',
        'address',
        'phone',
        'contact_name',
        'contact_area',
        'contact_email',
        'contact_phone',
        'is_visible',
        'payment_conditions',
        'payment_method',
        'bank',
        'type_account',
        'number_account'
    ];

    const ContratoActivo = 1;
    const NAME_PROVIDER = 1;
    const RUT = 2;
    const ADDRESS = 3;

    
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];
    
    public static  function getProviders(){
        return DB::table('provider')->select('name_provider')->get();
    }

    public static  function getProvidersContract($id_area){
        return DB::table('contract')
            ->join('provider','contract.id_provider','=','provider.id_provider')
            ->join('account_contract','contract.id_contract','=','account_contract.id_contract')
            ->where('contract.is_active','=',self::ContratoActivo)
            ->where('account_contract.id_area','=',$id_area)
            ->whereExists(function($query) use ($id_area){
                $query->select(DB::raw(1))
                    ->from('areas_budget')
                    ->where('id_area', '=', $id_area);
            })
            ->select(DB::raw('CONCAT(name_provider,\' - \',contract_number,\' (\',description,\')\') AS nombre '),'contract.id_contract')
            ->distinct()
            ->get();
    }
    
    public static function getProviderByName($name){

        return DB::table('provider')->where('name_provider',$name)->first();
        
    }

    public static function findProviderJoined($id){

        return DB::table('provider')->leftJoin('payment_conditions', 'provider.payment_conditions', '=', 'payment_conditions.id_payment_conditions')
            ->leftJoin('payment_method', 'provider.payment_method', '=','payment_method.id_payment_method')
            ->where('provider.id_provider','=',$id)
            ->select('provider.*', 'payment_conditions.name_condition as name_condition', 'payment_method.name_method as name_method')
            ->first();

    }

    public static function getCountProviders(){

        return Provider::all()->count();
    }

    public static function providers($start, $len, $search, $column, $dir){



        $users =  DB::table('provider')
            ->select('id_provider' , 'name_provider', 'rut', 'address');

        if($start!== null && $len !== null){
            $users->skip($start)->limit($len);
        }

        if($search !== null){
            $users->where('id_provider','like', '%'.$search.'%')
                ->orWhere('name_provider','like','%'.$search.'%')
                ->orWhere('rut','like','%'.$search.'%')
                ->orWhere('address','like','%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::NAME_PROVIDER:
                    $users->orderBy('name_provider', $dir);
                    break;
                case self::RUT:
                    $users->orderBy('rut', $dir);
                    break;
                case self::ADDRESS:
                    $users->orderBy('address', $dir);
                    break;
                default:
                    $users->orderBy('name_provider', 'asc');
                    break;
            }
        }else{
            $users->orderBy('name_provider', 'asc');
        }

        return $users->get();

    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\PaymentMethod
 *
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @property int $id_payment_method
 * @property string $name_method
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentMethod whereIdPaymentMethod($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentMethod whereNameMethod($value)
 */
class PaymentMethod extends RossModel
{
    use Auditable;

    const NAME_METHOD=1;

    protected $table = 'payment_method';
    protected $primaryKey = 'id_payment_method';
    protected $fillable = [
        'id_payment_method',
        'name_method'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static  function getPaymentMethod(){

        return DB::table('payment_method')->pluck('name_method','id_payment_method');
    }

    public static function getCountPaymentMethods(){

        return PaymentMethod::all()->count();
    }

    public static function PaymentMethods($start, $len, $search, $column, $dir){



        $methods =  DB::table('payment_method')->distinct();

        if($start!== null && $len !== null){
            $methods->skip($start)->limit($len);
        }

        if($search !== null){
            $methods->where('name_method','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::NAME_METHOD:
                    $methods->orderBy('name_method', $dir);
                    break;
                default:
                    $methods->orderBy('name_method', 'asc');
                    break;
            }
        }else{
            $methods->orderBy('name_method', 'asc');
        }

        return $methods->get();
    }
}

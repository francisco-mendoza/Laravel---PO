<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\PaymentCondition
 *
 * @mixin \Eloquent
 * @property int $id_payment_conditions
 * @property string $name_condition
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentCondition whereIdPaymentConditions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PaymentCondition whereNameCondition($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
class PaymentCondition extends RossModel
{
    use Auditable;


    const NAME_CONDITION = 1;

    protected $primaryKey = 'id_payment_conditions';
    protected $fillable = [
        'id_payment_conditions',
        'name_condition'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static  function getPaymentConditions(){

        return DB::table('payment_conditions')->pluck('name_condition','id_payment_conditions');
    }

    public static function getCountPaymentConditions(){

        return PaymentCondition::all()->count();
    }

    public static function PaymentConditions($start, $len, $search, $column, $dir){



        $conditions =  DB::table('payment_conditions')->distinct();

        if($start!== null && $len !== null){
            $conditions->skip($start)->limit($len);
        }

        if($search !== null){
            $conditions->where('name_condition','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::NAME_CONDITION:
                    $conditions->orderBy('name_condition', $dir);
                    break;
                default:
                    $conditions->orderBy('name_condition', 'asc');
                    break;
            }
        }else{
            $conditions->orderBy('name_condition', 'asc');
        }

        return $conditions->get();
    }

}

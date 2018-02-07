<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\Contract
 *
 * @property int $id_contract
 * @property int $id_provider
 * @property string $start_date
 * @property string $end_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIdContract($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIdProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereStartDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereEndDate($value)
 * @mixin \Eloquent
 * @property string $contract_number
 * @property string $description
 * @property string $contract_area
 * @property int $is_active
 * @property string $contract_pdf
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Contract whereContractPdf($value)
 */
class Contract extends RossModel
{
    use Auditable;

    protected $table = 'contract';
    protected $primaryKey = 'id_contract';
    protected $fillable = [
        'id_contract',
        'id_provider',
        'contract_number',
        'description',
        'contract_area',
        'start_date',
        'end_date',
        'is_active',
        'contract_pdf'
    ];

    const CONTRACT_NUMBER = 1;
    const NAME_PROVIDER = 2;
    const END_DATE = 4;



    public $timestamps = false;


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function getCountContracts(){

        return Contract::all()->count();
    }

    public static function Contracts($start, $len, $search, $column, $dir){



        $contracts =  DB::table('contract')
            ->leftJoin('provider', 'contract.id_provider', '=', 'provider.id_provider')
            ->select('contract.id_contract' , 'contract.contract_number','provider.name_provider',  'contract.is_active', DB::raw("DATE_FORMAT(contract.end_date, '%d-%m-%Y') as end_date") , 'contract.contract_pdf');

        if($start!== null && $len !== null){
            $contracts->skip($start)->limit($len);
        }

        if($search !== null){
            $contracts->where('provider.name_provider','like', '%'.$search.'%')
                ->orWhere('contract.contract_number','like','%'.$search.'%')
                ->orWhere(DB::raw("DATE_FORMAT(contract.end_date, '%d-%m-%Y')"),'like','%'.$search.'%');
            
        }

        if($column!== null && $dir !== null){
            switch($column){
                case self::CONTRACT_NUMBER:
                    $contracts->orderBy('contract.contract_number', $dir);
                    break;
                case self::NAME_PROVIDER:
                    $contracts->orderBy('provider.name_provider', $dir);
                    break;
                case self::END_DATE:
                    $contracts->orderBy('contract.end_date', $dir);
                    break;
                default:
                    $contracts->orderBy('provider.name_provider', 'asc');
                    break;
            }
        }else{
            $contracts->orderBy('provider.name_provider', 'asc');
        }

        return $contracts->get();
    }

    public static function  getContractByProvider($name_provider, $contract_number){
        return DB::table('contract')
            ->join('provider','contract.id_provider','=','provider.id_provider')
            ->where('contract.contract_number','=',$contract_number)
            ->where('provider.name_provider','=',$name_provider)
            ->first();
    }
}

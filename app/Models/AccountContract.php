<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use DB;

use Illuminate\Support\Collection;

/**
 * App\Models\AccountContract
 *
 * @mixin \Eloquent
 * @property int $id_contract
 * @property int $id_area
 * @property string $account_code
 * @property string $account_year
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereAccountCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereAccountYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountContract whereIdContract($value)
 */

class AccountContract extends RossModel
{
    use Auditable;

    protected $primaryKey = 'id_area';

    protected $fillable = [
        'id_contract',
        'id_area',
        'account_code',
        'account_year',
    ];

    protected $table = 'account_contract';
    public $timestamps = false;

    public static function getAccount($id_contract, $year , $code){
        return AccountContract::where('id_contract','=', $id_contract)
            ->where('account_year', '=', $year)
            ->where('account_code', '=', $code)
            ->first();
    }

    public static function getCountAccount($id=null){

        $count = 0;

        if($id != null){
            $count = DB::table('account_contract')->where('id_area','=', $id)
                ->whereRaw(DB::raw(" cast(account_year as unsigned)  = YEAR(CURDATE() ) "))->count();
        }

        return $count;
    }

    public static function getAccountByContract($id=null, $search = null){

        $result = new Collection();

        if($id != null){
            $result = DB::table('account_contract')
                ->select('areas.long_name','account_contract.account_code','account_contract.account_year')
                ->join('areas','account_contract.id_area','=','areas.id_area')
                ->where('id_contract','=', $id)
                ->whereRaw(DB::raw(" CAST(account_year as unsigned)  = YEAR(CURDATE() ) "));
            if($search != null){

                $result->where(function($query) use ($search){
                    $query->where('account_year', 'like', '%'.$search.'%')
                        ->orWhere('account_code','like', '%'.$search.'%');
                });

            }

            $result = $result->get();
        }

        return $result;
    }



    public static function deleteAccountsByContract($id_contract){
        DB::table('account_contract')->where('id_contract', '=', $id_contract)->delete();
    }

    public static function getAccountByAreaAndContract($id_contract, $id_area){
        return AccountContract::where('id_contract','=', $id_contract)
            ->where('id_area', '=', $id_area)
            ->first();
    }

    public static function getAccountFromOpenPurchaseOrdersByContract($id_contract, $useOpenOC = false){

        $accounts = DB::table('purchase_order')
            ->leftJoin('areas', 'purchase_order.id_area', '=', 'areas.id_area')
            ->leftJoin('contract', 'purchase_order.id_contract', '=', 'contract.id_contract')
            ->leftJoin('account_contract', function($join){
                $join->on('account_contract.id_contract', '=', 'contract.id_contract');
                $join->on('account_contract.id_area', '=', 'areas.id_area');
            })
            ->select('purchase_order.id_area', 'purchase_order.id_contract', 'account_contract.account_code',
                'account_contract.account_year', 'areas.long_name')->distinct()
            ->where('purchase_order.id_contract', '=', $id_contract);

        if($useOpenOC){
            $accounts = $accounts->whereIn('purchase_order.order_state', ['Aprobada', 'Emitida']);
        }

        return $accounts->get();
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use DB;

use Illuminate\Support\Collection;

/**
 * App\Models\AccountBudget
 *
 * @property int $id_area
 * @property string $budget_year
 * @property string $account_name
 * @property string $account_code
 * @property string $description
 * @property float $total_budget_initial
 * @property float $total_budget_available
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereAccountCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereAccountName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereBudgetYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereTotalBudgetAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AccountBudget whereTotalBudgetInitial($value)
 * @mixin \Eloquent
 */
class AccountBudget extends RossModel
{
    use Auditable;

    protected $primaryKey = 'id_area';

    protected $fillable = [
        'id_area',
        'budget_year',
        'account_name',
        'account_code',
        'description',
        'total_budget_initial',
        'total_budget_available'
    ];

    protected $table       = 'account_budget';

    public $timestamps = false;

    public static function getCountBudget($id=null){

        $count = 0;

        if($id != null){
            $count = DB::table('account_budget')->where('id_area','=', $id)
                ->whereRaw(DB::raw(" cast(budget_year as unsigned)  = YEAR(CURDATE() ) "))->count();
        }

        return $count;
    }

    public static function getBudgetByArea($id=null, $search = null){

        $result = new Collection();

        if($id != null){
            $result = DB::table('account_budget')
                ->select('budget_year', 'account_code', 'account_name', 'description',
                         DB::raw("CONCAT( '$ ', CAST(FORMAT(total_budget_initial,0,'de_DE') as char)) "),
                         DB::raw("CONCAT( '$ ', CAST(FORMAT(total_budget_available,0,'de_DE') as char)) "))
                ->where('id_area','=', $id)
                ->whereRaw(DB::raw(" CAST(budget_year as unsigned)  = YEAR(CURDATE() ) "));

            if($search != null){

                $result->where(function($query) use ($search){
                    $query->where('budget_year', 'like', '%'.$search.'%')
                        ->orWhere('account_code','like', '%'.$search.'%')
                        ->orWhere(DB::raw("CONCAT( CAST(FORMAT(total_budget_initial,2,'de_DE') as char), ' $') "), 'like', '%'.$search.'%')
                        ->orWhere(DB::raw("CONCAT( CAST(FORMAT(total_budget_available,2,'de_DE') as char), ' $') "), 'like', '%'.$search.'%');
                });

            }

            $result = $result->get();
        }

        return $result;
    }

    public static function getBudget($id, $year , $code){

        return AccountBudget::where('id_area','=', $id)->where('budget_year', '=', $year)->where('account_code', '=', $code)->first();
    }

    public static function getBudgetAvailable($id_area, $year){
        
        return DB::table('account_budget')
            ->select('budget_year', DB::raw("SUM(total_budget_initial) as total_budget_initial "), DB::raw("SUM(total_budget_available) as total_budget_available "))
            ->where('id_area','=', $id_area)
            ->where('budget_year', '=', $year)
            ->get();
    }

    public static function deleteBudgets($id_area){

        $result = DB::table('account_budget')
            ->where('id_area', $id_area);

        $result ->delete();

        return true;
    }

    public static function updateBudgetAvailable($id_area, $year, $id_account, $orderTotal){

        return AccountBudget::where('id_area', $id_area)
            ->where('budget_year', $year)
            ->where('account_code', $id_account)->decrement('total_budget_available', $orderTotal );
    }

    public static function restoreBudgetAvailable($id_area, $year, $id_account, $orderTotal){

        return AccountBudget::where('id_area', $id_area)
            ->where('budget_year', $year)
            ->where('account_code', $id_account)->increment('total_budget_available', $orderTotal );
    }

    public static function validateBudgetAvailable($id_area, $year, $code){

        $valid = true;
        /** @var AccountBudget $budget */
        $budget = self::getBudget($id_area, $year, $code);

        if($budget->total_budget_available < 0){
            $valid = false;
        }

        return $valid;
    }

    public static function getAccountsByAreaCurrentYear($id_area){
        $current_year = date("Y");
        return AccountBudget::where('id_area','=',$id_area)
            ->where('budget_year','=',$current_year)
            ->get();
    }


}

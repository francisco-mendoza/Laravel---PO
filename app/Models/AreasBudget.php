<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\AreasBudget
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @mixin \Eloquent
 * @property int $id_area
 * @property string $budget_year
 * @property float $total_budget_initial
 * @property float $total_budget_available
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AreasBudget whereBudgetYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AreasBudget whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AreasBudget whereTotalBudgetAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AreasBudget whereTotalBudgetInitial($value)
 */
class AreasBudget extends RossModel
{
    use Auditable;

    protected $primaryKey = 'id_area';
    protected $fillable = [
        'id_area',
        'budget_year',
        'total_budget_initial',
        'total_budget_available'
    ];

    protected $table       = 'areas_budget';

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function getCountBudget($id=null){

        $count = 0;

        if($id != null){
            $count = DB::table('areas_budget')->where('id_area','=', $id)->count();
        }

        return $count;
    }

    public static function getBudgetByArea($id=null, $search = null){

        $result = new Collection();

        if($id != null){
            $result = DB::table('areas_budget')
                ->select('budget_year', DB::raw("CONCAT( '$ ', CAST(FORMAT(total_budget_initial,0,'de_DE') as char)) "),DB::raw("FORMAT(total_budget_initial,0,'de_DE') "))
                ->where('id_area','=', $id);

            if($search != null){

                $result->where(function($query) use ($search){
                    $query->where('budget_year', 'like', '%'.$search.'%')
                        ->orWhere(DB::raw("CONCAT( CAST(FORMAT(total_budget_initial,2,'de_DE') as char), ' $') "), 'like', '%'.$search.'%');
                });

            }

            $result = $result->get();
        }

        return $result;
    }
    
    public static function getBudget($id, $year){

        return AreasBudget::where('id_area','=', $id)->where('budget_year', '=', $year)->first();
    }
    

    public static function deleteBudgets($id){

        $result = DB::table('areas_budget')
            ->where('id_area', $id);

        $result ->delete();

        return true;
    }

    public static function getBudgetsByArea($id, $year){


        return  AreasBudget::where('id_area','=', $id)->where('budget_year','=', $year)->get();
    }

    public static function updateBudgetAvailable($id_area, $year, $orderTotal){

        return AreasBudget::where('id_area', $id_area)
            ->where('budget_year', $year)->decrement('total_budget_available', $orderTotal );
    }

    public static function restoreBudgetAvailable($id_area, $year, $orderTotal){

        return AreasBudget::where('id_area', $id_area)
            ->where('budget_year', $year)->increment('total_budget_available', $orderTotal );
    }

    public static function validateBudgetAvailable($id_area, $year){

        $valid = true;
        /** @var AreasBudget $budget */
        $budget = self::getBudget($id_area, $year);

        if($budget->total_budget_available < 0){
            $valid = false;
        }

        return $valid;
    }


}

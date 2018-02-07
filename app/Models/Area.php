<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use OwenIt\Auditing\Auditable;

use DB;

/**
 * App\Models\Area
 *
 * @mixin \Eloquent
 * @property int $id_area
 * @property string $short_name
 * @property string $long_name
 * @property string $manager_name
 * @property string $manager_position
 * @property int $id_user
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereIdArea($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereShortName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereLongName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereManagerName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereManagerPosition($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereIdUser($value)
 * @property int $budget_closed
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Area whereBudgetClosed($value)
 */
class Area extends RossModel
{
    use Auditable;
    
    protected $primaryKey = 'id_area';
    protected $fillable = [
        'id_area',
        'short_name',
        'long_name',
        'manager_name',
        'manager_position',
        'id_user',
        'budget_closed'
    ];


    const SHORT_NAME = 1;
    const LONG_NAME = 2;
    const MANAGER_NAME = 3;
    const MANAGER_POSITION = 4;

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [    ];

    public static function getAreasOption(){

        return Area::pluck('long_name','id_area');
    }

    public static function getCountAreas($user){

        $count = DB::table('areas')->distinct();
        
        if($user != null){
            $count->where('areas.id_user','=', $user);
        }
        
        return $count->count();
    }

    public static function Areas($start, $len, $search, $column, $dir, $user){

        $areas =  DB::table('areas')->distinct();

        if($start!== null && $len !== null){
            $areas->skip($start)->limit($len);
        }

        if($user != null){
            $areas->where('areas.id_user','=', $user);
        }

        if($search !== null){
            $areas->where(function($query) use ($search){
                $query->where('short_name','like', '%'.$search.'%')
                ->orWhere('long_name','like','%'.$search.'%')
                ->orWhere('manager_name','like','%'.$search.'%')
                ->orWhere('manager_position','like','%'.$search.'%');
            });
        }


        if($column!== null && $dir !== null){
            switch($column){
                case self::SHORT_NAME:
                    $areas->orderBy('short_name', $dir);
                    break;
                case self::LONG_NAME:
                    $areas->orderBy('long_name', $dir);
                    break;
                case self::MANAGER_NAME:
                    $areas->orderBy('manager_name', $dir);
                    break;
                case self::MANAGER_POSITION:
                    $areas->orderBy('manager_position', $dir);
                    break;
                default:
                    $areas->orderBy('long_name', 'asc');
                    break;
            }
        }else{
            $areas->orderBy('long_name', 'asc');
        }

        return $areas->get();
    }

    public static function getAreaBudget($id_area,$year){
        return DB::table('areas_budget')
            ->where('id_area','=',$id_area)
            ->where('budget_year','=',$year)
            ->first();
    }
    
    public static function getAreaByManager($user){
        
//        $areas =  DB::table('areas')->where('areas.id_user','=', $user)->get();
        $areas = Area::where('areas.id_user','=', $user)->pluck('long_name','id_area');
        
        return $areas;
    }
   

}

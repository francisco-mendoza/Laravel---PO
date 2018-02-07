<?php

namespace App\Models;

use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;
use DB;


use OwenIt\Auditing\Auditable;
/**
 * App\Models\MenuOption
 *
 * @mixin \Eloquent
 * @property int $id_menu
 * @property string $name_option
 * @property int $order_option
 * @property string $option_route
 * @property string $option_icon
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereIdMenu($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereNameOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOrderOption($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOptionRoute($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MenuOption whereOptionIcon($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Auditing[] $audits
 */
class MenuOption extends RossModel
{

    protected $table       = 'menu_options';
    use Auditable;
    protected $primaryKey  = "id_menu";
    protected $fillable    = [
        'id_menu',
        'name_option',
        'order_option',
        'option_route',
        'option_icon'
    ];
    public $timestamps = false;

    const OptionRoute = "menuOptions.index";

    public static function getMenuOptions($start, $len, $search, $column, $dir)
    {
        $menu_options = DB::table('menu_options')->distinct();

        if($start!== null && $len !== null){
            $menu_options->skip($start)->limit($len);
        }

        if($search !== null){
            $menu_options->where('name_option','like', '%'.$search.'%');
            $menu_options->orWhere('option_route','like', '%'.$search.'%');
        }

        if($column!== null && $dir !== null){
            $menu_options->orderby('order_option', $dir);
        }else{
            $menu_options->orderby('id_menu', 'asc');
        }

        return $menu_options->get();
    }
    public static function getCountMenuOption(){
        return MenuOption::all()->count();
    }
    
    public static function findMenuOptions(){
        return DB::table('menu_options')->orderBy('menu_options.order_option', 'asc')->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * App\Models\Month
 *
 * @property int $id_month
 * @property string $name_month
 * @property string $short_name_month
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Month whereIdMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Month whereNameMonth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Month whereShortNameMonth($value)
 * @mixin \Eloquent
 */
class Month extends RossModel
{

    protected $primaryKey = "id_month";

    public static function getMonths(){
        return Month::all();
    }
}

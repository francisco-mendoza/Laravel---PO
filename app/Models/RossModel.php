<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * App\Models\RossModel
 *
 * @mixin \Eloquent
 */
class RossModel extends Model{

    private  $this_table;


    public function __construct(array $attributes = []){
        $this->this_table = $this->getTable();
         parent::__construct($attributes);
    }

    protected function findBy($columnName,$value){
        return $this::where($columnName,'=',$value)->first();
    }


}
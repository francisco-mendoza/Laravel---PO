<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAreasKeys($areas){

        $keys = array();

        $i = 0;
        foreach ($areas as $key => $value){
            $keys[$i] = $key;
            $i = $i +1;
        }

        return $keys;
    }

    function cleanTotal($value){

        $value = str_replace('.','',$value);
        $value = str_replace(' ','',$value);
        $value = str_replace('$','',$value);
        $value = str_replace('US','',$value);
        $value = str_replace('€','',$value);
        $value = str_replace('UF','',$value);
        $value = str_replace('NKr','',$value);
        $value = str_replace('SKr','',$value);
        $value = str_replace(',','.',$value);

        return $value;
    }

}

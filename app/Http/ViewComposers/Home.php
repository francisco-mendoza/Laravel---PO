<?php
/**
 * Created by PhpStorm.
 * User: anarela
 * Date: 16-03-17
 * Time: 12:03
 */

namespace App\Http\ViewComposers;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

use App\Models\Area;
use Entrust;

class Home
{

    public function compose(View $view)
    {
        $titulo = "";
        $titulo2 = "";
        $areasManager = null;

        $esGerente = false;
        if(Entrust::can('ver_graficosPorArea')) {
            $areasManager = Area::getAreaByManager(Auth::user()->id_user);
        }

        /** @var Area $area */
        if(isset(Auth::user()->id_area)){
            $area = Area::find(Auth::user()->id_area);
            $today = getdate();
            $titulo = "Gestión de Presupuesto de " . $area->long_name . " para el año " . $today['year'];
            $titulo2 = "Gestión de Presupuesto de " . $area->long_name . " distribuido por mes para el año " . $today['year'];
        }

        $view->with('titulo',$titulo)->with('areasManager', $areasManager)->with('titulo2', $titulo2);
    }

}
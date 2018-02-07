<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Whossun\Toastr\Facades\Toastr;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\MenuOption;
use Session;
use App\Models\AreasBudget;
use Entrust;

use App\Models\Role;
use App\Models\Permission;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        $url_avatar = $request->session()->get('avatar_user');

        $esAprobador = Entrust::can('ver_aprobarOrdenes');

        Session::set('opcion_menu',"");

        return view('home.index', [ 'url_avatar' => $url_avatar , 'esAprobador' => $esAprobador ]);
    }

    public function getBudgetHome($id = null){

		if(Entrust::can('ver_graficosPorArea') && isset(Auth::user()->id_area)){
            $today = getdate();

            $area = null;
            $id_area = null;

            if($id != null){
                $id_area = $id;
            }
            else if($area==null){
                $id_area = Auth::user()->id_area;
            }

            $year = $today['year'];
    
            $budget = AreasBudget::getBudget($id_area, $year);
    
            return response()->json($budget);        
		}

		return response()->json(new AreasBudget());
    }

    public function getBudgetByMonth($id = null){

        $empty = array_fill(0,11, 0);

        if(Entrust::can('ver_graficosPorArea') && isset(Auth::user()->id_area)){
            $today = getdate();

            $area = null;
            $id_area = null;

            if($id != null){
                $id_area = $id;
            }
            else if($area==null){
                $id_area = Auth::user()->id_area;
            }

            // A la fecha se debe tomar lo que está en BD (2016-2017) como presupuesto del año 2017

            $budgets = PurchaseOrder::getTotalByMonth($id_area);

            $data = array();

            foreach($budgets as $budget){
                array_push($data,number_format($budget->total, 1, '.', ''));
            }

            return response()->json($data);
        }

        return response()->json($empty);
    }


}

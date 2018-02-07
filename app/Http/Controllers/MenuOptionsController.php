<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\FontAwesome;
use App\Models\MenuOption;
use Toastr;
use DB;

class MenuOptionsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:ver_menuOptions.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_menus',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_menus',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_menus',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('menuOptions.index', []);
	}

	public function create(Request $request)
	{
        return view('menuOptions.add');
	}

	public function edit(Request $request, $id)
	{
		$MenuOption = MenuOption::findOrFail($id);
	    return view('menuOptions.add', [
	        'model' => $MenuOption	    ]);
	}

	public function show(Request $request, $id)
	{
		$MenuOption = MenuOption::findOrFail($id);
	    return view('menuOptions.show', [
	        'model' => $MenuOption	    ]);
	}

	public function grid(Request $request)
	{
		$len = $request->length;
		$start = $request->start;
        $search = $orderby = $dir = null;

		if($request->search['value']) {
            $search = $request->search['value'];
		}

        if($request->order[0]){
            $orderby = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];
        }

		$count = MenuOption::getCountMenuOption();

		$results = MenuOption::getMenuOptions($start,$len, $search, $orderby, $dir);
		$ret = [];
		foreach ($results as $row) {
			$r = [];
			foreach ($row as $value) {
				$r[] = $value;
			}
			$ret[] = $r;
		}

		$ret['data'] = $ret;
		$ret['recordsTotal'] = $count;
		$ret['iTotalDisplayRecords'] = $count;

		$ret['recordsFiltered'] = count($ret);
		$ret['draw'] = $request->draw;

		return json_encode($ret);

	}


	public function update(Request $request) {
	    //
	    /*$this->validate($request, [
	        'name' => 'required|max:255',
	    ]);*/
		$MenuOption = null;
		$icono = $request->option_icon;
		if($request->id_menu > 0) {
		    $MenuOption = MenuOption::findOrFail($request->id_menu);
            $mensaje = "Se modificado correctamente.";
            $titulo_mensaje = "Menú modificado!";
		}
		else { 
			$MenuOption = new MenuOption;
			$icono = 'fa '.$request->option_icon;
            $mensaje = "Se creado correctamente.";
            $titulo_mensaje = "Menú creado!";
		}

		$MenuOption->id_menu = $request->id_menu;
		$MenuOption->name_option = $request->name_option;
		$MenuOption->order_option = $request->order_option;
		$MenuOption->option_route = $request->option_route;
		$MenuOption->option_icon = $icono;
		
	    try {
			$MenuOption->save();

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear la opción del menú" , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

        

	    return redirect('/menuOptions');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$MenuOption = MenuOption::findOrFail($id);

		try {
			$MenuOption->delete();
		}catch(\Exception $e){
			return false;
		}
		return "OK";
	    
	}

	public function getFontawesome(){
        $fontawesome_icons = FontAwesome::IconsFa;
        $iconos = array();
        foreach ($fontawesome_icons as $clave => $valor){
            array_push($iconos,array('key'=>$clave,'value'=>$valor));
        }
        return response()->json($iconos);
    }
	
}
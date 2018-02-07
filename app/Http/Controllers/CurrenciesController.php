<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Whossun\Toastr\Facades\Toastr;
use App\Models\Currency;

use App\Models\Role;


use DB;

class CurrenciesController extends Controller
{
    //
    public function __construct()
    {

        $this->middleware('permission:ver_currencies.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_monedas',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_monedas',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_monedas',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('currencies.index', []);
	}

	public function create(Request $request)
	{
	    return view('currencies.add', [[]]);
	}

	public function edit(Request $request, $id)
	{
		$currency = Currency::findOrFail($id);

	    return view('currencies.add', [
	        'model' => $currency	    ]);
	}

	public function show(Request $request, $id)
	{
		$currency = Currency::findOrFail($id);
	    return view('currencies.show', [
	        'model' => $currency	    ]);
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

        $count = Currency::getCountCurrency();

        $results = Currency::getCurrencies($start,$len, $search, $orderby, $dir);

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
        $rules = [
            'name_currency' => 'required|max:60',
            'short_name' => 'required|max:60',
            'code' => 'required|max:10',
        ];

        $niceNames = [
            'name_currency' => 'Nombre Moneda',
            'short_name' => 'Sigla',
            'code' => 'Código',
        ];
	    $this->validate($request, $rules ,[],$niceNames);

		$currency = null;

		if($request->id_currency > 0) {
		    $currency = Currency::findOrFail($request->id_currency);
            $mensaje = "Se modificado correctamente.";
            $titulo_mensaje = "Moneda modificada!";
		}
		else { 
			$currency = new Currency;
            $mensaje = "Se creado correctamente.";
            $titulo_mensaje = "Moneda creada!";
		}
	    		
		$currency->id_currency = $request->id_currency;
		$currency->name_currency = $request->name_currency;
		$currency->short_name = $request->short_name;
		$currency->code = $request->code;

	    try {
			$currency->save();
			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear la moneda" , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

        

	    return redirect('/currencies');

	}

	public function store(Request $request)
	{

		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$currency = Currency::findOrFail($id);

		try {
			$currency->delete();
		}catch(\Exception $e){
			return false;
		}
		return "OK";
	    
	}

	/**
     * Recibe los parametros $moneda_origen y $moneda_destino en el estandar ISO 4217
     * Ejemplo: CLP - USD
     */
	public function conversor_monedas(Request $request){

	    $resp = 'Conversor de divisas ROSS';

        if($request->moneda_origen != null || $request->moneda_destino != null and $request->cantidad != null) {
            $moneda_origen  = Currency::findBy('code',$request->moneda_origen);
            $moneda_destino = $request->moneda_destino == 'CLP' ? Currency::PESO_CHILENO : Currency::findBy('code', $request->moneda_destino)->id_currency;
            $resp = Currency::conversorMoneda($moneda_origen->id_currency,$moneda_destino,$request->cantidad);
        }

        return json_encode($resp);
    }

	
}
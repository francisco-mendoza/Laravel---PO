<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\PaymentMethod;
use Whossun\Toastr\Facades\Toastr;

use DB;

class PaymentMethodController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:ver_paymentmethods.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_metodos',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_metodos',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_metodos',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('paymentmethods.index', []);
	}

	public function create(Request $request)
	{
	    return view('paymentmethods.add', [ [] ]);
	}

	public function edit(Request $request, $id)
	{
		$paymentmethod = Paymentmethod::findOrFail($id);
	    return view('paymentmethods.add', [ 'model' => $paymentmethod	 ]);
	}

	public function show(Request $request, $id)
	{
		$paymentmethod = Paymentmethod::findOrFail($id);
	    return view('paymentmethods.show', [
	        'model' => $paymentmethod	    ]);
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
				
		$count = PaymentMethod::getCountPaymentMethods();

		$results = PaymentMethod::PaymentMethods($start,$len, $search, $orderby, $dir);
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

		$rules = [
			'name_method' => 'required|max:128',
		];

		$niceNames = [
			'name_method' => 'Método de Pago',
		];

		$this->validate($request, $rules ,[],$niceNames);

		
		$paymentmethod = null;
		if($request->id_payment_method > 0) {
			$paymentmethod = Paymentmethod::findOrFail($request->id_payment_method);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Método de pago modificado!";
		}
		else { 
			$paymentmethod = new Paymentmethod;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Método de pago creado!";
		}
	    		
		$paymentmethod->name_method = $request->name_method;

		try {
			$paymentmethod->save();
			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear el método de pago" , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}

	    return redirect('/paymentmethods');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$paymentmethod = Paymentmethod::findOrFail($id);

		try {
			$paymentmethod->delete();
		}catch(\Exception $e){
			return false;
		}
		return "OK";
	    
	}

	
}
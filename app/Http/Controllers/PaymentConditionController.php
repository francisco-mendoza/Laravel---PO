<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\PaymentCondition;

use DB;

use Whossun\Toastr\Facades\Toastr;

class PaymentConditionController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:ver_paymentconditions.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_condiciones',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_condiciones',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_condiciones',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('paymentconditions.index', []);
	}

	public function create(Request $request)
	{
	    return view('paymentconditions.add', [ [] ]);
	}

	public function edit(Request $request, $id)
	{
		$paymentcondition = Paymentcondition::findOrFail($id);
	    return view('paymentconditions.add', [
	        'model' => $paymentcondition	    ]);
	}

	public function show(Request $request, $id)
	{
		$paymentcondition = Paymentcondition::findOrFail($id);
	    return view('paymentconditions.show', [
	        'model' => $paymentcondition	    ]);
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


		$count = PaymentCondition::getCountPaymentConditions();


		$results = PaymentCondition::PaymentConditions($start,$len, $search, $orderby, $dir);
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
		$ret['draw'] =$request->draw;

		return json_encode($ret);

	}

	public function validateFormPaymentCondition(Request $request){

		$rules = [
			'name_condition' => 'required|max:128',
		];

		$niceNames = [
			'name_condition' => 'Condición de Pago',
		];

		$this->validate($request, $rules ,[],$niceNames);



	}


	public function update(Request $request) {

		//Validar formulario
		$this->validateFormPaymentCondition($request);
		

		$paymentcondition = null;
		if($request->id_payment_conditions > 0) {
			$paymentcondition = Paymentcondition::findOrFail($request->id_payment_conditions);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Condición de pago modificada!";}
		else { 
			$paymentcondition = new Paymentcondition;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Condición de pago creada!";
		}		
	    		
		$paymentcondition->name_condition = $request->name_condition;

		try {
			$paymentcondition->save();

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear la condición de pago." , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

	    return redirect('/paymentconditions');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$paymentcondition = Paymentcondition::findOrFail($id);

		try {
			$paymentcondition->delete();
		}catch(\Exception $e){
			return false;
		}
		return "OK";
	    
	}

	
}
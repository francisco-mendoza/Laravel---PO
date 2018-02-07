<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Provider;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;

use Whossun\Toastr\Facades\Toastr;

use DB;

class ProvidersController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('permission:ver_providers.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_proveedores',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_proveedores',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_proveedores',['only' => ['destroy']]);

    }


    public function index(Request $request)
	{
	    return view('providers.index', []);
	}

	public function create(Request $request)
	{
		$payment_methods = PaymentMethod::getPaymentMethod();
		$payment_conditions = PaymentCondition::getPaymentConditions();

	    return view('providers.add', [ 'methods' => $payment_methods, 'conditions' => $payment_conditions   ]);
	}

	public function edit(Request $request, $id)
	{
		$provider = Provider::findOrFail($id);
		$payment_methods = PaymentMethod::getPaymentMethod();
		$payment_conditions = PaymentCondition::getPaymentConditions();

	    return view('providers.add', [
	        'model' => $provider, 'methods' => $payment_methods, 'conditions' => $payment_conditions   ]);
	}

	public function show(Request $request, $id)
	{
		$provider = Provider::findProviderJoined($id);
	    return view('providers.show', [
	        'model' => $provider	    ]);
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
			if($request->draw!=1) {
				$orderby = $request->order[0]['column'];
				$dir = $request->order[0]['dir'];
			}
		}
		

		$count = Provider::getCountProviders();

		$results = Provider::providers($start,$len, $search, $orderby, $dir);
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
			'name_provider' => 'required|max:128',
			'rut' => 'required|max:128',
			'address' => 'required|max:128',
			'phone' => 'required|max:60'
		];

		$niceNames = [
			'name_provider' => 'Razón Social',
			'rut' => 'RUT',
			'address' => 'Dirección',
			'phone' => 'Teléfono'
		];

		$this->validate($request, $rules ,[],$niceNames);

		$provider = null;
		if($request->id_provider > 0) { 
			$provider = Provider::findOrFail($request->id_provider);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Proveedor modificado!";
		}
		else { 
			$provider = new Provider;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Proveedor creado!";
		}	    

	    		

		$provider->name_provider = $request->name_provider;
		$provider->business = $request->business;
		$provider->rut = $request->rut;
		$provider->address = $request->address;
		$provider->phone = $request->phone;
		$provider->contact_name = $request->contact_name;
		$provider->contact_area = $request->contact_area;
		$provider->contact_email = $request->contact_email;
		$provider->contact_phone = $request->contact_phone;
		$provider->is_visible = $request->is_visible == "on" ? 1 : 0;
		$provider->payment_conditions = $request->payment_conditions == "" ? null : $request->payment_conditions;
		$provider->payment_method = $request->payment_method == "" ? null : $request->payment_method;
		$provider->bank = $request->bank;
		$provider->type_account = $request->type_account;
		$provider->number_account = $request->number_account == "" ? null : $request->number_account;


		try {

			$nameProvider= $provider->name_provider;
			if( (strpos($nameProvider, '-') !== false) || (strpos($nameProvider, ')') !== false) || (strpos($nameProvider, '(') !== false)){
				throw new \Exception('Razón social no debe contener guión o paréntesis.');
			}

			$provider->save();

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}catch(\Exception $e){
			Toastr::error("Ocurrió una excepción al crear el proveedor. " . $e->getMessage() , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

	    return redirect('/providers');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$provider = Provider::findOrFail($id);

		try {
			$provider->delete();

		} catch (\Exception $e) {
			return false;
		}

		return "OK";
	    
	}

    public  function getSelectProviders(){
        $providers = Provider::getProviders();

        $data = array();

        $i = 0;
        foreach($providers as $provider){
            $data[$i] = $provider->name_provider;
            $i++;
        }

        return response()->json($data);
    }


}
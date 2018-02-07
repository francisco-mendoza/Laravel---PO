<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\RossModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Contract;
use App\Models\Provider;
use App\Models\Area;
use App\Models\AccountContract;
use App\Models\Role;
use Config;

use Whossun\Toastr\Facades\Toastr;

use Storage;

use DateTime;

use DB;
use Auth;
use Entrust;

class ContractsController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:ver_contracts.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_contratos',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_contratos',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_contratos',['only' => ['destroy']]);
	}

    public function index(Request $request)
	{

	    return view('contracts.index', []);
	}

	public function create(Request $request)
	{
		$areas = Area::getAreasOption();
	    return view('contracts.add', [ 'areas' => $areas  ]);
	}

	public function edit(Request $request, $id)
	{

		/** @var Contract $contract */
		$contract = Contract::findOrFail($id);		
		$provider = Provider::find($contract->id_provider);
		$areas = Area::getAreasOption();
		
	    return view('contracts.add', [  'model' => $contract	,'provider' => $provider, 'areas' => $areas    ]);
	}
	
	public  function getProviders(){
		$providers = Provider::getProviders();

		$data = array();

		$i = 0;
		foreach($providers as $provider){
			$data[$i] = $provider->name_provider;
			$i++;
		}

		return response()->json($data);
	}

	public function show(Request $request, $id)
	{

		/** @var Contract $contract */
		$contract = Contract::findOrFail($id);
		
		$provider = Provider::find($contract->id_provider);

		$area = new Area();
		if($contract->contract_area != ""){
			$area = Area::findOrFail($contract->contract_area);
		}
		
	    return view('contracts.show', [
	        'model' => $contract, 'provider' => $provider , 'area' => $area  ]);
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
		
		$count = Contract::getCountContracts();

		$results = Contract::Contracts($start, $len, $search, $orderby, $dir);
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

	public function validateContract(Request $request){
        $contract = Contract::getContractByProvider($request->name_provider,$request->contract_number);
        return response()->json($contract);
    }

	public function validateFormContract(Request $request){

        $rules = [
            'id_provider' => 'required',
            'contract_number' => 'required|max:128',
        ];

        $niceNames = [
            'id_provider' => 'Proveedor',
            'contract_number' => 'Número de Contrato',
        ];

        $this->validate($request, $rules ,[],$niceNames);
    }

    public function update(Request $request) {


	    //Validar formulario
        $this->validateFormContract($request);


		$contract = null;
		if($request->id_contract > 0) {
			$contract = Contract::findOrFail($request->id_contract);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Contrato modificado!";}
		else {

			$contract = new Contract;

			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Contrato creado!";
		}

		/** @var Provider $proveedor */
		$proveedor = Provider::getProviderByName($request->id_provider);

		$contract->id_provider = $proveedor->id_provider;
		$contract->contract_number = $request->contract_number;
		$contract->description = $request->description;

		$contract->is_active = $request->is_active == "on" ? 1 : 0;
		$contract->start_date = $contract->is_active == 1 ? date("Y-m-d") : null;
		$contract->end_date = $request->end_date != "" ? date_format(date_create_from_format('d/m/Y', $request->end_date),"Y-m-d") : null;

		if($request->contract_pdf != null && $request->contract_pdf!= ""){
			$pdf = $request->file('contract_pdf');
			$pdfName = $pdf->getClientOriginalName();
			Storage::disk('pdfContratos')->put($pdfName,file_get_contents($pdf->getRealPath()));

			$contract->contract_pdf = $pdfName;
		}

        $error_contrato = 'Ocurrió una excepción al crear el contrato';
		try {
            DB::transaction(function() use ($request, $contract) {

				$contractNumber= $contract->contract_number;
				if( (strpos($contractNumber, '-') !== false) || (strpos($contractNumber, ')') !== false) || (strpos($contractNumber, '(') !== false)){
					throw new \Exception(' Número de contrato no debe contener guión o paréntesis.');
				}

				$contractDesc= $contract->description;
				if( (strpos($contractDesc, '-') !== false) || (strpos($contractDesc, ')') !== false) || (strpos($contractDesc, '(') !== false)){
					throw new \Exception(' La descripción del contrato no debe contener guión o paréntesis.');
				}

                $contract->save();

                $id = $contract->id_contract;
                $accounts = json_decode($request->accounts);

                $edit_accounts = $request->edit_accounts;
                //Vemos si es edicion ( se habilita por js )
                if ( $edit_accounts == 'true' ){

                    //Buscar si existen OC asociadas a este contrato, si es así obtener las cuentas correspondientes
                    $usedAccounts = AccountContract::getAccountFromOpenPurchaseOrdersByContract($id, true);

                    if($usedAccounts){ //Existen cuentas del contrato siendo usadas por OC
                        foreach ($usedAccounts as $acc){
                            $found = false;
                            foreach($accounts as $a){
                                //Validar que la cuenta usada se encuentre en la lista de cuentas (sin haberse modificado)
                                if($a->area == $acc->long_name && $a->account_code == $acc->account_code && $a->account_year == $acc->account_year){
                                    $found = true;
                                    break;
                                }
                            }

                            if(!$found){
                                throw new \Exception(' Estas intentado modificar una cuenta asociada a una Orden de Compra');
                            }
                        }
                    }

                    // Borrar todas las cuentas de este contrato
                    AccountContract::deleteAccountsByContract($id);
                }

                //Actualizar las cuentas en caso de inserción o edición de cuentas
                if(($request->id_contract > 0 && $edit_accounts == 'true') or $request->id_contract <= 0){
                    foreach ($accounts as $account) {
                        $area = Area::findBy('long_name',$account->area);
                        $id_area = $area->id_area;
                        $b = $this->createAccountContract($id, $id_area,$account->account_code,$account->account_year);
                        $b->save();
                    }
                }
            });

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}catch(\Exception $e){
			Toastr::error($error_contrato . $e->getMessage() , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

	    return redirect('/contracts');

	}

    public function createAccountContract($id_contract, $id_area, $account_code, $account_year){
        /** @var AccountContract $b */
        $b = new AccountContract();
        $b->id_contract = $id_contract;
        $b->id_area = $id_area;
        $b->account_code = $account_code;
        $b->account_year = $account_year;
        return $b;
    }

    /**
     * @param Request $request
     * @param null $id id del CONTRATO a consultar
     * @return string
     */
    public function getAccounts(Request $request, $id = null){

        $ret = [];
        $count = AccountContract::getCountAccount($id);

        if($id != null){

            $search = null;

            if($request->search['value']) {
                $search = $request->search['value'];
            }

            $results = AccountContract::getAccountByContract($id, $search);
            foreach ($results as $row) {
                $r = [];
                foreach ($row as $value) {
                    $r[] = $value;
                }
                /** @var Area $area */
                $validateAccountOc = PurchaseOrder::getOrdersByAreaAndContract($r[0],$id);
                $countOC =  count($validateAccountOc);
                if($countOC>0){
                    array_push($r,"noedit");
                }else{
                    array_push($r,"edit");
                }

                $ret[] = $r;

            }

        }

        $ret['data'] = $ret;
        $ret['recordsTotal'] = $count;
        $ret['iTotalDisplayRecords'] = $count;

        $ret['recordsFiltered'] = count($ret);
        $ret['draw'] =  $request->draw;

        return json_encode($ret);
    }

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {
		
		$contract = Contract::findOrFail($id);

		try {
            DB::transaction(function() use ($request, $contract) {
                $accounts = AccountContract::getAccountFromOpenPurchaseOrdersByContract($contract->id_contract);
                if(count($accounts)>0){
                    throw new \Exception('Existen OC (vigentes o no) asociadas a alguna cuenta del contrato seleccionado.');
                }

                AccountContract::deleteAccountsByContract($contract->id_contract);
                $contract->delete();
            });

		} catch (\Exception $e) {
            $returnData = array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
            return response()->json($returnData, 500);
		}
		return "OK";
	    
	}

	
}
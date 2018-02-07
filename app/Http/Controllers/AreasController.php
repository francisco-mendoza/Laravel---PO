<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Whossun\Toastr\Facades\Toastr;

use App\Models\Area;
use App\Models\User;
use App\Models\AreasBudget;
use App\Models\AccountBudget;
use Config;
use App\Models\Role;
use Session;
use DB;
use Entrust;

class AreasController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:ver_areas.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_areas',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_areas',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_areas',['only' => ['destroy']]);
    }

    public function index(Request $request)
	{
	    return view('areas.index', []);
	}

	public function create(Request $request)
	{
	    return view('areas.add', [  []  ]);
	}

	public function edit(Request $request, $id)
	{
		$area = Area::findOrFail($id);
	    return view('areas.add', [
	        'model' => $area	    ]);
	}

	public function show(Request $request, $id)
	{
		$areas = null;


        $areas = $this->getAreasKeys(Area::getAreaByManager(Auth::user()->id_user));

        if(User::needFilteringByArea() && ! in_array(  $id, $areas) ){
            Session::flash('error_message', config('messages.areaRestricted'));
            return redirect(url('/areas'));
        }
		
		/** @var Area $area */
		$area = Area::findOrFail($id);

		$today = getdate();

		$budget = null;
		$accounts = null;
		
		if($area->budget_closed == 0){
			if(AccountBudget::getCountBudget($area->id_area) != 0){
				$budget = AccountBudget::getBudgetAvailable($area->id_area,$today['year']);
			}

		}else{
			$budget = AreasBudget::getBudgetsByArea($id,$today['year']);
		}

		$accounts = AccountBudget::getAccountsByAreaCurrentYear($area->id_area);

	    return view('areas.show', [
	        'model' => $area	, 'budgets' => $budget  , 'accounts' =>$accounts  ]);
	}

	public function grid(Request $request)
	{
		$len = $request->length;
		$start = $request->start;
		$search = $orderby = $dir = $user = null;

		if($request->search['value']) {
			$search = $request->search['value'];
		}

		if($request->order[0]){
			$orderby = $request->order[0]['column'];
			$dir = $request->order[0]['dir'];
		}

		if(User::needFilteringByArea()){ //Saber si necesitan filtrar
			$user = Auth::User()->id_user;
		}

		$count = Area::getCountAreas($user);
		
		$results = Area::Areas($start,$len, $search, $orderby, $dir, $user);
		
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
		$ret['draw'] =  $request->draw;

		return json_encode($ret);

	}

	public function createAccountBudget($id, $year, $amount, $code, $name, $desc){
		/** @var AccountBudget $b */
		$b = new AccountBudget();
		$b->id_area = $id;
		$b->budget_year = $year;
		$b->account_code = $code;
		$b->account_name = $name;
		$b->description = $desc;
		$b->total_budget_initial = floatval($amount);
		$b->total_budget_available = floatval($amount);

		return $b;
	}

	public function setBudgetForArea($id_area){

		$today = getdate();
		$budget = AccountBudget::getBudgetAvailable($id_area, $today['year']);

		$areaBudget = new AreasBudget();
		$areaBudget->id_area = $id_area;
		$areaBudget->budget_year = $today['year'];
		if($budget[0] != null && $budget[0]->total_budget_initial != null){
			$areaBudget->total_budget_initial = $budget[0]->total_budget_initial;
			$areaBudget->total_budget_available = $budget[0]->total_budget_available;
		}else{
			$areaBudget->total_budget_initial = 0;
			$areaBudget->total_budget_available = 0;
		}

		$areaBudget->save();
	}

	public function updateBudgetForArea($id_area){

		$today = getdate();
		$budgetArea = AreasBudget::getBudget($id_area, $today['year']);

		$budget = AccountBudget::getBudgetAvailable($id_area, $today['year']);

		if($budget[0] != null && $budget[0]->total_budget_available != null){
			$budgetArea->total_budget_available = $budget[0]->total_budget_available;
		}

		$budgetArea->save();
	}

	public function validateFormArea(Request $request){

		$rules = [
			'short_name' => 'required|max:16',
			'long_name' => 'required|max:128',
			'manager_name' => 'required|max:128',
			'manager_position' => 'required|max:128'
		];

		$niceNames = [
			'short_name' => 'Abreviatura',
			'long_name' => 'Nombre de Área',
			'manager_name' => 'Gerente',
			'manager_position' => 'Cargo Gerente'
		];

		$this->validate($request, $rules ,[],$niceNames);

	}

	public function getNewAmounts($budget_closed_before, $budgetOld, $newAmount, $totalBudgetInterface){

		/** @var AccountBudget $amounts */
		$amounts = new AccountBudget();
		$amounts->total_budget_initial = $budgetOld->total_budget_initial;
		$amounts->total_budget_available = $budgetOld->total_budget_available;

		if( $budgetOld->total_budget_initial < $newAmount){//Estoy aumentando el presupuesto de la cuenta
			$diff = floatval($newAmount) - floatval($budgetOld->total_budget_initial);

			if($budget_closed_before == 1){ //El área estaba cerrada
				/** @var AreasBudget $areaBudget */
				$areaBudget = AreasBudget::getBudget($budgetOld->id_area, $budgetOld->budget_year);

				if(floatval($totalBudgetInterface) > $areaBudget->total_budget_initial){
					throw new \Exception('El ajuste al monto total de la cuenta no procede pues excede el presupuesto definido para el área');
				}else{
					$amounts->total_budget_initial = floatval($newAmount);
					$amounts->total_budget_available = floatval($budgetOld->total_budget_available) + $diff;
				}
			}elseif($budget_closed_before == 0){ //El área estaba abierta
				$amounts->total_budget_initial = floatval($newAmount);
				$amounts->total_budget_available = floatval($budgetOld->total_budget_available) + $diff;
			}

		}elseif($budgetOld->total_budget_initial > $newAmount){ //Estoy disminuyendo el presupuesto de la cuenta
			$diff = floatval($budgetOld->total_budget_initial) - floatval($newAmount) ;
			if($diff > $budgetOld->total_budget_available){
				throw new \Exception('El ajuste al monto de la cuenta no procede pues ya se consumió más de lo que se desea debitar');
			}else{
				$amounts->total_budget_initial = floatval($newAmount);
				$amounts->total_budget_available = floatval($budgetOld->total_budget_available) - $diff;
			}
		}

		return $amounts;
	}

	public function update(Request $request) {


		//Validar formulario
		$this->validateFormArea($request);


		$fijarBudget = false;
		$area = null;
		$budget_closed_before = 0;
		if($request->id_area > 0) {
			/** @var Area $area */
			$area = Area::findOrFail($request->id_area);
			$mensaje = "Se ha modificado correctamente.";
			$titulo_mensaje = "Área modificada!";
			$budget_closed_before = $area->budget_closed;
		}
		else {
			/** @var Area $area */
			$area = new Area;
			$mensaje = "Se ha creado correctamente.";
			$titulo_mensaje = "Área creada!";
		}


		$area->short_name = $request->short_name;
		$area->long_name = $request->long_name;
		$area->manager_name = $request->manager_name;
		$area->manager_position = $request->manager_position;
		$area->id_user = $request->id_user;

		if(($request->budget_closed == "on" && $area->budget_closed == 0)){
			$area->budget_closed = 1;
			$fijarBudget = true;
		}

		try {

			DB::transaction(function() use ($request, $area, $fijarBudget, $budget_closed_before) {

				$area->save();
				$id = $area->id_area;

				$budgets = json_decode($request->budgets);

				foreach ($budgets as $budget) {
					$budget->amount= str_replace('.','', $budget->amount);
					if ($request->id_area > 0) {
						/** @var AccountBudget $b */
						$b = AccountBudget::getBudget($id, $budget->year, $budget->code);
						if($b!=null){

							$amounts = $this->getNewAmounts($budget_closed_before,$b, $budget->amount, $request->total_budget_html);

							AccountBudget::where('id_area', $id)
								->where('budget_year', $budget->year)
								->where('account_code', $budget->code)->update(array(
								'account_name'    =>  $budget->name,
								'description' =>  $budget->desc,
								'total_budget_initial' =>  $amounts->total_budget_initial,
								'total_budget_available' =>  $amounts->total_budget_available
							));

						}elseif($b == null ){
							$b = $this->createAccountBudget($id, $budget->year, $budget->amount, $budget->code, $budget->name, $budget->desc);
							$b->save();
						}

					} else {
						$b = $this->createAccountBudget($id, $budget->year, $budget->amount, $budget->code, $budget->name, $budget->desc);
						$b->save();
					}
				}

				//Fijar presupuesto
				if($fijarBudget){
					$this->setBudgetForArea($id);
				}elseif($budget_closed_before == 1){ //Actualizar presupuesto disponible
					$this->updateBudgetForArea($id);
				}

			});

			Toastr::success($mensaje, $titulo_mensaje, [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);

		}catch(\Exception $e){
			
			Toastr::error("Ocurrió una excepción al crear el área" , "Ocurrió un error", [
				"positionClass" => "toast-top-right",
				"progressBar" => true,
				"closeButton" => true,
			]);
		}

	    return redirect('/areas');

	}

	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy(Request $request, $id) {


		/** @var Area $area */
		$area = Area::findOrFail($id);

		try {

			DB::transaction(function() use ($request, $area) {

				AreasBudget::deleteBudgets($area->id_area);
				AccountBudget::deleteBudgets($area->id_area);

				$area->delete();

			});

		} catch (\Exception $e) {
			return false;
		}
		
		return "OK";
	    
	}
	
	public function getUsers(){
		$users = User::getUsers();

		$data = array();

		$i = 0;
		foreach($users as $user){
			$data[$i] = ['user_name'=>$user->full_name,'id_user'=>$user->id_user];
			$i++;
		}

		return response()->json($data);
	}

	public function getBudgets(Request $request, $id = null){

		$ret = [];
		$count = AccountBudget::getCountBudget($id);

		if($id != null){

			$search = null;

			if($request->search['value']) {
				$search = $request->search['value'];
			}

			$results = AccountBudget::getBudgetByArea($id, $search);
			$i = 1;
			foreach ($results as $row) {
				$r = [];
				foreach ($row as $value) {
					$r[] = $value;
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

	public function getBudgetAvailableByArea($id_area, $year){

		/** @var AreasBudget $budget */
		$budget = AreasBudget::getBudget($id_area, $year);

		return response()->json($budget);
	}

	public function getBudgetAvailableByAccounts($id_area, $year){

		/** @var AccountBudget $budget */
		$budget = AccountBudget::getBudgetAvailable($id_area, $year);

		return response()->json($budget);
	}

	public function getBudgetAvailableByAccount($id_area, $year, $code){

		/** @var AccountBudget $budget */
		$budget = AccountBudget::getBudget($id_area, $year, $code);

		return response()->json($budget);
	}
	
	public function getAccountsArea(Request $request){
	    //$accountsAll = AccountBudget::where('id_area','=',$request->id_area)->get();
	    $accountsAll = AccountBudget::getAccountsByAreaCurrentYear($request->id_area);

	    $count=0;
        $accounts=array();
	    foreach($accountsAll as $account){
            $accounts[$count] = $account->account_code;
	        $count++;
        }

	    return response()->json($accounts);
    }

    public function getInformationAccount(Request $request){
	    
	    $account = AccountBudget::getBudget($request->id_area,$request->year,$request->account_code);
        //$account = AccountBudget->find;
	    return $account;
    }

	
}
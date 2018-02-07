<?php

namespace App\Http\Controllers;

use App\Models\AccountBudget;
use App\Models\AccountContract;
use App\Models\AreasBudget;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\InvoicesOrders;
use App\Models\PaymentCondition;
use App\Models\PaymentMethod;
use App\Models\PurchaseOrderDetail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Provider;

use Illuminate\Support\Facades\Auth;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Route;
use League\Flysystem\Exception;
use Whossun\Toastr\Facades\Toastr;

use Session;
use App\Models\Area;
use Mail;
use App\Http\ViewMails;

use DB;



class PurchaseOrderController extends Controller
{

    private $Emitida = "Emitida";
    private $aprobada = "Aprobada";


    public function __construct()
    {
        $this->middleware('permission:editar_oc',['only' => ['editPurchaseOrder']]);
        $this->middleware('permission:eliminar_oc',['only' => ['deletePurchaseOrder']]);
    }

    public function consultOrders()
    {
        //Validar que el usuario tenga área asignada

        if(Auth::user()->id_area == null){
            Session::flash('error_message', config('messages.areaNotAssignedForConsultOC'));
            return redirect(route('home'));
        }

        Session::set('opcion_consulta_OC', 'consultarOrdenes');
        
        return view('purchaseOrder.consultOrders', [[]]);
    }

    public function findPurchaseOrders(Request $request, $states){

        $len = $request->length;
        $start = $request->start;
        $search = null;
        $orderby = null;
        $dir = null;
        $filterArea = null;
        $filterUser = null;


        $user = Auth::user()->id_user;
        $area = Area::getAreaByManager($user);


        if(User::needFilteringByArea()){ //Saber si necesitan filtrar por area
            $filterArea = $this->getAreasKeys($area);
        }

        if(User::needFilteringByUser()){ //Filtrar por usuario
            $filterArea = null;
            $filterUser = $user;
        }


        if($request->search['value'] && strlen($request->search['value']) >= 3) {
            $search = $request->search['value'];
        }


        if($request->order[0]){
            if($request->draw!=1) {
                $orderby = $request->order[0]['column'];
                $dir = $request->order[0]['dir'];
            }
        }

        $count = PurchaseOrder::getCountPurchaseOrders($states, $search, $filterArea, $filterUser);

        $results = PurchaseOrder::PurchaseOrders($start,$len, $search, $orderby, $dir, $filterArea, $filterUser,$states);
       

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
    
    public function grid(Request $request){

        $states = null;

        if(User::needFilteringByUser()){
            $states = ['Aprobada', 'Rechazada','Emitida'];
        }

        return $this->findPurchaseOrders($request,$states);

    }

    public function approvedGrid(Request $request){

        $states = ['Aprobada', 'Rechazada'];
        return $this->findPurchaseOrders($request,$states);

    }

    public function approveOrders()
    {

        //Validar que el usuario tenga área asignada

        if(Auth::user()->id_area == null){
            Session::flash('error_message', config('messages.areaNotAssignedForApproveOC'));
            return redirect(route('home'));
        }

        return view('purchaseOrder.approveOrders');
    }

    public function createOrder()
    {
        //Validar que el usuario tenga área asignada

        if(Auth::user()->id_area == null){
            Session::flash('error_message', config('messages.areaNotAssignedForCreateOC'));
            return redirect(route('home'));
        }

        $currencies = Currency::pluck('name_currency','id_currency');
        $payment_methods = PaymentMethod::getPaymentMethod();
        $payment_conditions = PaymentCondition::getPaymentConditions();
        return view('purchaseOrder.createPurchaseOrder',['currencies' => $currencies, 'methods' => $payment_methods, 'conditions' => $payment_conditions]);
        
    }
    
    public function getProviders(){

        $id_area = Auth::user()->id_area;

        $providers = Provider::getProvidersContract($id_area);
        $data = array();

        $i = 0;
        foreach($providers as $provider){
            $data[$i] = $provider->nombre;
            $i++;
        }

        return response()->json($data);
    }
    
    public function getProviderByName($name_provider){
        
        $provider = Provider::getProviderByName($name_provider);
        
        return response()->json($provider);
    }

    public function getMonths(){

        $data = array('1'=>'ENE',
            '2'=>'FEB',
            '3'=>'MAR',
            '4'=>'ABR',
            '5'=>'MAY',
            '6'=>'JUN',
            '7'=>'JUL',
            '8'=>'AGO',
            '9'=>'SEP',
            '10'=>'OCT',
            '11'=>'NOV',
            '12'=>'DIC');
        
        $months = response()->json($data);

        return $months;
    }


    public function validateFormPurchaseOrder(Request $request){

        $rules = [
            'name_provider' => 'required',
            'payment_condition' => 'required',
            'payment_method' => 'required',
            'currency' => 'required',
        ];

        $niceNames = [
            'name_provider' => 'Razón Social',
            'payment_condition' => 'Condición de Pago',
            'payment_method' => 'Método de Pago',
            'currency' => 'Moneda',
        ];

        $this->validate($request, $rules ,[],$niceNames);

    }
    
    public function getFolioName($area){

        $today = getdate();
        $random = rand(0,999);


        return $area->short_name . $today['year'] . sprintf('%02d', $today['mon']) . sprintf('%02d', $today['mday'])
                . sprintf('%02d', $today['hours']) . sprintf('%02d', $today['minutes']) . sprintf('%02d', $today['seconds'])
                . '_' . sprintf('%03d', $random);
        
    }

    public function createPurchaseOrder(Request $request, $folio, $area , $contract ){

        //Crear orden de compra
        $order = new PurchaseOrder;

        $order->folio_number = $folio;

        $order->id_area = $area->id_area;
        $order->id_user = Auth::user()->id_user;
        $order->id_contract = $contract->id_contract;
        $order->id_payment_condition = $request->payment_condition;
        $order->id_payment_method = $request->payment_method;
        $order->contract_number = "";
        $order->quotation_number = "";
        $order->id_currency = $request->currency;
        $order->date_purchase = date("Y-m-d H:i:s");
        $order->exchange_rate = $this->currencyConverter( $request->currency,Currency::PESO_CHILENO, 1);
        $order->paid_type = $request->tipo_boleta;
        $order->total_price = null;
        $order->total_iva_price = null;

        $order->order_state = config('constants.emitida'); //Estado por defecto

        return $order;

    }

    public function createDetail($detail, $order, $folioByMonth){

        $orderDetail = new PurchaseOrderDetail();
        $orderDetail->quantity = $detail->quantity;
        $orderDetail->description = $detail->description;
        $orderDetail->price = ($detail->price/$detail->quantity);
        $orderDetail->has_iva = $detail->has_iva;
        $orderDetail->price_iva = isset($detail->price_iva) ? ($detail->price_iva/$detail->quantity) : null;
        $orderDetail->id_currency = $order->id_currency;
        $orderDetail->id_purchase_order = $folioByMonth;

        return $orderDetail;
    }

    public function createPurchaseOrderDetails($listOrderDetail, $order, $folioByMonth, $area, $account){

        $total = 0;
        $total_con_iva = 0;

        foreach ($listOrderDetail as $detail) {
            //Crear detalles

            $orderDetail = $this->createDetail($detail,$order,$folioByMonth);

            $total = $total + $detail->price;
            $total_con_iva = $total_con_iva + (isset($detail->price_iva) ? $detail->price_iva : $detail->price);

            $orderDetail->save();
        }

        //Actualizar el monto de la orden de compra maestra
        $order = PurchaseOrder::find($folioByMonth);
        $order->total_price = $total;
        $order->total_iva_price = $total_con_iva;

        $order->save();

        $today = getdate();

        //Convertir el monto a CLP usando la tasa obtenida antes
        $total_CLP = $total * $order->exchange_rate;

        //Actualizar monto disponible de la cuenta asociada a la orden
        AccountBudget::updateBudgetAvailable($area->id_area,$today['year'],$account->account_code,  $total_CLP);



        //Actualizar monto disponible del área asociada a la orden
        AreasBudget::updateBudgetAvailable($area->id_area, $today['year'], $total_CLP);

        return $order;

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function savePurchaseOrder(Request $request){

        //Validar formulario
        $this->validateFormPurchaseOrder($request);

        //Proceder a la creación de orden de compra
        $mensaje = "Se ha creado correctamente.";
        $titulo_mensaje = "Orden de Compra creada!";
        //Obtener el área del usuario y el nombre de folio
        $area = Area::find(Auth::user()->id_area);

        $folio = $this->getFolioName($area);

        //Obtener el proveedor seleccionado (Formato: nombre proveedor - numero de contrato (descripcion contrato) )
        $div = explode("-", $request->name_provider);
        $contract_number = explode("(",  $div[1]); //Sacar la descripcion del contrato
        /** @var Contract $contract */
        $contract = Contract::getContractByProvider(trim($div[0]), trim($contract_number[0]));

        //Obtener la cuenta asociada
        $account = AccountContract::getAccountByAreaAndContract($contract->id_contract, $area->id_area);

        $folio_orden = [];

        try{

            DB::transaction(function() use ($request, $folio, $area, $contract,$folio_orden, $account)
            {
                $orders = json_decode($request->ordersByMonth); //Obtener el listado de las ordenes de compra por mes
                $count_orders=0;
                foreach ($orders as $key => $listOrderDetail) {
                    $count_orders++;
                    if(!empty($listOrderDetail)){

                        $folioByMonth = $folio . '_' . sprintf('%02d', $key);

                        //Crear orden maestro
                        /** @var PurchaseOrder $order */
                        $order = $this->createPurchaseOrder($request, $folioByMonth, $area, $contract);
                        $folio_orden[$count_orders] = $folioByMonth;
                        $order->save();

                        //Crear detalles de la orden maestro y actualizar montos disponibles en cuentas
                        $this->createPurchaseOrderDetails($listOrderDetail,$order,$folioByMonth, $area, $account);

                    }
                }

                //Validar que los presupuestos disponibles no queden en negativo
                if(!AccountBudget::validateBudgetAvailable($area->id_area, date('Y'), $account->account_code)
                || !AreasBudget::validateBudgetAvailable($area->id_area, date('Y'))){
                    throw new \Exception('Ha ocurrido un error. Los montos disponibles de la cuenta o el área han quedado en negativo');
                }


                $gerente_area = User::find($area->id_user);

                if(isset($_SERVER['SERVER_NAME'])){
                    self::sendMailPurchaseOrder('createOrder',$gerente_area->email, $folio_orden);
                }

            });


            Toastr::success($mensaje, $titulo_mensaje, [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);


        }catch (\Exception $e){

            //echo PHP_EOL.">>>>>>>>>>>>>> ERROR: ".$e->getMessage().PHP_EOL;

            Toastr::error("Ocurrió una excepción al crear la orden de compra." . $e->getMessage(), "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }
        
        return redirect('/consultarOrdenes');
    }

    public function getTotalActual($folio_number){
        $folio_number = PurchaseOrder::find($folio_number);
        $totales = ['total_price'=>$folio_number->total_price,'total_iva_price'=>$folio_number->total_iva_price];
        return response()->json($totales);
    }

    public function updatePurchaseOrder(Request $request){

        /** @var PurchaseOrder $order */
        $order = PurchaseOrder::findOrFail($request->purchase_order);

        $order_detail = PurchaseOrderDetail::getFirstOrderDetails($order->folio_number);

        $old_oc = false;

        //todo:validar las otras monedas, hacer conversion

        try{
            DB::transaction(function() use ($order,$request,$order_detail,$old_oc) {

                $total_order_old = (float)$order->total_price;
                $total_order_new = (float)$this->cleanTotal($request->total_sin_iva);
                $total_order_con_iva_new = (float)$this->cleanTotal($request->total);

                $currency = $order->id_currency;

                $today = getdate();

                //Cuenta de ordenes de compra viejas
                $old_account = "";

                if($order->old_folio_number == ""){
                    //Obtener la cuenta asociada
                    $account = AccountContract::getAccountByAreaAndContract($order->id_contract, $order->id_area);
                    $account_code = $account->account_code;
                }else{
                    //Obtengo la primera descripción
                    $first_description = trim($order_detail->description);
                    $account_code = explode(' ', $first_description)[0];
                    $old_oc = true;
                    $old_account = explode(' ',trim($order_detail->description))[0];
                }

                if($total_order_new > $total_order_old){
                    //Disminuimos del budget
                    $diferencia_total = $total_order_new - $total_order_old;
                    if($currency != "2"){
                        $total_order_old_clp = $this->currencyConverter($currency,Currency::PESO_CHILENO,$total_order_old);
                        $total_order_new_clp = $this->currencyConverter($currency,Currency::PESO_CHILENO,$total_order_new);
                        $diferencia_total = $total_order_new_clp - $total_order_old_clp;
                    }
                    AccountBudget::updateBudgetAvailable($order->id_area,$today['year'],$account_code,$diferencia_total);
                    //Actualizar monto disponible del área asociada a la orden
                    AreasBudget::updateBudgetAvailable($order->id_area, $today['year'], $diferencia_total);
                }else if ($total_order_new < $total_order_old){
                    //Aumentamos del budget
                    $diferencia_total = $total_order_old - $total_order_new;
                    if($currency != "2"){
                        $total_order_old_clp = $this->currencyConverter($currency,Currency::PESO_CHILENO,$total_order_old);
                        $total_order_new_clp = $this->currencyConverter($currency,Currency::PESO_CHILENO,$total_order_new);
                        $diferencia_total = $total_order_old_clp - $total_order_new_clp;
                    }
                    AccountBudget::restoreBudgetAvailable($order->id_area,$today['year'],$account_code,$diferencia_total);
                    //Actualizar monto disponible del área asociada a la orden
                    AreasBudget::restoreBudgetAvailable($order->id_area, $today['year'], $diferencia_total);
                }

                //Borramos los detalles antiguos
                PurchaseOrderDetail::deleteOrderDetails($order->folio_number);

                $new_details = json_decode($request->items);


                //Creamos los detalles nuevos
                foreach($new_details as $detail){
                    if($old_oc){
                        $form_account = explode(' ',trim($detail->description))[0];
                        if($form_account != $old_account){
                            throw new Exception(' Esta Orden de Compra requiere que mantenga el número de cuenta en sus descripciones.');
                        }
                    }
                    $orderDetail = new PurchaseOrderDetail();
                    $orderDetail->quantity          = (int)$detail->cantidad;
                    $orderDetail->description       = $detail->description;
                    $orderDetail->price             = (float)$this->cleanTotal($detail->valor_sin_iva);
                    $orderDetail->has_iva           = $detail->has_iva == "true" ? 1:0;
                    $orderDetail->price_iva         = (float)$this->cleanTotal($detail->valor_con_iva);
                    $orderDetail->id_currency       = $order->id_currency;
                    $orderDetail->id_purchase_order = $order->folio_number;
                    $orderDetail->save();
                }

                //Actualizamos los totales de la Orden de Compra
                $order->total_price = $total_order_new;
                $order->total_iva_price = $total_order_con_iva_new;
                $order->save();

                Toastr::success("Orden Modificada", "La Orden de Compra se ha modificado correctamente.", [
                    "positionClass" => "toast-top-right",
                    "progressBar" => true,
                    "closeButton" => true,
                ]);


            });
        }catch(Exception $exception){
            Toastr::error("Ocurrió una excepción al editar la orden de compra." . $exception->getMessage(), "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }

        return redirect('/consultarOrdenes');
    }

    public function createDetailForResume(Request $request, $count, $iniMonth, $endMonth){

        $orderDetail = new PurchaseOrderDetail();

        $currency = $request->currency;

        $price_float = floatval($this->cleanTotal($request['priceWithoutIva_'.$count] ));

        $orderDetail->quantity = $iniMonth == $endMonth ? $request['cant_'.$count] : 1 ;
        $orderDetail->description = $request['desc_'.$count];
        $orderDetail->price = $price_float * $orderDetail->quantity ;
        $orderDetail->has_iva = 0;
        if($request['iva_'.$count] == "on"){
            $price_iva_float = floatval($this->cleanTotal($request['priceWithIva_'.$count]));
            $orderDetail->price_iva = $price_iva_float * $orderDetail->quantity;
            $orderDetail->has_iva = 1;
        }
        $orderDetail->id_currency = $currency;

        return $orderDetail;

    }

    public function validatePurchaseOrders( Request $request ){

        $purchaseOrders = array('1'=>array(),'2'=>array(),'3'=>array(),'4'=>array(),'5'=>array(),'6'=>array(),'7'=>array(),'8'=>array(),'9'=>array(),'10'=>array(),'11'=>array(),'12'=>array());

        $len = $request->count_detail;
        for($count = 0; $count <= $len ;$count++){
            if($request->has('cant_'.$count)){

                $iniMonth = $request['month_ini_'.$count];
                $endMonth = $iniMonth;
                if($request['month_'.$count] == "on"){
                    $endMonth = $request['month_end_'.$count];
                }

                //Validar mes de inicio de orden de compra
                for($month = $iniMonth; $month<=$endMonth; $month++){

                    $orderDetail = $this->createDetailForResume($request, $count, $iniMonth, $endMonth);
                    array_push($purchaseOrders[$month],$orderDetail);
                }
            }
        }
        
        return response()->json($purchaseOrders);

    }
    
    public function getOrderDetail(Request $request, $id, $action = null){        

        try {

            /** @var PurchaseOrder $order */
            $order = PurchaseOrder::findOrFail($id);
            $mensaje = null;
            $areas = null;
            $gerente = false;

            if(User::needFilteringByArea() && !User::needFilteringByUser()){
                $gerente = true;
                $areas = $this->getAreasKeys(Area::getAreaByManager(Auth::user()->id_user));
            }

            //Validar permisologia dependiendo del usuario loggueado y la acción que se desea ejecutar
            if($action!=null && $action=="validate" ){

                if(User::needFilteringByUser()){
                    $mensaje = config('messages.rolNotAuthorized');
                }

                if($gerente && ! in_array(  $order->id_area, $areas) ){
                    $mensaje = config('messages.forbiddenAreas');
                }

            }else if($action!=null && $action == "print" ) {

                if( ($gerente && ! in_array(  $order->id_area, $areas))
                    || (User::needFilteringByUser() && Auth::user()->id_area != $order->id_area )){
                    $mensaje = config('messages.forbiddenAreasPrint');
                }

                if(User::needFilteringByUser() && Auth::user()->id_user != $order->id_user){
                    $mensaje = config('messages.forbiddenOCPrint');
                }

            }else if($action == null){

                if( ($gerente && ! in_array(  $order->id_area, $areas))
                    || (User::needFilteringByUser() && Auth::user()->id_area != $order->id_area )){
                    $mensaje = config('messages.forbiddenAreasConsult');
                }

                if(User::needFilteringByUser() && Auth::user()->id_user != $order->id_user){
                    $mensaje = config('messages.forbiddenOCConsult');
                }
            }

            if(Auth::user()->id_area == null ){
                $mensaje = config('messages.areaNotAssigned');
            }else if(count(Auth::user()->roles()->get())== 0 ){
                $mensaje = config('messages.rolNotAssigned');
            }

            if($mensaje != null){
                Session::flash('error_message', $mensaje);
                return redirect(route('home'));
            }

            //Determinar si voy a ver el detalle de OC desde la pantalla de búsqueda avanzada
            //y si es asi, concatenar a la ruta un parámetro de busqueda
            $url = Session::get('opcion_consulta_OC');
            $patron = Session::get('patronBusquedaAvanzada');
            if($url != null && $url != "" && $url == "filtrarOrdenes" && $patron != null && $patron != ""){
                Session::set('opcion_consulta_OC', 'filtrarOrdenes/'.$patron);
            }


            $area = Area::findOrFail($order->id_area);
            /** @var Contract $contract */
            $contract= Contract::findOrFail($order->id_contract);
            $provider = Provider::findOrFail($contract->id_provider);
            $method = PaymentMethod::findOrFail($order->id_payment_method);
            $condition = PaymentCondition::findOrFail($order->id_payment_condition);
            $details = PurchaseOrderDetail::getOrderDetails($id);
            $currency = Currency::findOrFail($order->id_currency);
            

            return view('purchaseOrder.orderDetails', [ 'order' => $order, 'id' => $id,
                                                        'provider' => $provider,
                                                        'contract' => $contract,
                                                        'method' => $method,
                                                        'condition' => $condition,
                                                        'currency' => $currency,
                                                        'area' => $area,
                                                        'details' => $details,
                                                        'action' => $action]);

        } catch(\Exception $e){
            return false;
        }
    }

    public function approvePurchaseOrder(Request $request, $id){

        /** @var PurchaseOrder $order */
        $order = PurchaseOrder::findOrFail($id);

        if($order->order_state != config('constants.emitida')){
            return false;
        }

        $order->order_state = config('constants.aprobada');

        $user_order = User::find($order->id_user);
        $email_user = $user_order->email;
        $order_number = $id;

        try {
            $order->save();

            // EMAIL
            self::sendMailPurchaseOrder('approveOrder', $email_user, $order_number);

        } catch (\Exception $e) {
            return false;
        }

        return "OK";

    }

    public static function restoreBudget($order, $account_code){

        //Convertir el monto a CLP usando la tasa obtenida antes
        $total_CLP = $order->total_price * $order->exchange_rate;
        $today = getdate();

        //Actualizar monto disponible de la cuenta asociada a la orden
        AccountBudget::restoreBudgetAvailable($order->id_area, $today['year'],$account_code,  $total_CLP);

        //Actualizar monto disponible del área asociada a la orden
        AreasBudget::restoreBudgetAvailable($order->id_area, $today['year'], $total_CLP);

    }
    
    public function rejectPurchaseOrder(Request $request, $id){

        /** @var PurchaseOrder $order */
        $order = PurchaseOrder::findOrFail($id);

        if($order->order_state != config('constants.emitida')){
            return false;
        }

        $order->order_state = config('constants.rechazada');

        $user_order = User::find($order->id_user);
        $email_user = $user_order->email;
        $order_number = $id;
        try {
            $order->save();

            //Restaurar monto al presupuesto disponible de la cuenta y del área

            //Obtener la cuenta asociada
            $account = AccountContract::getAccountByAreaAndContract($order->id_contract, $order->id_area);
            $this->restoreBudget($order, $account->account_code);

            //Eliminar posibles relaciones de la OC con facturas
            InvoicesOrders::deleteOrdersByOCId($order->folio_number);

            // EMAIL
            self::sendMailPurchaseOrder('rejectOrder',$email_user,$order_number,$request->mensajeRechazo);


        } catch (\Exception $e) {
            return false;
        }

        return "OK";
    }

    public function currencyConverter($moneda_origen,$moneda_destino,$cantidad){

        if($moneda_origen == Currency::PESO_CHILENO) {
            $valor = $cantidad;
        } else {
            $valor = Currency::conversorMoneda($moneda_origen,$moneda_destino,$cantidad);
        }

        return $valor;
    }

    public function validateAreaBudget(Request $request){
        $respuesta = true;
        $moneda_destino = $request->moneda_destino;
        $cantidad       = $request->cantidad;
        $id_area        = $request->id_area;
        //Obtener el proveedor seleccionado
        $div = explode("-", $request->proveedor);
        $contract_number = explode("(",  $div[1]); //Sacar la descripcion del contrato
        /** @var Contract $contract */
        $contract = Contract::getContractByProvider(trim($div[0]), trim($contract_number[0]));

        $valor = $this->currencyConverter($request->moneda_origen, $moneda_destino, $cantidad);

        $year = date('Y');


        if($request->action == "edit"){

            $order = PurchaseOrder::findBy('folio_number',$request->order);
            // validar si es edicion o nueva OC
            if($order->old_folio_number == ""){
                //Obtener la cuenta asociada
                $account = AccountContract::getAccountByAreaAndContract($contract->id_contract,$id_area);
                $account_code = $account->account_code;
            }else{
                //Obtengo la primera descripción
                $first_description = trim($request->first_item_description);
                $account_code = explode(' ', $first_description)[0];
            }
        }else{
            //Obtener la cuenta por área y contrato
            /** @var AccountContract $account */
            $account = AccountContract::getAccountByAreaAndContract($contract->id_contract,$id_area );
            $account_code = $account->account_code;
        }


        $budget = AccountBudget::getBudget($id_area,$year, $account_code);
        $monto_area = $budget->total_budget_available;

        if($valor > $monto_area){
            $respuesta = false;
        }

        return json_encode($respuesta);
    }

    public static function sendMailPurchaseOrder($type, $email, $folio_order,$mensaje_rechazo=null)
    {
        $cabeceras  = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From: Sistema de Ordenes de Compra <noreply@yapo.cl>' . "\r\n";
        $asunto  = null;
        $mensaje = null;
        $server_name = null;

        $server_name = (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : 'local.ordenescompra.cl';

        switch ($type)
        {
            case 'createOrder':
                $asunto = 'Orden de Compra Registrada';
                $mensaje = ViewMails\ViewMail::OcCreada($folio_order,$server_name);
                break;
            case 'approveOrder':
                $asunto = 'Su Orden de Compra '.$folio_order.' ha sido aprobada.';
                $mensaje = ViewMails\ViewMail::OcAprobada($folio_order,$server_name);
                break;
            case 'rejectOrder':
                $mensaje = ViewMails\ViewMail::OcRechazada($folio_order,$server_name,$mensaje_rechazo);
                $asunto = 'Su Orden de Compra '.$folio_order.' ha sido rechazada.';
                break;
        }

        mail($email, $asunto, $mensaje,$cabeceras);

    }

    public function deletePurchaseOrder(Request $request){

        /** @var PurchaseOrder $order */
        $order = PurchaseOrder::findBy('folio_number', $request->idOrden);

        try {

            if($order == null){ return false; }
            if($order->order_state == config('constants.rechazada')){ return false; }

            DB::transaction(function() use ( $order) {

                $account_code = "";

                $order->is_visible = 0;
                $order->order_state = config('constants.eliminada');
                $order->save();

                if($order->old_folio_number != null && !(stristr($order->folio_number, '_111_') === FALSE)){

                    //Actualizar presupuestos de ordenes viejas
                    $details = PurchaseOrderDetail::getOrderDetails($order->folio_number);
                    $desc = trim($details[0]->description);
                    $div = explode(" ", $desc);
                    $account_code =  str_replace(".", "", $div[0]);
                    
                } else {
                    
                    //Obtener la cuenta asociada
                    $account = AccountContract::getAccountByAreaAndContract($order->id_contract, $order->id_area);
                    $account_code = $account->account_code;
                }

                //Actualizar presupuestos
                $this->restoreBudget($order, $account_code);

                //Eliminar posibles relaciones de la OC con facturas
                InvoicesOrders::deleteOrdersByOCId($order->folio_number);

            });

        } catch (\Exception $e) {
            return false;
        }

        return "OK";

    }

    public function editPurchaseOrder(Request $request,$id){

        if(Auth::user()->id_area == null){
            Session::flash('error_message', config('messages.areaNotAssignedForEditOC'));
            return redirect(route('home'));
        }

        $currencies = Currency::pluck('name_currency','id_currency');
        $payment_methods = PaymentMethod::getPaymentMethod();
        $payment_conditions = PaymentCondition::getPaymentConditions();

        /** @var PurchaseOrder $order */
        $order = PurchaseOrder::findOrFail($id);

        $area = Area::findOrFail($order->id_area);
        /** @var Contract $contract */
        $contract= Contract::findOrFail($order->id_contract);
        $provider = Provider::findOrFail($contract->id_provider);
        $method = PaymentMethod::findOrFail($order->id_payment_method);
        $condition = PaymentCondition::findOrFail($order->id_payment_condition);
        $details = PurchaseOrderDetail::getOrderDetails($id);
        $currency = Currency::findOrFail($order->id_currency);


        $total_price = $order->total_price;


        return view('purchaseOrder.editPurchaseOrder',[
            'currencies' => $currencies,
            'methods'    => $payment_methods,
            'conditions' => $payment_conditions,
            'order'      => $order,
            'id'         => $id,
            'provider'   => $provider,
            'contract'   => $contract,
            'method'     => $method,
            'condition'  => $condition,
            'currency'   => $currency,
            'area'       => $area,
            'details'    => $details,
            'total_price'=> $total_price,
        ]);
    }

    public function filterOrders($patron = null)
    {
        if(Auth::user()->id_area == null){
            Session::flash('error_message', config('messages.areaNotAssignedForAdvancedSearch'));
            return redirect(route('home'));
        }

        //Limpiar el parámetro de búsqueda cuando no se envie parámetro
        if($patron == null || $patron == ""){
            Session::set('patronBusquedaAvanzada', null);
        }

        Session::set('opcion_consulta_OC', 'filtrarOrdenes');

        return view('purchaseOrder.filterPurchaseOrder', [[]]);
    }
    
    public function filterPurchaseOrder(Request $request){


        $len = $request->length;
        $start = $request->start;
        $search = null;
        $orderby = null;
        $dir = null;
        $filterArea = null;


        $user = Auth::user()->id_user;
        $area = Area::getAreaByManager($user);


        if(User::needFilteringByArea() && !User::needFilteringByUser()){ //Rol Gerencia
            $filterArea = $this->getAreasKeys($area);
        }


        if($request->search['value'] && strlen($request->search['value']) >= 3) {
            $search = $request->search['value'];
            Session::set('patronBusquedaAvanzada', $search);
        }

        if($request->order[0]){
            if($request->draw!=1) {
                $orderby = $request->order[0]['column'];
                $dir = $request->order[0]['dir'];
            }
        }

        $count = PurchaseOrder::getCountFilteredPurchaseOrders($search, $filterArea);

        $results = PurchaseOrder::FilteredPurchaseOrders($start,$len, $search, $orderby, $dir, $filterArea);

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

    public function getOCDetailsFromAdvancedSearch($id){

        $details = PurchaseOrderDetail::getOrderDetails($id);
        $data = array();

        $i = 0;
        foreach($details as $detail){
            $detail->price = number_format($detail->price, 2, ',', '.');
            $data[$i] = $detail;
//            $data[$i] = ['contract_number'=>$provider->contract_number];
            $i++;
        }

        return response()->json($data);

    }

    public function filterPurchaseOrderToBill(Request $request){


        $len = $request->length;
        $start = $request->start;
        $search = null;
        $orderBy = null;
        $dir = null;
        $filterArea = null;
        $filterMonthIni = $filterMonthEnd = null;

        $user = Auth::user()->id_user;
        $area = Area::getAreaByManager($user);


        if(User::needFilteringByArea() && !User::needFilteringByUser()){ //Rol Gerencia
            $filterArea = $this->getAreasKeys($area);
        }


        if($request->search['value'] ) {
            $search = $request->search['value'];
        }

        if($request->columns[5]['search']['value']){
            $div = explode(",", $request->columns[5]['search']['value']);
            $filterMonthIni = $div[0] == "" ? null: $div[0];
            $filterMonthEnd = $div[1] == "" ? null: $div[1];
        }

        if($request->order[0]){
            if($request->draw!=1) {
                $orderBy = $request->order[0]['column'];
                $dir = $request->order[0]['dir'];
            }
        }

        $count = PurchaseOrder::getCountPurchaseOrderToBill($search, $filterArea, $filterMonthIni, $filterMonthEnd);

        $results = PurchaseOrder::PurchaseOrderToBill($start,$len, $search, $orderBy, $dir, $filterArea, $filterMonthIni, $filterMonthEnd);

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

}

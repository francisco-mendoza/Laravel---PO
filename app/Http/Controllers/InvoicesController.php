<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Models\InvoicesOrders;
use App\Models\Provider;
use App\Models\PurchaseOrder;
use App\Models\Currency;
use App\Models\Area;
use App\Models\Month;
use Toastr;
use Session;
use DB;

class InvoicesController extends Controller
{
    //
    public function __construct()
    {

        $this->middleware('permission:ver_invoices.index',['only' => ['index', 'show']]);
        $this->middleware('permission:crear_facturas',['only' => ['create', 'store']]);
        $this->middleware('permission:editar_facturas',['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar_facturas',['only' => ['destroy']]);
    }


    public function index(Request $request)
	{
	    return view('invoices.index', []);
	}

	public function pending(Request $request){
        return view('invoices.pending', []);
    }

	public function create(Request $request)
	{
        $currencies = Currency::pluck('name_currency','id_currency');
        $areas = Area::getAreasOption();

        $months = Month::getMonths();

        $count_month = 1;
        $array_months = [];
        foreach ($months as $month){
            $array_months[$count_month] = $month->name_month;
            $count_month = $count_month + 1;
        }

        $array_year = [
            date("Y",strtotime("-1 year")) => date("Y",strtotime("-1 year")),
            date("Y") => date("Y"),
            date("Y",strtotime("+1 year")) => date("Y",strtotime("+1 year")),
        ];

        return view('invoices.add', [
            'currencies' => $currencies,
            'areas' => $areas,
            'months' => $array_months,
            'years' => $array_year,
        ]);
	}

	public function formatTotal($invoice){

        if($invoice->id_currency == 2){
            $invoice->total = number_format($invoice->total,0,",",".");
            $invoice->total_iva = number_format($invoice->total_iva,0,",",".");
        }else{
            //Para otras monedas dejamos solo reemplazamos el punto por coma
            $invoice->total = number_format(floatval($invoice->total), 2,",", ".");
            $invoice->total_iva = number_format(floatval($invoice->total_iva), 2,",", ".");
        }

        return $invoice;
    }

	public function edit(Request $request, $id)
	{
        $currencies = Currency::pluck('name_currency','id_currency');
        $areas = Area::getAreasOption();
        $months = Month::getMonths();

        $count_month = 1;
        $array_months = [];
        foreach ($months as $month){
            $array_months[$count_month] = $month->name_month;
            $count_month = $count_month + 1;
        }

        $array_year = [
            date("Y",strtotime("-1 year")) => date("Y",strtotime("-1 year")),
            date("Y") => date("Y"),
            date("Y",strtotime("+1 year")) => date("Y",strtotime("+1 year")),
        ];

        /** @var Invoice $invoice */
		$invoice = Invoice::findOrFail($id);

        $invoice = $this->formatTotal($invoice);

        $provider = Provider::find($invoice->id_provider);

        $select_area = $invoice->areas()->first();

        $min_total = InvoicesOrders::getTotalInvoiced($id); //Obtener el monto asignado a OC hasta el momento

	    return view('invoices.add', [
	        'model' => $invoice,
            'provider' => $provider,
            'currencies' => $currencies,
            'areas' => $areas,
            'months' => $array_months,
            'years' => $array_year,
            'select_area' => $select_area,
            'min_total' => $min_total,
        ]);
	}

	public function show(Request $request, $id)
	{
	    /** @var Invoice $invoice */
		$invoice = Invoice::findOrFail($id);

		$provider = Provider::find($invoice->id_provider);

		$month = Month::find($invoice->billing_month);

		$currency = Currency::find($invoice->id_currency);

		$area = $invoice->areas()->first();

        $orders = InvoicesOrders::findPurchaseOrdersByInvoiceId($id);

	    return view('invoices.show', [
	        'model' => $invoice,
            'provider' => $provider,
            'month' => $month,
            'currency'=>$currency,
            'orders' => $orders,
            'area' => $area
        ]);
	}

	public function grid(Request $request)
	{
        return $this->findInvoices($request);

	}

	public function pendingGrid(Request $request){

	    $pending = true; //Obtener las facturas pendientes

        return $this->findInvoices($request, $pending);
    }

	public function findInvoices(Request $request, $pending = false){

        $len = $request->length;
        $start = $request->start;
        $search = $orderBy = $dir = null;

        if($request->search['value']) {
            $search = $request->search['value'];
        }
        if($request->order[0]){
            $orderBy = $request->order[0]['column'];
            $dir = $request->order[0]['dir'];
        }

        $count = Invoice::getCountInvoice($search, $pending);

        $results = Invoice::getInvoices($start,$len, $search, $orderBy, $dir, $pending);


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

        echo json_encode($ret);
    }

    public function addPurchaseOrder(Request $request, $id){

        $invoice = Invoice::find($id);
	    $provider = Provider::find($invoice->id_provider);
	    $currency = Currency::find($invoice->id_currency);
	    $orders = InvoicesOrders::getPurchaseOrdersByInvoiceId($id);
        $countOrders = count($orders);

        $invoice = $this->formatTotal($invoice);


        return view('invoices.addOC', [
            'invoice' => $invoice,
            'provider' => $provider,
            'currency' => $currency,
            'count' => $countOrders,
            'orders' => $orders
        ]);

    }

    public function validateFormInvoices(Request $request){

        $rules = [
            'id_document' => 'required|max:50',
            'id_provider' => 'required',
        ];

        $niceNames = [
            'id_document' => 'Número de Factura',
            'id_provider' => 'Nombre de Proveedor',
        ];
        $this->validate($request, $rules ,[],$niceNames);

    }


	public function update(Request $request) {

        //Validar formulario
        $this->validateFormInvoices($request);

		$invoice = null;
		$edit = false;

        if($request->method() == 'POST'){
            $invoice = new Invoice;
            $mensaje = "Se ha creado correctamente.";
            $titulo_mensaje = "Factura creada!";
        }else{
            $invoice = Invoice::findOrFail($request->id_invoice);
            $mensaje = "Se ha modificado correctamente.";
            $titulo_mensaje = "Factura modificada!";
            $edit = true;
        }


		$provider = Provider::findBy('name_provider',$request->id_provider);

        //Solo si es una factura nueva se podrá ingresar el id_document
        if(!$edit){
            $invoice->id_document = $request->id_document;
        }
        $invoice->id_provider = $provider->id_provider;
        $invoice->billing_month = $request->billing_month;
        $invoice->billing_year = $request->billing_year;
        $invoice->billing_day = $request->billing_day;
        $invoice->total = $this->cleanTotal($request->total);
        $invoice->total_iva = $this->cleanTotal($request->total_impuesto);
        $invoice->id_currency = $request->currency;

        try{
            DB::transaction(function() use ($request, $invoice,$edit) {
                InvoicesOrders::deleteAreasInvoices($request->id_invoice);
                $invoice->save();
                $invoice->areas()->attach($request->id_area);
            });
            Toastr::success($mensaje, $titulo_mensaje, [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);

        }catch (\Exception $e ){
            Toastr::error("Ocurrió una excepción al crear/modificar la factura: " . $e->getMessage() , "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }

	    return redirect('/invoices');

	}

	public function assignPurchaseOrders(Request $request, $id){

        try {

            $orders = InvoicesOrders::findPurchaseOrdersIdsByInvoiceId($id);
            DB::transaction(function() use ( $request, $orders, $id) {

                $total = 0;
                $total_invoice = Invoice::findBy('id_invoice', $id)->total;
                foreach($request['subtotal'] as $key => $subtotal){
                    $order = null;
                    $total = $total + floatval($this->cleanTotal($subtotal));

                    if(in_array($key, $orders)){
                        $calculated = $this->cleanTotal($request['calculated'][$key]);

                        InvoicesOrders::updateInvoiceOrder($id,$key,$this->cleanTotal($subtotal),$this->cleanTotal($request['rate'][$key]),$calculated);
                        //Eliminar orden del arreglo
                        unset($orders[array_Search($key,$orders)]);

                    }else{
                        $order = new InvoicesOrders();
                        $order->id_invoice = $id;
                        $order->id_purchase_order = $key;
                        $order->subtotal = $this->cleanTotal($subtotal);
                        $order->exchange_rate = $this->cleanTotal($request['rate'][$key]);
                        $order->subtotal_po_currency = $this->cleanTotal($request['calculated'][$key]);
                        $order->save();
                    }

                }

                if($total > $total_invoice){
                    throw new \Exception(' El monto de la factura fue sobrepasado en la asignación a las Ordenes de Compra.');
                }

                //Se permitirán eliminar ordenes de las que estaban seleccionadas?
                Toastr::success("Factura Modificada", "Las asignaciones de la factura se han modificado correctamente.", [
                    "positionClass" => "toast-top-right",
                    "progressBar" => true,
                    "closeButton" => true,
                ]);
            });

        } catch (\Exception $e) {
            Toastr::error("Ocurrió una excepción al editar la factura.", "Ocurrió un error", [
                "positionClass" => "toast-top-right",
                "progressBar" => true,
                "closeButton" => true,
            ]);
        }

        return redirect('/invoices/pending');
    }



	public function store(Request $request)
	{
		return $this->update($request);
	}

	public function destroy($id) {

        /** @var Invoice $invoice */
		$invoice = Invoice::findOrFail($id);

        try {
            InvoicesOrders::deleteOrdersByInvoiceId($id);
            $invoice->areas()->detach();
            $invoice->delete();
        }catch(\Exception $e){
            return false;
        }

		return "OK";
	    
	}

	public function convertInvoiceCurrency(Request $request) {
        $order = PurchaseOrder::findBy('folio_number',$request->id_order);

        $value_rate = Currency::conversorMoneda($request->invoice_currency,$order->id_currency,1);

        $value_rate = number_format($value_rate,4,',','.');

        return json_encode($value_rate);

    }




	
}
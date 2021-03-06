<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\InvoicesController;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Area;
use App\Models\InvoicesOrders;
use App\Models\PurchaseOrder;
use App\Models\Contract;

use Page\TestData;


class InvoicesUnitTest extends TestCase
{

    /** @var  InvoicesController */
    protected $controller;

    public $invoiceFields;
    public $areaFields;
    public $purchaseOrderFields;

    /** @var  Request */
    protected $request;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        //Setear usuario
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        //Inicializar el controlador a probar
        $this->controller = new InvoicesController();

        $this->invoiceFields = TestData::invoiceFields;

        $this->areaFields = TestData::areaFields;

        $this->purchaseOrderFields = TestData::purchaseOrderFields;

        //Parámetros mínimos del request
        $this->resetRequest();


    }

    public function tearDown(){
        Session::clear();
    }

    public function resetRequest(){
        //Parámetros mínimos del request
        $this->request = new Request();
    }

    public function createAreaForTest(){
        $area = new Area($this->areaFields);
        $area->save();
    }

    public function testListInvoicesWithoutRolAssigned(){

        Session::start();

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);
        $user->detachRoles();

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/invoices');
        $this->assertRedirectedToRoute("home");
        $this->assertArrayHasKey('error_message',Session::all(),"No tienes permiso para ver esa área");


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testListInvoicesWithoutRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testListInvoicesWithRolAssigned(){


        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/invoices');

        $this->see('Listado de Facturas');
        $this->see('Agregar Factura');
        $this->see('N° Factura');
        $this->see('Proveedor');
        $this->see('Total');

        $user->detachRoles();
        $user->attachRole(config('constants.gerencia'));


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testListInvoicesWithRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }


    public function testCreateInvoice(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/invoices/create');

        $this->see('Agregar/Editar Factura');
        $this->see('Proveedor');
        $this->seeElement('input', ['name' => 'id_provider']);
        $this->see('Area');
        $this->seeElement('select', ['name' => 'id_area']);
        $this->see('N° Factura');
        $this->seeElement('input', ['name' => 'id_document']);
        $this->see('Periodo de Facturación');
        $this->seeElement('select', ['name' => 'billing_day']);
        $this->seeElement('select', ['name' => 'billing_month']);
        $this->seeElement('select', ['name' => 'billing_year']);
        $this->see('Tipo Moneda');
        $this->seeElement('select', ['name' => 'currency']);
        $this->see('Total');
        $this->seeElement('input', ['name' => 'total']);
        $this->see('Total c/Impuesto');
        $this->seeElement('input', ['name' => 'total_impuesto']);


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testCreateInvoice "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function saveInvoiceForSearch(){
        $this->createAreaForTest();
        $invoice = new Invoice($this->invoiceFields);
        $invoice->save();
        $invoice->areas()->attach($this->invoiceFields['id_area']);

    }

    public function seeInvoiceInPage(){
        $this->see('Proveedor');
        $this->see('Area');
        $this->see('N° Factura');
        $this->seeInField('#id_document', $this->invoiceFields['id_document']);
        $this->see('Periodo de Facturación');
        $this->seeIsSelected('#billing_month', $this->invoiceFields['billing_month']);
        $this->seeIsSelected('#billing_year', $this->invoiceFields['billing_year']);
        $this->see('Tipo Moneda');
        $this->seeIsSelected('#currency', $this->invoiceFields['id_currency']);
        $this->see('Total');
        $this->seeInField('#total', '100');
        $this->see('Total c/Impuesto');
        $this->seeInField('#total_impuesto', '119');
    }

    public function testEditInvoice(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveInvoiceForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        //$this->click('Facturas');
        //$this->click('Agregar Factura');
        $this->call('GET', '/invoices/');

        $this->see("Listado de Facturas");

        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->call('GET', '/invoices/'. $invoice->id_invoice .'/edit');



        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();


        $this->see('Agregar/Editar Factura');
        $this->seeInvoiceInPage();

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testEditInvoice "."\033[32m OK \033[0m ".PHP_EOL );

    }


    public function testCreateStoreInvoice(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->createAreaForTest();

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["id_area"] = $this->invoiceFields['id_area'];
        $this->request["id_document"] = $this->invoiceFields['id_document'];
        $this->request["billing_day"] = $this->invoiceFields['billing_day'];
        $this->request["billing_month"] = $this->invoiceFields['billing_month'];
        $this->request["billing_year"] = $this->invoiceFields['billing_year'];
        $this->request["currency"] = $this->invoiceFields['id_currency'];
        $this->request["total"] = "100";
        $this->request["total_impuesto"] = "119";
        $this->request->setMethod('POST');

        $this->controller->store($this->request);

        $this->seeInDatabase('invoices', [
            'id_document'=>$this->invoiceFields['id_document']
        ]);

        $invoice = Invoice::findBy('id_document', $this->invoiceFields['id_document']);
        $this->seeInDatabase('invoices_areas', [
            'id_invoice'=>$invoice->id_invoice,
            'id_area'=>$this->invoiceFields['id_area']
        ]);

        //$invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);
        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testCreateStoreInvoice "."\033[32m OK \033[0m ". "\n");
    }

    public function testDestroyInvoice(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveInvoiceForSearch();

        $this->seeInDatabase('invoices', [
            'id_document'=>$this->invoiceFields['id_document']
        ]);
        $invoice = Invoice::findBy('id_document', $this->invoiceFields['id_document']);
        $invoice_id = $invoice->id_invoice;
        $this->seeInDatabase('invoices_areas', [
            'id_invoice'=>$invoice_id,
            'id_area'=>$this->invoiceFields['id_area']
        ]);

        $delete = $this->controller->destroy($invoice_id);

        $this->assertTrue($delete == 'OK', "Borrado");

        $this->dontSeeInDatabase('invoices', [
            'id_document'=>$this->invoiceFields['id_document']
        ]);
        $this->dontSeeInDatabase('invoices_areas', [
            'id_invoice'=>$invoice_id,
            'id_area'=>$this->invoiceFields['id_area']
        ]);

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testDestroyInvoice "."\033[32m OK \033[0m ". "\n");


    }

    public function testUpdateInvoice() {
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveInvoiceForSearch();

        //Modificar factura
        $invoice = Invoice::findBy('id_document', $this->invoiceFields['id_document']);

        $id_invoice = $invoice->id_invoice;

        $this->request["id_invoice"]     = $id_invoice;
        $this->request["id_provider"]    = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["id_area"]        = $this->invoiceFields['id_area'];
        $this->request["id_document"]    = $this->invoiceFields['id_document'];
        $this->request["billing_day"]    = $this->invoiceFields['billing_day'];
        $this->request["billing_month"]  = $this->invoiceFields['billing_month'];
        $this->request["billing_year"]   = $this->invoiceFields['billing_year'];
        $this->request["currency"]       = $this->invoiceFields['id_currency'];
        $this->request["total"]          = "200";
        $this->request["total_impuesto"] = "219";
        $this->request->setMethod('PATCH');//Edit

        $this->controller->update($this->request);

        $this->seeInDatabase('invoices', [
            'id_document'=>$this->invoiceFields['id_document']
        ]);

        $this->seeInDatabase('invoices_areas', [
            'id_invoice'=>$invoice->id_invoice,
            'id_area'=>$this->invoiceFields['id_area']
        ]);


        //$invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);
        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testUpdateInvoice "."\033[32m OK \033[0m ". "\n");
    }

    public function savePurchaseOrderForSearch(){

        $contract = new Contract(TestData::contractFields);
        $contract['id_contract'] = $this->purchaseOrderFields['id_contract'];
        $contract->save();

        $order = new PurchaseOrder($this->purchaseOrderFields);
        $order->save();
    }

    public function deletePurchaseOrderForSearch(){

        $order = PurchaseOrder::findBy('folio_number', $this->purchaseOrderFields['folio_number']);

        $order->delete();
        $contract = Contract::findBy('id_contract', $this->purchaseOrderFields['id_contract']);
        $contract->delete();
    }

    public function addInvoiceAndAssignationToPurchaseOrder($subtotal = "50 $", $rate = 1, $sub_currency = "50 $"){
        //Agregar la factura
        $this->saveInvoiceForSearch();
        unset($this->invoiceFields['id_area']);

        //Agregar la orden de compra a asociar a la factura
        $this->savePurchaseOrderForSearch();

        $this->request["subtotal"]= [$this->purchaseOrderFields['folio_number']=> $subtotal];
        $this->request["rate"] = [$this->purchaseOrderFields['folio_number']=> $rate];
        $this->request["calculated"] = [$this->purchaseOrderFields['folio_number']=>$sub_currency];
    }


    public function testAssignPurchaseOrderToInvoice(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->addInvoiceAndAssignationToPurchaseOrder();
        $this->seeInDatabase('invoices', $this->invoiceFields);

        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        $expected = ['id_invoice' => $invoice->id_invoice,
                        'id_purchase_order' => $this->purchaseOrderFields['folio_number'],
                    'subtotal' => '50.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '50.00'];

        $this->seeInDatabase('invoices_orders', $expected);

        //Eliminar datos
        InvoicesOrders::deleteOrdersByInvoiceId($invoice->id_invoice);
        $this->deletePurchaseOrderForSearch();


        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testAssignPurchaseOrderToInvoice "."\033[32m OK \033[0m ". "\n");

    }

    public function testEditAssignationToPurchaseOrder(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->addInvoiceAndAssignationToPurchaseOrder();
        $this->seeInDatabase('invoices', $this->invoiceFields);

        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        $expected = ['id_invoice' => $invoice->id_invoice,
            'id_purchase_order' => $this->purchaseOrderFields['folio_number'],
            'subtotal' => '50.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '50.00'];

        $this->seeInDatabase('invoices_orders', $expected);



        //Modificar los datos de la orden de compra asignada a la factura
        $this->request["subtotal"]= [$this->purchaseOrderFields['folio_number']=> "75 $"];
        $this->request["rate"] = [$this->purchaseOrderFields['folio_number']=> "1"];
        $this->request["calculated"] = [$this->purchaseOrderFields['folio_number']=>"75 $"];

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        $expected = ['id_invoice' => $invoice->id_invoice,
            'id_purchase_order' => $this->purchaseOrderFields['folio_number'],
            'subtotal' => '75.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '75.00'];

        $this->seeInDatabase('invoices_orders', $expected);

        //Eliminar datos
        InvoicesOrders::deleteOrdersByInvoiceId($invoice->id_invoice);
        $this->deletePurchaseOrderForSearch();


        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testEditAssignationToPurchaseOrder "."\033[32m OK \033[0m ". "\n");


    }

    public function testAddAssignationToPurchaseOrderToExceedInvoiceTotal()
    {
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->addInvoiceAndAssignationToPurchaseOrder("101 $", null, "101 $");
        $this->seeInDatabase('invoices', $this->invoiceFields);


        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        $this->dontSeeInDatabase('invoices_orders', ['id_invoice' => $invoice->id_invoice,
                                                            'id_purchase_order' => $this->purchaseOrderFields['folio_number']]);

        //Eliminar datos
        InvoicesOrders::deleteOrdersByInvoiceId($invoice->id_invoice);
        $this->deletePurchaseOrderForSearch();


        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testAddAssignationToPurchaseOrderToExceedInvoiceTotal "."\033[32m OK \033[0m ". "\n");

    }

    public function testEditAssignationToPurchaseOrderToExceedInvoiceTotal(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->addInvoiceAndAssignationToPurchaseOrder();
        $this->seeInDatabase('invoices', $this->invoiceFields);

        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);



        //Modificar los datos de la orden de compra asignada a la factura
        $this->request["subtotal"]= [$this->purchaseOrderFields['folio_number']=> "175 $"];
        $this->request["rate"] = [$this->purchaseOrderFields['folio_number']=> "1"];
        $this->request["calculated"] = [$this->purchaseOrderFields['folio_number']=>"175 $"];

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        $expected = ['id_invoice' => $invoice->id_invoice,
            'id_purchase_order' => $this->purchaseOrderFields['folio_number'],
            'subtotal' => '175.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '175.00'];

        $this->dontSeeInDatabase('invoices_orders', $expected);
        $expected['subtotal'] = "50.00";
        $expected['subtotal_po_currency'] = "50.00";
        $this->seeInDatabase('invoices_orders', $expected);

        //Eliminar datos
        InvoicesOrders::deleteOrdersByInvoiceId($invoice->id_invoice);
        $this->deletePurchaseOrderForSearch();

        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testEditAssignationToPurchaseOrderToExceedInvoiceTotal "."\033[32m OK \033[0m ". "\n");


    }

    public function testAddManyAssignationsToPurchaseOrders(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Agregar la factura
        $this->saveInvoiceForSearch();
        unset($this->invoiceFields['id_area']);

        //Agregar la orden de compra a asociar a la factura
        $this->savePurchaseOrderForSearch();
        $OLD_FOLIO_NUMBER = $this->purchaseOrderFields['folio_number'];
        $this->purchaseOrderFields['folio_number'] = 'TEST_VARIAS_OC_789';
        $order = new PurchaseOrder($this->purchaseOrderFields);
        $order->save();

        $this->request["subtotal"]= [$OLD_FOLIO_NUMBER=> "50 $", $this->purchaseOrderFields['folio_number']=> "40 $"];
        $this->request["rate"] = [$OLD_FOLIO_NUMBER=> "1", $this->purchaseOrderFields['folio_number']=> "1"];
        $this->request["calculated"] = [$OLD_FOLIO_NUMBER=>"50 $",$this->purchaseOrderFields['folio_number']=> "40 $"];

        $invoice = Invoice::findBy('id_document',$this->invoiceFields['id_document']);

        $this->controller->assignPurchaseOrders($this->request, $invoice->id_invoice);

        //Se encuentran ambas OC asignadas
        $expected = ['id_invoice' => $invoice->id_invoice,'id_purchase_order' => $OLD_FOLIO_NUMBER,
                     'subtotal' => '50.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '50.00'];
        $this->seeInDatabase('invoices_orders', $expected);

        $expected = ['id_invoice' => $invoice->id_invoice,
            'id_purchase_order' => $this->purchaseOrderFields['folio_number'],
            'subtotal' => '40.00', 'exchange_rate' => '1', 'subtotal_po_currency' => '40.00'];
        $this->seeInDatabase('invoices_orders', $expected);

        //Eliminar datos
        InvoicesOrders::deleteOrdersByInvoiceId($invoice->id_invoice);
        $order = PurchaseOrder::findBy('folio_number',$OLD_FOLIO_NUMBER);
        $order->delete();
        $this->deletePurchaseOrderForSearch();


        $invoice->areas()->detach();
        $invoice->delete();

        $area = Area::find($this->areaFields['id_area']);
        $area->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m InvoicesUnitTest:\033[0m testAddManyAssignationsToPurchaseOrders "."\033[32m OK \033[0m ". "\n");

    }





}

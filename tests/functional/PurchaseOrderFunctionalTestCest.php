<?php
use Page\Functional\Login;
use App\Models\User;
use Page\Functional\PurchaseOrderPage;
use Page\TestData;
use App\Http\Controllers\PurchaseOrderController;
use App\Models\AccountBudget;
use Illuminate\Http\Request;
use App\Models\AreasBudget;

class PurchaseOrderFunctionalTestCest
{
    private $userAttributes;
    private $defaultAttributes;
    private $contract;


    public function __construct()
    {
        $this->defaultAttributes = [ 'folio_number' => 'TEC20170403160426_174_01' ];
        $this->postAttributes = [
            'name_provider'     => 'PERSONAL COMPUTER FACTORY S.A. - 686855 (compra de monitores)',
            'payment_condition' => 2,
            'payment_method'    => 5,
            'currency'          => "2",
            'tipo_boleta'       => 1,
            'cant_1'            => "1",
            'desc_1'            => 'Teclado',
            'month_ini_1'       => 1,
            'priceWithoutIva_1' => '150000',
            'total_sin_iva'     => '150000',
            'total'             => '150000'
        ];

    }

    public function _before(FunctionalTester $I)
    {
        //Add Area
        $I->haveRecord('areas',[
            'id_area'=>8888,
            'short_name'=>'AREATEST',
            'long_name'=>'AREATEST',
            'manager_name'=>'Ignacio Perez',
            'manager_position'=>'Gerente de Área',
            'id_user'=>9999,
            'budget_closed'=>0,
        ]);
        $I->haveRecord('areas_budget',[
           'id_area'=>8888,
           'budget_year'=>'2017',
           'total_budget_initial'=>5000000,
           'total_budget_available'=>5000000,
        ]);
        $I->haveRecord('account_budget',[
           'id_area'=>8888,
            'budget_year'=>'2017',
            'account_name'=>'Cuenta test',
            'account_code'=>'7777',
            'description'=>'Cuenta test',
            'total_budget_initial'=>5000000,
            'total_budget_available'=>5000000,
        ]);



        $this->userAttributes = Login::getUserGoogle(9999);
        $this->userAttributes['id_area']=8888;
        $I->haveRecord('users', $this->userAttributes);
        /** @var User $user */
        $user = User::find($this->userAttributes['id_user'] );

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));
        $I->amLoggedAs($user);
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();

    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function testConsultOrders(FunctionalTester $I)
    {
        if(Auth::user()->id_area === null){
            $I->amOnPage('/');
            $I->see('No puede consultar ordenes de compra.
            Aún no tiene Área asignada. Por favor contacte al administrador de sistemas.');
        }
        $I->amOnPage('/consultarOrdenes');
        $I->see('Consultar Órdenes de Compra','.content');
        $I->see('Listado de Órdenes','.content');
        $I->see('Agregar Orden de Compra','.content');
        $I->see('Órdenes Emitidas','.content');
        $I->see('Órdenes Aprobadas / Rechazadas','.content');
        //$I->sendAjaxGetRequest('/purchaseOrder/grid',array('length' => '3'));

    }

    public function testDetailPurchaseOrder(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage)
    {
        $name_provider = $this->prepareToCreatePurchaseOrder($I);
        $purchase_order = $purchaseOrderPage->createPurchaseOrderForEdit($name_provider,$this->contract );

        $I->amOnPage('/detailPurchaseOrder/'.$purchase_order['folio_number']);
        $I->see('Yapo.cl SpA');
        $I->see('Orden de Compra');
        $I->see('Folio');
        $I->see($purchase_order['folio_number']);
        $I->see('Razón Social');
        $I->see(TestData::Provider['name_provider']);
        $I->see('Condición de Pago');
        $I->see(TestData::Provider['payment_conditions']);
        $I->see('Método de Pago');
        $I->see(TestData::Provider['payment_method']);
        $I->see('N° Contrato');
        $I->see('666');
        $I->see('Cantidad');
        $I->see('Descripción');
        $I->see('Importe Unitario Sin IVA');
        $I->see('Importe Unitario Con IVA');
        $I->see(TestData::orderDetail_4['quantity']);
        $I->see(TestData::orderDetail_4['description']);
        $I->see(TestData::orderDetail_4['price']);
        $I->see(TestData::orderDetail_4['has_iva']);
        $I->see(TestData::orderDetail_4['price_iva']);
        $I->see(TestData::purchaseOrderFields['total_price']);
        $I->see(TestData::purchaseOrderFields['total_iva_price']);
    }

    public function prepareToCreatePurchaseOrder($I){

        $provider = $I->haveRecord('provider',TestData::Provider);
        $this->contract = $I->haveRecord('contract',TestData::ContractProvider($provider));

        $I->haveRecord('account_contract',[
            'id_contract'=>$this->contract ,
            'id_area'=>8888,
            'account_code'=>'7777',
            'account_year'=>'2017',
        ]);

        //dd($contract);
        $I->seeRecord('contract', ['id_contract' => $this->contract ]);

        $name_provider = TestData::Provider['name_provider'].' - '.TestData::ContractProvider($provider)['contract_number'].' ('.TestData::ContractProvider($provider)['description'].')';

        $this->testConsultOrders($I);

        return $name_provider;
    }

    public function testCreatePurchaseOrderWithTwoDetails(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);
        $purchaseOrderPage->createPurchaseOrder(1,$name_provider,$this->contract );

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
//        echo "\033[32m PASSED \033[0m ".PHP_EOL;
    }

    public function testCreatePurchaseOrderWithTotalIndependent(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);

        $purchaseOrderPage->createPurchaseOrder(2,$name_provider,$this->contract);

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
//        echo "\033[32m PASSED \033[0m ".PHP_EOL;
    }

    public function testCreateOnePurchaseOrderWithThreeDetails(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);

        $purchaseOrderPage->createPurchaseOrder(3,$name_provider,$this->contract);

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
//        echo "\033[32m PASSED \033[0m ".PHP_EOL;
    }

    public function testCreateOnePurchaseOrderWithTwoDetails(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);

        $purchaseOrderPage->createPurchaseOrder(4,$name_provider,$this->contract);

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
//        echo "\033[32m PASSED \033[0m ".PHP_EOL;
    }

    public function testCreatePurchaseOrderDolar(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);

        $purchaseOrderPage->createPurchaseOrder(5,$name_provider,$this->contract);

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
    }

    public function testCreatePurchaseOrderHigherAvailable(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);
        //Monto mayor

        $purchaseOrderPage->createPurchaseOrder(6,$name_provider,$this->contract);

        $this->testConsultOrders($I);
        $I->seeCurrentUrlEquals($purchaseOrderPage::$URL_CONSULTORDER);
        $I->see('Listado de Órdenes');
//        echo "\033[32m PASSED \033[0m ".PHP_EOL;

    }

    public function testRejectPurchaseOrder(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $provider = $I->haveRecord('provider',TestData::Provider);
        $contract = $I->haveRecord('contract',TestData::ContractProvider($provider));

        $I->haveRecord('account_contract',[
            'id_contract'=>$contract,
            'id_area'=>8888,
            'account_code'=>'7777',
            'account_year'=>'2017',
        ]);

        $I->seeRecord('contract', ['id_contract' => $contract]);

        $name_provider = TestData::Provider['name_provider'].' - '.TestData::ContractProvider($provider)['contract_number'].' ('.TestData::ContractProvider($provider)['description'].')';

        $this->testConsultOrders($I);
        $accountBudgetInicial = AccountBudget::getBudget(8888,'2017','7777');

        //echo PHP_EOL."- Creación Orden Compra".PHP_EOL;
        $oc = $purchaseOrderPage->createPurchaseOrder(7,$name_provider,$contract);

        $diferencia = (float)$accountBudgetInicial->total_budget_available - (float)$oc['total_price'];

        $accountBudgetNew = AccountBudget::getBudget(8888,'2017','7777');

        //Vemos si el monto inicial es mayor que el nuevo monto
        $I->assertTrue($diferencia == (float)$accountBudgetNew->total_budget_available,"Monto disponible disminuye");

        $urlValidatePurchaseOrder = '/detailPurchaseOrder/'.$oc['folio_number'].'/validate';

        $I->amOnPage($urlValidatePurchaseOrder);

        $I->see('Yapo.cl SpA');

        $I->click('#rechazar');

        $urlRejectPurchaseOrder = "/rejectPurchaseOrder/".$oc['folio_number'];

        //echo PHP_EOL."- Rechazar Orden Compra".PHP_EOL;
        $I->sendAjaxRequest('POST',$urlRejectPurchaseOrder,['id'=>$oc['folio_number'],'_method'=>'GET',"mensajeRechazo"=>"CORREO TEST | Justificacion test"]);

        $accountBudgetFinal = AccountBudget::getBudget(8888,'2017','7777');

        $sumaBudget = (float)$accountBudgetNew->total_budget_available + (float)$oc['total_price'];

        //Vemos que el monto final sea igual al inicial ( osea que se haya recuperado luego de rechazar la oc )
        $I->assertTrue($sumaBudget == (float)$accountBudgetFinal->total_budget_available,"Se recupera el monto");

    }

    public function testDeleteOCAssociatedToInvoice(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){

        //Validar presupuesto inicial
        $accountBudgetInicial = AccountBudget::getBudget(8888,'2017','7777');

        //Crear data necesaria para OC
        $dataInvoice = TestData::invoiceFields;
        $provider = $I->haveRecord('provider',TestData::Provider);
        $dataInvoice['id_provider'] = $provider;
        unset($dataInvoice['id_area']);
        $contract = $I->haveRecord('contract',TestData::ContractProvider($provider));
        $invoice = $I->haveRecord('invoices', $dataInvoice);
        $I->haveRecord('account_contract',[
            'id_contract'=>$contract,
            'id_area'=>8888,
            'account_code'=>'7777',
            'account_year'=>'2017',
        ]);
        $name_provider = TestData::Provider['name_provider'].' - '.TestData::ContractProvider($provider)['contract_number'].' ('.TestData::ContractProvider($provider)['description'].')';

        //Crear OC
        $oc = $purchaseOrderPage->createPurchaseOrder(7,$name_provider,$contract);
        $I->haveRecord('invoices_orders',['id_invoice' => $invoice, 'id_purchase_order' => $oc['folio_number'],
                                                'subtotal' => 50, 'exchange_rate' => 1, 'subtotal_po_currency' => 50]);

        //Vemos si el monto inicial del presupuesto es mayor que el nuevo monto (que la OC se creo correctamente)
        $diferencia = (float)$accountBudgetInicial->total_budget_available - (float)$oc['total_price'];
        $accountBudgetNew = AccountBudget::getBudget(8888,'2017','7777');
        $I->assertTrue($diferencia == (float)$accountBudgetNew->total_budget_available,"Monto disponible disminuye");

        //Eliminar OC
        $urlValidatePurchaseOrder = '/consultarOrdenes';
        $I->amOnPage($urlValidatePurchaseOrder);
        $urlRejectPurchaseOrder = "/deletePurchaseOrder";
        $I->sendAjaxRequest('GET',$urlRejectPurchaseOrder,array('idOrden' => $oc['folio_number'], '_method' => 'GET'));


        //Vemos que el monto final sea igual al inicial ( osea que se haya recuperado luego de rechazar la oc )
        $accountBudgetFinal = AccountBudget::getBudget(8888,'2017','7777');
        $sumaBudget = (float)$accountBudgetNew->total_budget_available + (float)$oc['total_price'];
        $I->assertTrue($sumaBudget == (float)$accountBudgetFinal->total_budget_available,"Se recupera el monto");

        //Validamos que la relación con la factura ya no exista
        $I->dontSeeRecord('invoices_orders',['id_invoice' => $invoice, 'id_purchase_order' => $oc['folio_number'],
                                                'subtotal' => 50, 'exchange_rate' => 1, 'subtotal_po_currency' => 50]);
    }

    public function testEditPurchaseOrder(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){
        $name_provider = $this->prepareToCreatePurchaseOrder($I);
        $purchase_order = $purchaseOrderPage->createPurchaseOrderForEdit($name_provider,$this->contract );

        $actual_account_budget = AccountBudget::getBudget(8888,'2017','7777');

        $actual_budget_available_area = \App\Models\AreasBudget::find(8888);

        $actual_budget_available_area->total_budget_available = 0.00;
        $actual_budget_available_area->save();


        $I->amOnPage('/editarOrden/'.$purchase_order['folio_number']);
        $I->see('Editar Orden de Compra','.content');

        $data_form = [
            'items'     => '[{
                "cantidad"     :"1",
                "description"  :"asd",
                "valor_sin_iva":"$ 100",
                "has_iva"      :"true",
                "valor_con_iva":"$ 119"
            }]',
            'purchase_order'     => $purchase_order['folio_number'],
            'total_sin_iva'     => '100',
            'total'             => '119',
        ];

        $I->submitForm('#f_orden', $data_form);
        
        $I->grabRecord('purchase_order',[
            'folio_number'=>$purchase_order['folio_number'],
            'id_user'=>9999,
            'id_area'=>8888,
            'id_payment_condition'=>TestData::Provider['payment_conditions'],
            'id_payment_method'=>TestData::Provider['payment_method'],
            'id_currency'=>2,
        ]);

        $accountBudget = AccountBudget::getBudget(8888,'2017','7777');
        $areaBudget = \App\Models\AreasBudget::find(8888);

        $diferencia_account_budget = $accountBudget->total_budget_available - $actual_account_budget->total_budget_available ; //100

        $I->assertFalse($diferencia_account_budget  != 100,"El monto total no coinside con el descuento del budget de la cuenta");
        $I->assertFalse($areaBudget->total_budget_available  != 100,"El monto total no coinside con el descuento del budget de la cuenta");
        $I->see('La Orden de Compra se ha modificado correctamente.');

    }

    public function testEditOldPurchaseOrder(FunctionalTester $I,PurchaseOrderPage $purchaseOrderPage){

        $provider = $I->haveRecord('provider',TestData::Provider);

        $FolioNumberOldOC = 'TEST20161223095724_111_12';


        $I->haveRecord('contract',[
           'id_contract' => 123,
            'id_provider' => TestData::ContractProvider($provider)['id_provider'],
            'contract_number'=>'C123',
            'description'=>'Contrato test',
            'is_active' => 1
        ]);

        $I->haveRecord('account_budget',[
           'id_area' => 8888,
            'budget_year' => 2017,
            'account_name' => 'Cuenta P6666',
            'account_code' => 'P6666',
            'description' => 'Cuenta P6666',
            "total_budget_initial" => 5000000.00,
            'total_budget_available' => 0.00
        ]);


        $I->haveRecord('purchase_order',[
            'folio_number' => 'TEST20161223095724_111_12',
            'id_area' => 8888,
            'id_user' =>$this->userAttributes['id_user'] ,
            'id_contract' => 123,
            'id_payment_condition' => 2,
            'id_payment_method' => 5,
            'total_price' => 5500,
            'total_iva_price' => 5500,
            'id_currency' => 2,
            'is_visible' => 1,
            'old_folio_number' => 'DOR20161223095724',
        ]);

        $I->haveRecord('purchase_order_detail',[
            'id_purchase_order_detail' => 111,
            'id_purchase_order' => 'TEST20161223095724_111_12',
            'description' => 'P6666 TEST DETAIL',
            'quantity' => 1,
            'price' => 5500.00,
            'has_iva' => 0,
            'price_iva' => 5500.00,
            'id_currency' => 2,
        ]);

        $actual_account_budget = AccountBudget::getBudget(8888,'2017','P6666');
        $actual_budget_available_area = \App\Models\AreasBudget::find(8888);

        $actual_budget_available_area->total_budget_available = 0.00;
        $actual_budget_available_area->save();



        $I->amOnPage('/editarOrden/TEST20161223095724_111_12');
        $I->see('Editar Orden de Compra','.content');


        $data_form = [
            'items'     => '[{
                "cantidad"     :"1",
                "description"  :"P6666 TEST DETAIL",
                "valor_sin_iva":"$ 5000",
                "has_iva"      :"false",
                "valor_con_iva":"$ 5000"
            }]',
            'id_currency' => '2',
            'purchase_order'     => $FolioNumberOldOC,
            'total_sin_iva'     => '5000',
            'total'             => '5000',
        ];

        $I->submitForm('#f_orden', $data_form);


        $accountBudget = AccountBudget::getBudget(8888,'2017','P6666');
        $areaBudget = \App\Models\AreasBudget::find(8888);

        $diferencia_account_budget = $accountBudget->total_budget_available - $actual_account_budget->total_budget_available ; //500

        $I->assertFalse($diferencia_account_budget  != 500,"El monto total no coinside con el descuento del budget de la cuenta");
        $I->assertFalse($areaBudget->total_budget_available  != 500,"El monto total no coinside con el descuento del budget de la cuenta");
        $I->see('La Orden de Compra se ha modificado correctamente.');

    }
}

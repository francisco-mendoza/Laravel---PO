<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\ContractsController;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Contract;
use App\Models\AccountContract;
use App\Models\PurchaseOrder;

use Page\TestData;

class ContractUnitTest extends TestCase
{

    /** @var  ContractsController */
    protected $controller;

    /** @var  Contract */
    protected $contract;

    /** @var  Request */
    protected $request;
    public $contractFields;
    protected $userFields;
    protected $budgetFields;
    protected $budgetFields2;
    protected $accountbudgetFields;
    protected $accountbudgetFields2;


    public function setUp(){
        parent::setUp(); // TODO: Change the autogenerated stub
        //Setear usuario
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        //Inicializar el controlador a probar
        $this->controller = new ContractsController();

        //Parámetros mínimos del request
        $this->resetRequest();

        //Datos de Prueba
        $this->userFields = TestData::userFields;
        $this->contractFields = TestData::contractFields;

    }

    public function tearDown()
    {
        Session::clear();
    }

    public function resetRequest(){
        //Parámetros mínimos del request
        $this->request = new Request();
    }

    public function testListContractsWithoutRolAssigned(){

        Session::start();

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);
        $user->detachRoles();

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts');
        $this->assertRedirectedToRoute("home");
        $this->assertArrayHasKey('error_message',Session::all(),"No tienes permiso para ver esa área");


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testListContractsWithoutRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testListContractsWithRolAssigned(){


        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts');

        $this->see('Listado de Contratos');
        $this->see('Agregar Contrato');
        $this->see('Nro. de Contrato');
        $this->see('Proveedor');
        $this->see('Activo?');
        $this->see('Finalización');

        $user->detachRoles();
        $user->attachRole(config('constants.gerencia'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts');

        $this->see('Listado de Contratos');
        $this->see('Nro. de Contrato');
        $this->see('Proveedor');
        $this->see('Activo?');
        $this->see('Finalización');

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testListContractsWithRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testCreateContract(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts/create');

        $this->see('Agregar/Editar Contrato');
        $this->see('Proveedor');
        $this->seeElement('input', ['name' => 'id_provider']);
        $this->see('Número de Contrato');
        $this->seeElement('input', ['name' => 'contract_number']);
        $this->see('Descripción');
        $this->seeElement('input', ['name' => 'description']);
        $this->see('Fecha de Finalización');
        $this->seeElement('input', ['name' => 'end_date']);
        $this->see('PDF Contrato');
        //$this->seeElement('input', ['name' => 'end_date']);
        $this->see('Activar Contrato');
        $this->seeElement('input', ['name' => 'is_active']);



        $this->see('Cuentas');
        $this->see('Agregar Cuenta');
        $this->seeElement('select', ['name' => 'account_area']);
        $this->seeElement('input', ['name' => 'account_code']);
        $this->seeElement('input', ['name' => 'account_year']);

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testCreateContract "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function saveContractForSearch(){
        $contract = new Contract($this->contractFields);
        $contract->save();
    }

    public function seeContractInPage(){
        $this->see('Proveedor');
        //$this->seeElement('#id_provider', $this->contractFields['id_provider']);
        $this->see('Número de Contrato');
        $this->seeInField('#contract_number', $this->contractFields['contract_number']);
        $this->see('Descripción');
        $this->seeInField('#description', $this->contractFields['description']);
        $this->see('Fecha de Finalización');
        $this->seeInField('#end_date', '15/02/2017');
        $this->see('PDF Contrato');
    }

    public function testEditContract(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Contratos');

        $this->see("Listado de Contratos");

        $this->call('GET', '/contracts/'. $this->contractFields['id_contract'] .'/edit');


        $area = Contract::find($this->contractFields['id_contract']);
        $area->delete();


        $this->see('Agregar/Editar Contrato');
        $this->seeContractInPage();
        $this->see('Cuentas');
        $this->see('Año');
        $this->dontSee('Presupuesto Anual');
        $this->see('Agregar Cuenta');
        $this->seeElement('select', ['name' => 'account_area']);
        $this->seeElement('input', ['name' => 'account_code']);
        $this->seeElement('input', ['name' => 'account_year']);

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testEditContract "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function testValidateContractWithoutProvider(){

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormContract($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testValidateContractWithoutProvider "."\033[32m OK \033[0m ". "\n");

    }

    public function testValidateContractWithoutContractNumber(){

        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Llenar el último dato necesario
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormContract($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testValidateContractWithoutContractNumber "."\033[32m OK \033[0m ". "\n");

    }

    public function testPassValidateContract(){

        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Llenar el último dato necesario
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->controller->validateFormContract($this->request);

        $this->addToAssertionCount(1);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testPassValidateContract "."\033[32m OK \033[0m ". "\n");
    }

    public function testCreateStoreContract(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->request["description"] = $this->contractFields['description'];
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";


        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        //dd($json_cuentas);
        $this->controller->store($this->request);

        unset($this->contractFields['id_contract']);
        unset($array_cuentas[0]['area']);
        unset($array_cuentas[1]['area']);

        $this->seeInDatabase('contract', $this->contractFields);
        $this->seeInDatabase('account_contract', $array_cuentas[0]);
        $this->seeInDatabase('account_contract', $array_cuentas[1]);

        $contract = Contract::findBy('contract_number',$this->contractFields['contract_number']);
        AccountContract::deleteAccountsByContract($contract->id_contract);
        $contract->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testCreateStoreContract "."\033[32m OK \033[0m ". "\n");
    }


    public function testStoreContractWithWrongDescriptionForDash(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->request["description"] = "Prueba con guion - en la descripción";
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";


        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;


        $response = $this->controller->store($this->request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals($this->baseUrl.'/contracts', $response->getTargetUrl());
        $this->assertTrue($response->getSession()->exists('toastr::messages'));
        $this->assertContains(" La descripción del contrato no debe contener guión o paréntesis.", $response->getSession()->get('toastr::messages')[0]['message']);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testStoreContractWithWrongDescriptionForDash "."\033[32m OK \033[0m ". "\n");

    }

    public function testStoreContractWithWrongDescriptionForParenthesis(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->request["description"] = "Prueba con parentesis (  en la descripción )";
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";


        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        $response = $this->controller->store($this->request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals($this->baseUrl.'/contracts', $response->getTargetUrl());
        $this->assertTrue($response->getSession()->exists('toastr::messages'));
        $this->assertContains(" La descripción del contrato no debe contener guión o paréntesis.", $response->getSession()->get('toastr::messages')[0]['message']);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testStoreContractWithWrongDescriptionForParenthesis "."\033[32m OK \033[0m ". "\n");

    }

    public function testStoreContractWithWrongContractNumberForParenthesis(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["contract_number"] = "Prueba con parentesis ( en el numero de contrato )";
        $this->request["description"] = $this->contractFields['description'];
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";


        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        $response = $this->controller->store($this->request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals($this->baseUrl.'/contracts', $response->getTargetUrl());
        $this->assertTrue($response->getSession()->exists('toastr::messages'));
        $this->assertContains(" Número de contrato no debe contener guión o paréntesis.", $response->getSession()->get('toastr::messages')[0]['message']);
//            dd($response->getSession()->get('toastr::messages')[0]['message']);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testStoreContractWithWrongContractNumberForParenthesis "."\033[32m OK \033[0m ". "\n");

    }

    public function testStoreContractWithWrongContractNumberForDash(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Nuevo contrato
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA "; //Con el nombre del proveedor de id 1
        $this->request["contract_number"] = "Prueba con parentesis - en el numero de contrato ";
        $this->request["description"] = $this->contractFields['description'];
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";


        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        $response = $this->controller->store($this->request);

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals($this->baseUrl.'/contracts', $response->getTargetUrl());
        $this->assertTrue($response->getSession()->exists('toastr::messages'));
        $this->assertContains(" Número de contrato no debe contener guión o paréntesis.", $response->getSession()->get('toastr::messages')[0]['message']);
//            dd($response->getSession()->get('toastr::messages')[0]['message']);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testStoreContractWithWrongContractNumberForDash "."\033[32m OK \033[0m ". "\n");

    }


    public function testUpdateContract(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        //Nuevo contrato
        $newDescription = "prueba de edicion de contrato";
        $this->request["id_contract"] = $this->contractFields['id_contract'];
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA";
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->request["description"] = $newDescription;
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";

        //Editar cuentas (marcador se activa por JS)
        $this->request["edit_accounts"] = "true";

        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"1234","account_year"=>"2017"]);
        array_push($array_cuentas,["area"=>"Desarrollo Organizacional","account_code"=>"1010","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        //dd($json_cuentas);
        $this->controller->update($this->request);

        $this->dontSeeInDatabase('contract', $this->contractFields);
        $this->contractFields['description'] = $newDescription;

        unset($array_cuentas[0]['area']);
        unset($array_cuentas[1]['area']);

        $this->seeInDatabase('contract', $this->contractFields );
        $this->seeInDatabase('account_contract', $array_cuentas[0]);
        $this->seeInDatabase('account_contract', $array_cuentas[1]);

        $contract = Contract::findBy('contract_number',$this->contractFields['contract_number']);
        AccountContract::deleteAccountsByContract($contract->id_contract);
        $contract->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testUpdateContract "."\033[32m OK \033[0m ". "\n");
    }

    public function testUpdateAccountContractWithPurchaseOrders(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $oc_data = TestData::purchaseOrderFields;
        $oc_data['id_contract'] = $this->contractFields['id_contract'];
        $oc = new PurchaseOrder($oc_data);
        $oc->save();

        $data_account = TestData::getAccountContract($oc_data["id_area"],"1234", $this->contractFields['id_contract']);

        $account = new AccountContract($data_account);
        $account->save();

        //Nuevo contrato
        $newDescription = "prueba de edicion de contrato";
        $this->request["id_contract"] = $this->contractFields['id_contract'];
        $this->request["id_provider"] = "AB MARKETING PROMOCION LIMITADA";
        $this->request["contract_number"] = $this->contractFields['contract_number'];
        $this->request["description"] = $newDescription;
        $this->request["is_active"] = "on";
        $this->request["end_date"] = "15/02/2017";

        //Editar cuentas (marcador se activa por JS)
        $this->request["edit_accounts"] = "true";

        $array_cuentas = [];
        //Agregar cuentas
        array_push($array_cuentas,["area"=>"Tecnología","account_code"=>"5555","account_year"=>"2017"]);

        $json_cuentas = json_encode($array_cuentas);

        $this->request["accounts"] = $json_cuentas;

        //dd($json_cuentas);
        $response = $this->controller->update($this->request);

        $contract = Contract::findBy('contract_number',$this->contractFields['contract_number']);
        $purchase = PurchaseOrder::findBy('folio_number', $oc_data['folio_number']);
        $purchase->delete();
        AccountContract::deleteAccountsByContract($contract->id_contract);
        $contract->delete();

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertEquals($this->baseUrl.'/contracts', $response->getTargetUrl());
        $this->assertTrue($response->getSession()->exists('toastr::messages'));
        $this->assertContains("Estas intentado modificar una cuenta asociada a una Orden de Compra", $response->getSession()->get('toastr::messages')[0]['message']);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testUpdateContract "."\033[32m OK \033[0m ". "\n");

    }

    public function testDestroyContract(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $data_account = TestData::getAccountContract(TestData::purchaseOrderFields["id_area"],"1234", $this->contractFields['id_contract']);

        $account = new AccountContract($data_account);
        $account->save();

        $this->controller->destroy($this->request, $this->contractFields['id_contract']);

        $this->dontSeeInDatabase('account_contract', $data_account);
        $this->dontSeeInDatabase('contract', $this->contractFields);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testDestroyContract "."\033[32m OK \033[0m ". "\n");
    }

    public function testDeleteContractWithPurchaseOrders(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $oc_data = TestData::purchaseOrderFields;
        $oc_data['id_contract'] = $this->contractFields['id_contract'];
        $oc = new PurchaseOrder($oc_data);
        $oc->save();

        $data_account = TestData::getAccountContract($oc_data["id_area"],"1234", $this->contractFields['id_contract']);

        $account = new AccountContract($data_account);
        $account->save();

        $response = $this->controller->destroy($this->request, $this->contractFields['id_contract']);

        //Eliminar datos adicionales
        $contract = Contract::findBy('contract_number',$this->contractFields['contract_number']);
        $purchase = PurchaseOrder::findBy('folio_number', $oc_data['folio_number']);
        $purchase->delete();
        AccountContract::deleteAccountsByContract($contract->id_contract);
        $contract->delete();

        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        $data = json_decode($response->getContent());
        $this->assertContains("Existen OC (vigentes o no) asociadas a alguna cuenta del contrato seleccionado.", $data->message);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testDeleteContractWithPurchaseOrders "."\033[32m OK \033[0m ". "\n");
    }


    public function testFindContract(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Contratos');

        $this->see('Listado de Contratos');

        $this->request["search"] = ["value" => $this->contractFields['contract_number'] ];
        $json = $this->controller->grid($this->request);


        $area = Contract::find($this->contractFields['id_contract']);
        $area->delete();

       // dd($json);

        $this->assertNotNull($json);

        $data = json_decode($json, true); //Decodificar el json


        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('recordsTotal', $data);
        $this->assertArrayHasKey('iTotalDisplayRecords', $data);
        $this->assertArrayHasKey('recordsFiltered', $data);
        $this->assertArrayHasKey('draw', $data);

        $this->assertGreaterThanOrEqual(1,$data['recordsTotal']);



        $this->assertContains($this->contractFields['contract_number'],$json);
        $this->assertContains("".$this->contractFields['id_provider'],$json);
       // $this->assertContains($this->contractFields['contract_pdf'],$json);
        //$this->assertContains($this->contractFields['id_contract'],$json);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testFindContract "."\033[32m OK \033[0m ". "\n");


    }

    public function testValidateExistingContract(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveContractForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts/create');

        $this->see('Agregar/Editar Contrato');

        $this->request["name_provider"] = "AB MARKETING PROMOCION LIMITADA" ;
        $this->request["contract_number"] = $this->contractFields['contract_number'] ;
        $json = $this->controller->validateContract($this->request);

        $area = Contract::find($this->contractFields['id_contract']);
        $area->delete();

        $data = json_decode($json->content(), true);

        $this->assertArrayHasKey('contract_number', $data);
        $this->assertContains($this->contractFields['contract_number'],$data);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testValidateExistingContract "."\033[32m OK \033[0m ". "\n");

    }

    public function testValidateNonExistingContract(){
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/contracts/create');

        $this->see('Agregar/Editar Contrato');

        $this->request["name_provider"] = "AB MARKETING PROMOCION LIMITADA" ;
        $this->request["contract_number"] = $this->contractFields['contract_number'] ;
        $json = $this->controller->validateContract($this->request);


        $data = json_decode($json->content(), true);

        $this->assertArrayNotHasKey('contract_number', $data);
        //$this->assertNotContains()
        $this->assertNotContains($this->contractFields['contract_number'],$data);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testValidateNonExistingContract "."\033[32m OK \033[0m ". "\n");

    }

    public function testGetProviders(){
        $json = $this->controller->getProviders();

        $count = count(json_decode($json->content()));

        $this->assertNotNull($json);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testGetProviders "."\033[32m OK \033[0m ". "\n");

    }

    public function testGetAccounts(){

        $json = $this->controller->getAccounts($this->request,1);
        $this->assertNotNull($json);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m ContractsUnitTest:\033[0m testGetAccounts "."\033[32m OK \033[0m ". "\n");

    }

}

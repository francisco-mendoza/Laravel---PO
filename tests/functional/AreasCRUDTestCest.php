<?php

use Page\Functional\Login;
use Page\Functional\AreasPage;

use App\Models\User;
use App\Models\Role;

use Page\TestData;

class AreasCRUDTestCest
{
    private $userAttributes;
    private $areaAttributes;
    private $budgetAttributes;
    private $postAttributes;
    private $idArea;
    private $areaRestrictedAttributes;

    private $tableName;
    private $detailTableName;

    public function __construct()
    {
        $random = rand(0,9);
        $this->postAttributes = ['short_name' => 'XXX'.$random,
            'long_name' => 'Area de Prueba - data aleatoria',
            'manager_name' => 'Julio',
            'manager_position' => 'Gerente',
            'id_user' => '9999',
            'budgets' => json_encode(array()),
        ];
    }

    public function _before(FunctionalTester $I)
    {
        $this->userAttributes = Login::getUserGoogle(9999);
        $I->haveRecord('users', $this->userAttributes);

        $this->areaAttributes = TestData::areaFields;
        $this->idArea = $I->haveRecord('areas', $this->areaAttributes);

        $this->budgetAttributes = TestData::accountBudgetFields_1;

        $user = User::find($this->userAttributes['id_user'] );

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $I->amLoggedAs($user);
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();

        $this->tableName = 'areas';
        $this->detailTableName = 'account_budget';

    }

    public function _createBudget(){

        $form['year'] = $this->budgetAttributes['budget_year'];
        $form['code'] = $this->budgetAttributes['account_code'];
        $form['name'] = $this->budgetAttributes['account_name'];
        $form['desc'] = $this->budgetAttributes['description'];
        $form['amount'] = number_format($this->budgetAttributes['total_budget_initial'],0,',','.');

        $detail = array();
        array_push($detail, $form);

        $this->postAttributes['budgets'] = json_encode($detail);

    }

    public function _createBudgetEdit(){

        $form['year'] = $this->budgetAttributes['budget_year'];
        $form['code'] = $this->budgetAttributes['account_code'];
        $form['name'] = $this->budgetAttributes['account_name'];
        $form['desc'] = $this->budgetAttributes['description'];
        $form['amount'] = number_format("35000.00",0,',','.');

        $detail = array();
        array_push($detail, $form);

        $this->postAttributes['budgets'] = json_encode($detail);

    }


    public function _after(FunctionalTester $I)
    {
    }

    public function listAreas(FunctionalTester $I, AreasPage $areasPage)
    {
        $areasPage->listAreas();
        $I->seeCurrentUrlEquals($areasPage::$URL); //Usuario ve la página porque tiene los permisos adecuados
        $I->see('Listado de Áreas');
        $I->see('Abreviatura');
        $I->see('Nombre');
        $I->see('Gerente');
        $I->see('Cargo Gerente');
        $I->see('Editar');
        $I->see('Eliminar');
        $I->sendAjaxGetRequest('/areas/grid',array('length' => '1'));

        //Al estar ordenado alfabeticamente, aparecerá siempre de primera
        $I->see($this->areaAttributes['short_name']);
        $I->see($this->areaAttributes['long_name']);
        $I->see($this->areaAttributes['manager_name']);
        $I->see($this->areaAttributes['manager_position']);

    }


    public function detailArea(FunctionalTester $I, AreasPage $areasPage){

        $area = $I->grabRecord($this->tableName,  $this->areaAttributes);
        $this->listAreas($I,$areasPage);
        $areasPage->detailArea($area['id_area']);

        $I->seeInField('#short_name', $area['short_name']);
        $I->seeInField('#long_name', $area['long_name']);
        $I->seeInField('#manager_name', $area['manager_name']);
        $I->seeInField('#manager_position', $area['manager_position']);
    }

    public function createAreaWithoutBudget(FunctionalTester $I, AreasPage $areasPage){

        $this->listAreas($I,$areasPage);
        $areasPage->createArea($this->postAttributes);
        $I->see('Área creada!');
        $I->seeCurrentUrlEquals($areasPage::$URL);
        unset($this->postAttributes['budgets']); //Este campo no pertenece a la tabla area
        $I->seeRecord($this->tableName,$this->postAttributes);
        $I->see('Listado de Áreas');

    }

    public function createAreaWithBudget(FunctionalTester $I, AreasPage $areasPage){

        $this->listAreas($I,$areasPage);

        $this->_createBudget(); //Generar detalle en los atributos detalle
        $areasPage->createArea($this->postAttributes);
        $I->see('Área creada!');
        $I->seeCurrentUrlEquals($areasPage::$URL);
        unset($this->postAttributes['budgets']); //Este campo no pertenece a la tabla area
        $I->seeRecord($this->tableName,$this->postAttributes);


        //Validar que existe el detalle
        $area = $I->grabRecord($this->tableName,$this->postAttributes);
        $this->budgetAttributes['id_area'] = $area['id_area'];
        $I->seeRecord($this->detailTableName,$this->budgetAttributes);
        $I->see('Listado de Áreas');

    }

    public function failCreationArea(FunctionalTester $I, AreasPage $areasPage){
        $this->listAreas($I,$areasPage);
        $areasPage->createArea();
        $I->seeCurrentUrlEquals($areasPage::$URL . '/create' );
        $I->seeFormHasErrors();
        $I->seeFormErrorMessage('short_name');
        $I->seeFormErrorMessage('long_name');
        $I->seeFormErrorMessage('manager_name');
        $I->seeFormErrorMessage('manager_position');
    }

    public function editArea(FunctionalTester $I, AreasPage $areasPage){
        $this->listAreas($I,$areasPage);
        $randName = "Nuevo nombre";

        $newAttributes = $this->areaAttributes;
        $newAttributes['long_name'] = $randName;

        unset($newAttributes['id_area']); //Este campo no pertenece a la tabla area

        $newAttributes['budgets'] = json_encode(array());
        $newAttributes['id_user'] = $this->userAttributes['id_user'];

        $newAttributes['budget_closed'] = false;
        $areasPage->editArea($this->idArea, $newAttributes);
        $I->seeCurrentUrlEquals($areasPage::$URL);
        $I->see('Área modificada!');

        unset($newAttributes['budgets']); //Este campo no pertenece a la tabla area

        $I->seeRecord($this->tableName,$newAttributes);
        $I->dontSeeRecord($this->tableName,$this->areaAttributes);
    }

    public function editAreaAddBudget(FunctionalTester $I, AreasPage $areasPage){
        $this->listAreas($I,$areasPage);
        $randName = "Nuevo nombre";

        $newAttributes = $this->areaAttributes;
        $newAttributes['long_name'] = $randName;

        unset($newAttributes['id_area']); //Este campo no pertenece a la tabla area

        $this->_createBudget();
        $newAttributes['budgets'] = $this->postAttributes['budgets'];
        $newAttributes['id_user'] = $this->userAttributes['id_user'];
        $newAttributes['budget_closed'] = false;

        $areasPage->editArea($this->idArea, $newAttributes);
        $I->seeCurrentUrlEquals($areasPage::$URL);
        $I->see('Área modificada!');

        unset($newAttributes['budgets']); //Este campo no pertenece a la tabla area

        $I->seeRecord($this->tableName,$newAttributes);
        $I->dontSeeRecord($this->tableName,$this->areaAttributes);

        $this->budgetAttributes['id_area'] = $this->idArea;
        $I->seeRecord($this->detailTableName,$this->budgetAttributes);
    }

    public function editAreaEditBudget(FunctionalTester $I, AreasPage $areasPage){

        $this->listAreas($I,$areasPage);
        $randName = "Nuevo nombre";

        $newAttributes = $this->areaAttributes;
        $newAttributes['long_name'] = $randName;

        $this->budgetAttributes['id_area'] = $this->idArea;
        $I->haveRecord($this->detailTableName, $this->budgetAttributes);

        unset($newAttributes['id_area']); //Este campo no pertenece a la tabla area

        $this->_createBudgetEdit();
        $newAttributes['budgets'] = $this->postAttributes['budgets'];
        $newAttributes['id_user'] = $this->userAttributes['id_user'];
        $newAttributes['budget_closed'] = false;

        $areasPage->editArea($this->idArea, $newAttributes);
        $I->seeCurrentUrlEquals($areasPage::$URL);
        $I->see('Área modificada!');

        unset($newAttributes['budgets']); //Este campo no pertenece a la tabla area

        $I->seeRecord($this->tableName,$newAttributes);
        $I->dontSeeRecord($this->tableName,$this->areaAttributes);

        $this->budgetAttributes['id_area'] = $this->idArea;
        $I->dontSeeRecord($this->detailTableName,$this->budgetAttributes);

        $this->budgetAttributes['total_budget_initial'] = number_format(str_replace('.', '',json_decode($this->postAttributes['budgets'])[0]->amount),2,'.','');
        $this->budgetAttributes['total_budget_available'] = number_format(str_replace('.', '',json_decode($this->postAttributes['budgets'])[0]->amount),2,'.','');

        $I->seeRecord($this->detailTableName,$this->budgetAttributes);
    }

    public function editAreaFailEditBudget(FunctionalTester $I, AreasPage $areasPage){

        $randName = "Nuevo nombre";

        Session::start();
        $token = csrf_token();
        $areasPage->deleteArea($this->idArea, $token);

        $newAttributes = TestData::areaClosedFields;
        $idAreaClosed = $I->haveRecord('areas', TestData::areaClosedFields);

        $newAttributes['long_name'] = $randName; //Cambiar el nombre del area

        $this->budgetAttributes['id_area'] = $this->idArea;
        $I->haveRecord($this->detailTableName, $this->budgetAttributes); //Presupuesto actual

        unset($newAttributes['id_area']); //Este campo no pertenece a la tabla area

        $this->_createBudgetEdit();
        $newAttributes['budgets'] = $this->postAttributes['budgets'];
        $newAttributes['id_user'] = $this->userAttributes['id_user'];
        $newAttributes['budget_closed'] = '1';

        //Intentaremos aumentar el presupuesto original
        verify(number_format(str_replace('.', '',json_decode($this->postAttributes['budgets'])[0]->amount),2,'.',''))->
        greaterOrEquals($this->budgetAttributes['total_budget_initial']);

        $areasPage->editArea($this->idArea, $newAttributes);
        $I->seeCurrentUrlEquals($areasPage::$URL);
        $I->see('Ocurrió un error'); //Se lanza una excepción por intentar aumentar el presupuesto de un área cerrada

        unset($newAttributes['budgets']); //Este campo no pertenece a la tabla area

        $I->seeRecord($this->tableName,TestData::areaClosedFields); //Los cambios no se efectúan
        $I->dontSeeRecord($this->tableName,$newAttributes);

        $this->budgetAttributes['id_area'] = $idAreaClosed;
        $I->seeRecord($this->detailTableName,$this->budgetAttributes); //El presupuesto no cambia

        $this->budgetAttributes['total_budget_initial'] = number_format(str_replace('.', '',json_decode($this->postAttributes['budgets'])[0]->amount),2,'.','');
        $this->budgetAttributes['total_budget_available'] = number_format(str_replace('.', '',json_decode($this->postAttributes['budgets'])[0]->amount),2,'.','');

        $I->dontSeeRecord($this->detailTableName,$this->budgetAttributes);
    }


    public function deleteArea(FunctionalTester $I, AreasPage $areasPage){

        $this->listAreas($I,$areasPage);
        $I->seeRecord('areas',$this->areaAttributes);
        Session::start();
        $token = csrf_token();
        $areasPage->deleteArea($this->idArea, $token);
        $I->seeCurrentUrlEquals($areasPage::$URL);
        $I->dontSeeRecord($this->tableName,$this->areaAttributes);

    }




}

<?php

use App\Models\User;
use App\Models\Role;

use Page\Functional\PaymentConditionPage;
use Page\Functional\Login;

class PaymentConditionCRUDTestCest
{
    private $userAttributes;
    private $postAttributes;
    private $defaultAttributes;
    private $idPaymentCondition;

    public function __construct()
    {
        $this->postAttributes = [
            'name_condition' => 'Nueva condición de pago'
        ];

        $this->defaultAttributes = [ 'name_condition' => 'Pago en 3 meses' ];

    }

    public function _before(FunctionalTester $I)
    {

        $this->userAttributes = Login::getUserGoogle(9999);

        $this->idPaymentCondition = $I->haveRecord('payment_conditions', $this->defaultAttributes);
        $I->haveRecord('users', $this->userAttributes);

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




    public function listPaymentConditions(FunctionalTester $I, PaymentConditionPage $conditionPage)
    {
        $conditionPage->listPaymentCondition();
        $I->seeCurrentUrlEquals($conditionPage::$URL); //Usuario ve la página porque tiene los permisos adecuados
        $I->see('Listado de Condiciones de Pago');
        $I->see('Condición de Pago');
        $I->see('Editar');
        $I->see('Eliminar');
        $I->sendAjaxGetRequest('/paymentconditions/grid',array('length' => '1'));
        
    }

    public function detailPaymentCondition(FunctionalTester $I, PaymentConditionPage $conditionPage){

        $payment = $I->grabRecord('payment_conditions',  $this->defaultAttributes);
        $this->listPaymentConditions($I,$conditionPage);
        $conditionPage->detailPaymentCondition($payment['id_payment_conditions']);

        $I->seeInField('#name_condition', $payment['name_condition']);
    }
    
    public function createPaymentCondition(FunctionalTester $I, PaymentConditionPage $conditionPage){

        $this->listPaymentConditions($I,$conditionPage);
        $conditionPage->createPaymentCondition($this->postAttributes);
        $I->see('Condición de pago creada!');
        $I->seeCurrentUrlEquals($conditionPage::$URL);
        $I->seeRecord('payment_conditions',$this->postAttributes);
        $I->see('Listado de Condiciones de Pago');

    }

    public function failCreationPaymentCondition(FunctionalTester $I, PaymentConditionPage $conditionPage){
        $this->listPaymentConditions($I,$conditionPage);
        $conditionPage->createPaymentCondition();
        $I->seeCurrentUrlEquals($conditionPage::$URL . '/create' );
        $I->seeFormHasErrors();
        $I->seeFormErrorMessage('name_condition');
    }

    public function editPaymentCondition(FunctionalTester $I, PaymentConditionPage $conditionPage){
        $this->listPaymentConditions($I,$conditionPage);
        $randName = "Nuevo nombre";
        $conditionPage->editPaymentCondition($this->idPaymentCondition, ['name_condition' => $randName]);
        $I->seeCurrentUrlEquals($conditionPage::$URL);
        $I->see('Condición de pago modificada!');
        $I->seeRecord('payment_conditions',['name_condition' => $randName]);
        $I->dontSeeRecord('payment_conditions',$this->defaultAttributes);
    }

    public function deletePaymentCondition(FunctionalTester $I, PaymentConditionPage $conditionPage){

        $this->listPaymentConditions($I,$conditionPage);
        $I->seeRecord('payment_conditions',$this->defaultAttributes);
        Session::start();
        $token = csrf_token();
        $conditionPage->deletePaymentCondition($this->idPaymentCondition, $token);
        $I->seeCurrentUrlEquals($conditionPage::$URL);
        $I->dontSeeRecord('payment_conditions',$this->defaultAttributes);

    }

}

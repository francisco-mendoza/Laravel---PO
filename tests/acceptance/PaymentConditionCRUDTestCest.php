<?php

use Illuminate\Support\Facades\Facade;

use App\Models\PaymentCondition;

class PaymentConditionCRUDTestCest {


    public function _before(AcceptanceTester $I)
    {

        $I->amOnPage('/login');
        $I->click('#btn_login');
        $I->see('compra');
        $I->fillField('#Email', 'ana.mora@schibsted.cl');
        $I->click('#next');
        $I->fillField('#Passwd', 'Bhankyapo');
        $I->click('#signIn');
        $I->wait(3);
        $I->see('Inicio');
        $I->haveInDatabase('payment_conditions', [ 'name_condition' => 'Pago en 3 meses' ]);
    }

    public function _after(AcceptanceTester $I)
    {


    }

    /**
     * @group ana.mora
     */
    public function listPaymentCondition(AcceptanceTester $I)
    {
        $I->amOnPage('/paymentconditions');
        $I->wait(3);
        $I->see('Listado de Condiciones de Pago');
        $I->see('Condición de Pago');
        $I->see('Editar');
        $I->see('Eliminar');
        $I->see('Pago en 3 meses');
        $I->wait(3);

    }

    /**
     * @group ana.mora
     * @after listPaymentCondition
     */
    public function detailPaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $I->click('Pago en 3 meses');
        $I->wait(3);
        $I->see('Detalle de Condición de Pago');
        $I->see('Id de Condición de Pago');
        $I->see('Condición de Pago');
        $I->seeInField('#name_condition', 'Pago en 3 meses');
        $I->click('Atrás');

    }

    /**
     * @group ana.mora
     */
    public function editPaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $payment_id = $I->grabFromDatabase('payment_conditions', 'id_payment_conditions', array('name_condition' => 'Pago en 3 meses'));

        $varPath = "a[href='http://local.ordenescompra.cl/paymentconditions/" . $payment_id . "/edit']";
        $I->click('.icon-pencil',$varPath);
        $I->wait(3);
        $I->see('Agregar/Editar Condición de Pago');
        $I->fillField(['name' => 'name_condition'], 'PROBANDO');
        $I->click('Guardar');
        $I->wait(2);
        $I->see('Condición de pago modificada!');
        $I->wait(1);
        $I->see('PROBANDO');

    }

    /**
     * @group ana.mora
     */
    public function addPaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $I->click('Agregar Condición de Pago');
        $I->wait(3);
        $I->see('Agregar/Editar Condición de Pago');
        $I->fillField(['name' => 'name_condition'], 'Nueva condición de pago');
        $I->click('Guardar');
        $I->wait(2);
        $I->see('Condición de pago creada!');
        $I->wait(1);
        $I->see('Nueva condición de pago');
        $I->wait(1);

    }

    /**
     * @group ana.mora
     */
    public function deletePaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $payment_id = $I->grabFromDatabase('payment_conditions', 'id_payment_conditions', array('name_condition' => 'Pago en 3 meses'));

        $varPath = "a[onClick='return doDelete(" . $payment_id . ")']";
        $I->click('.icon-trash',$varPath);

        $I->wait(3);

        $I->see('Está seguro?');
        $I->click('.confirm','.sa-button-container');
        $I->wait(3);
        $I->see('El registro ha sido eliminado');
        $I->click('.confirm','.sa-button-container');
        $I->wait(1);
        $I->dontSee('Pago en 3 meses');
        $I->wait(1);

    }

    /**
     * @group ana.mora
     */
    public function noDeletePaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $payment_id = $I->grabFromDatabase('payment_conditions', 'id_payment_conditions', array('name_condition' => 'Pago en 3 meses'));

        $varPath = "a[onClick='return doDelete(" . $payment_id . ")']";
        $I->click('.icon-trash',$varPath);

        $I->wait(3);

        $I->see('Está seguro?');
        $I->click('.cancel','.sa-button-container');
        $I->wait(3);
        $I->see('Pago en 3 meses');
        $I->wait(1);
        $I->amOnPage('/paymentconditions');
        $I->wait(3);
        $I->see('Pago en 3 meses');

    }


    /**
     * @group ana.mora
     */
    public function addErrorPaymentCondition(AcceptanceTester $I)
    {
        $this->listPaymentCondition($I);
        $I->click('Agregar Condición de Pago');
        $I->wait(3);
        $I->see('Agregar/Editar Condición de Pago');
        $I->fillField(['name' => 'name_condition'], '');
        $I->click('Guardar');
        $I->wait(2);
        $I->seeInSource('<li>Complete este campo');
        $I->wait(1);

    }


}

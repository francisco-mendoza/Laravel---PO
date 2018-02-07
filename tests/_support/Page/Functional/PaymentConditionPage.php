<?php
namespace Page\Functional;

class PaymentConditionPage
{
    // include url of current page
    public static $URL = '/paymentconditions';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public static $formFields = ['name_condition' => 'name_condition'];

    /**
     * @var \FunctionalTester;
     */
    protected $functionalTester;

    public function __construct(\FunctionalTester $I)
    {
        $this->functionalTester = $I;
    }


    public function listPaymentCondition()
    {
        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
    }

    public function detailPaymentCondition($id){
        $I = $this->functionalTester;
        $I->amOnPage(self::route("/$id"));
        $I->see('Detalle de Condición de Pago');
        $I->see('Id de Condición de Pago');
        $I->see('Condición de Pago');
    }

    public function createPaymentCondition($fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
        $I->click('Agregar Condición de Pago');
        $I->seeCurrentUrlEquals(static::$URL . '/create' );
        $I->see('Agregar/Editar Condición de Pago');
        $this->fillFormFields($fields);
        $I->click('Guardar');
        
    }

    public function editPaymentCondition($id, $fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::route("/$id/edit"));
        $I->see('Agregar/Editar Condición de Pago');
        $this->fillFormFields($fields);
        $I->click('Guardar');
    }

    public function deletePaymentCondition($id, $token){
        $I = $this->functionalTester;        
        $I->sendAjaxRequest('DELETE', static::route("/$id"), array('id' => $id, '_method' => 'DELETE', '_token' => $token));

    }

    protected function fillFormFields($data)
    {
        foreach ($data as $field => $value) {
            if (!isset(static::$formFields[$field])) {
                throw new \Exception("Form field  $field does not exist");
            }
            $this->functionalTester->fillField(static::$formFields[$field], $value);
        }
    }

}

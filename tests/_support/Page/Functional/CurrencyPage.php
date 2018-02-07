<?php
namespace Page\Functional;

class CurrencyPage
{
    // include url of current page
    public static $URL = '/currencies';

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

    public static $formFields = ['name_currency' => 'name_currency',
                                 'short_name' => 'short_name', 'code' => 'code'   ];

    /**
     * @var \FunctionalTester;
     */
    protected $functionalTester;

    public function __construct(\FunctionalTester $I)
    {
        $this->functionalTester = $I;
    }

    public function listCurrency()
    {
        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
    }

    public function detailCurrency($id){
        $I = $this->functionalTester;
        $I->amOnPage(self::route("/$id"));
        $I->see('Detalle Moneda');
        $I->see('Id Moneda');
        $I->see('Nombre Moneda');
        $I->see('Sigla');
        $I->see('Código');
    }

    public function createCurrency($fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
        $I->click('Nueva Moneda');
        $I->seeCurrentUrlEquals(static::$URL . '/create' );
        $I->see('Agregar/Editar Moneda');
        $this->fillFormFields($fields);
        $I->click('Guardar');

    }

    public function editCurrency($id, $fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::route("/$id/edit"));
        $I->see('Agregar/Editar Moneda');
        $this->fillFormFields($fields);
        $I->click('Guardar');
    }

    public function deleteCurrency($id, $token){
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

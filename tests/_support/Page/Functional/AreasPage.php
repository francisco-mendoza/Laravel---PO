<?php
namespace Page\Functional;

class AreasPage
{
    // include url of current page
    public static $URL = '/areas';

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

    public static $formFields = ['short_name' => 'short_name',
                                 'long_name' => 'long_name',
                                 'manager_name' => 'manager_name',
                                 'manager_position' => 'manager_position',
                                 'id_user' => 'id_user',
                                 'budgets' => 'budgets',
                                 'budget_closed' => 'budget_closed'
                                ];

    /**
     * @var \FunctionalTester;
     */
    protected $functionalTester;

    public function __construct(\FunctionalTester $I)
    {
        $this->functionalTester = $I;
    }

    public function listAreas()
    {
        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
    }

    public function detailArea($id){
        $I = $this->functionalTester;
        $I->amOnPage(self::route("/$id"));
        $I->see('Detalle de Área');
        $I->see('Id Area');
        $I->see('Abreviatura');
        $I->see('Nombre de Área');
        $I->see('Gerente');
        $I->see('Cargo Gerente');
        $I->see('Presupuestos Anuales');
        $I->see('Presupuesto Anual');
        $I->see('Año');
        $I->see('Monto Disponible');
    }

    public function createArea($fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
        $I->click('Agregar Área');
        $I->seeCurrentUrlEquals(static::$URL . '/create' );
        $I->see('Agregar/Editar Área');
        $I->see('Presupuestos Anuales por Cuenta');
        $this->fillFormFields($fields);
        $I->click('Guardar');

    }

    public function editArea($id, $fields = []){

        $I = $this->functionalTester;
        $I->amOnPage(static::route("/$id/edit"));
        $I->see('Agregar/Editar Área');
        $I->see('Presupuestos Anuales por Cuenta');
        $this->fillFormFields($fields);
        $I->click('Guardar');
    }

    public function deleteArea($id, $token){
        $I = $this->functionalTester;
        $I->sendAjaxRequest('DELETE', static::route("/$id"), array('id' => $id, '_method' => 'DELETE', '_token' => $token));

    }

    protected function fillFormFields($data)
    {
        foreach ($data as $field => $value) {
            if (!isset(static::$formFields[$field])) {
                throw new \Exception("Form field  $field does not exist");
            }
            if($field == 'budget_closed'){
                $this->functionalTester->uncheckOption(static::$formFields[$field]);
            }else{
                $this->functionalTester->fillField(static::$formFields[$field], $value);
            }
        }
    }

}

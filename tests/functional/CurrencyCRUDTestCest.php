<?php


use Page\Functional\Login;
use Page\Functional\CurrencyPage;

use App\Models\User;
//use Zizaco\Entrust;

class CurrencyCRUDTestCest
{
    private $userAttributes;
    private $currencyAttributes;

    private $idCurrency;
    private $tableName;

    public function __construct()
    {
        $random = rand(0,9);
        $this->postAttributes = ['name_currency' => 'Prueba de moneda nueva',
                                 'short_name' => 'MON', 'code' => 'MMM'   ];
    }
    
    public function _before(FunctionalTester $I)
    {
        $this->userAttributes = Login::getUserGoogle(9999);
        $I->haveRecord('users', $this->userAttributes);

        $this->currencyAttributes = ['name_currency' => 'New currency',
                                     'short_name' => 'CUR', 'code' => 'MMM'   ];

        $this->idCurrency = $I->haveRecord('currency', $this->currencyAttributes);

        $user = User::find($this->userAttributes['id_user'] );

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $I->amLoggedAs($user);
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();

        $this->tableName = 'currency';
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function listCurrency(FunctionalTester $I, CurrencyPage $currencyPage)
    {
        $currencyPage->listCurrency();
        $I->seeCurrentUrlEquals($currencyPage::$URL); //Usuario ve la página porque tiene los permisos adecuados
        $I->see('Lista de Monedas');
        $I->see('Nombre');
        $I->see('Sigla');
        $I->see('Código');
        $I->see('Editar');
        $I->see('Eliminar');
        $I->sendAjaxGetRequest('/currencies/grid',array('length' => '1'));

        $I->see($this->currencyAttributes['short_name']);
        $I->see($this->currencyAttributes['name_currency']);
        $I->see($this->currencyAttributes['code']);

    }

    public function detailCurrency(FunctionalTester $I, CurrencyPage $currencyPage){

        $currency = $I->grabRecord($this->tableName,  $this->currencyAttributes);
        $this->listCurrency($I,$currencyPage);
        $currencyPage->detailCurrency($currency['id_currency']);

        $I->seeInField('#id_currency', $currency['id_currency']);
        $I->seeInField('#name_currency', $currency['name_currency']);
        $I->seeInField('#short_name', $currency['short_name']);
        $I->seeInField('#code', $currency['code']);
    }

    public function createCurrency(FunctionalTester $I, CurrencyPage $currencyPage){

        $this->listCurrency($I,$currencyPage);
        $currencyPage->createCurrency($this->postAttributes);
        $I->see('Moneda creada!');
        $I->seeCurrentUrlEquals($currencyPage::$URL);
        $I->seeRecord($this->tableName,$this->postAttributes);
        $I->see('Lista de Monedas');

    }

    public function failCreationCurrency(FunctionalTester $I, CurrencyPage $currencyPage){
        $this->listCurrency($I,$currencyPage);
        $currencyPage->createCurrency();
        $I->seeCurrentUrlEquals($currencyPage::$URL . '/create' );
        $I->seeFormHasErrors();
        $I->seeFormErrorMessage('name_currency');
        $I->seeFormErrorMessage('short_name');
        $I->seeFormErrorMessage('code');

    }

    public function editCurrency(FunctionalTester $I, CurrencyPage $currencyPage){
        $this->listCurrency($I,$currencyPage);
        $randName = "Nuevo nombre";

        $newAttributes = $this->currencyAttributes;
        $newAttributes['name_currency'] = $randName;

        $currencyPage->editCurrency($this->idCurrency, $newAttributes);
        $I->seeCurrentUrlEquals($currencyPage::$URL);
        $I->see('Moneda modificada!');


        $I->seeRecord($this->tableName,$newAttributes);
        $I->dontSeeRecord($this->tableName,$this->currencyAttributes);
        
    }

    public function deleteCurrency(FunctionalTester $I, CurrencyPage $currencyPage){

        $this->listCurrency($I,$currencyPage);
        $I->seeRecord($this->tableName,$this->currencyAttributes);
        Session::start();
        $token = csrf_token();
        $currencyPage->deleteCurrency($this->idCurrency, $token);
        $I->seeCurrentUrlEquals($currencyPage::$URL);
        $I->dontSeeRecord($this->tableName,$this->currencyAttributes);

    }

}

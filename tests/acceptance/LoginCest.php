<?php
use App\Models\User;

class LoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function loginYapo(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->click('#btn_login');
        $I->see('compra');
        $I->fillField('#Email', 'francisco.mendoza@schibsted.cl');
        $I->click('#next');
        $I->fillField('#Passwd', 'Propro123');
        $I->click('#signIn');
        $I->wait(5);
        $I->amOnPage('/#');
        $I->see('Sistema de Órdenes de Compra','.content');
    }

    public function noYapo(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->click('#btn_login');
        $I->see('compra');
        $I->fillField('#Email', 'franciscomendozaroumat@gmail.com');
        $I->click('#next');
        $I->fillField('#Passwd', 'Fuckup666');
        $I->click('#signIn');
        $I->wait(2);
        $I->amOnPage('/login');
        $I->see('Dominio no autorizado.');
    }


}

<?php

use Page\Functional\Login;
use App\Models\User;

class LoginCest
{
    private $userAttributes;
    private $controller;

    public function _before(FunctionalTester $I)
    {
        $this->userAttributes = Login::getUserGoogle(9999);
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests

    //Click Login
    public function loginUser(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Entrar con Google');
        $I->click('#btn_login');
    }

    //Usuario ya registrado y logeado con Google
    public function registerUser(FunctionalTester $I)
    {
        $I->haveRecord('users', $this->userAttributes);
        $user = User::find($this->userAttributes['id_user']);
        $I->amLoggedAs($user);
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();
        $I->amOnPage('/');
        $I->see('Sistema de Órdenes de Compra');
    }

    //Usuario registrado pero aún no se ha logeado con Google
    public function newGoogleUser(FunctionalTester $I)
    {
        $I->haveRecord('users', $this->userAttributes);
        $user = User::find($this->userAttributes['id_user']);
        $I->amLoggedAs($user);
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();
    }

    //Usuario que nunca había sido registrado ni loeado con Google
    public function newUser(FunctionalTester $I)
    {
        $I->amLoggedAs(User::create($this->userAttributes));
        $I->amOnPage(Login::$URL);
        $I->seeAuthentication();
    }
}

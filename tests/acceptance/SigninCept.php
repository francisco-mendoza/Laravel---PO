<?php
$I = new AcceptanceTester($scenario);
//$I->am('user');
//$I->wantTo('login to website');
//$I->lookForwardTo('access all website features');
$I->amOnPage('/login');
//$I->fillField('Username','davert');
//$I->fillField('Password','qwerty');
$I->click('#btn_login');
$I->see('compra');
$I->fillField('#Email', 'francisco.mendoza@schibsted.cl');
$I->click('#next');
$I->fillField('#Passwd', 'Propro123');
$I->click('#signIn');
$I->wait(3);
$I->see('Inicio');

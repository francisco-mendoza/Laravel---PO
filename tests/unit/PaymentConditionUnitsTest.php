<?php

use App\Models\PaymentCondition;

use App\Http\Controllers\PaymentConditionController;


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class PaymentConditionUnitsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $defaultAttributes;
    protected $idPaymentCondition;
    protected $mock;

    protected function _construct(){
        // We have no interest in testing Eloquent
    }

    protected function _before()
    {
        $this->defaultAttributes = [ 'name_condition' => 'Pago en 3 meses' ];
        $this->idPaymentCondition = $this->tester->haveRecord('payment_conditions', $this->defaultAttributes);

//        parent::setUp();
//
//        Session::start();

        // Enable filters
//        Route::enableFilters();

    }

    protected function _after()
    {
    }


    //tests
    public function testGetPaymentConditions(){

        $response = (object) PaymentCondition::PaymentConditions(0,10,'Pago',0,'asc');
        verify($response[0]->name_condition)->contains($this->defaultAttributes['name_condition']);
        
    }

    public function testDontGetPaymentCondition(){
        $str = rand(0,100) . 'name';
        $response = (object) PaymentCondition::PaymentConditions(0,10,$str,0,'asc');
        verify($response)->isEmpty();
    }

    public function testDontFindPaymentCondition(){
        $response = (object) PaymentCondition::PaymentConditions(100,10,'Pago',0,'asc');
        verify($response)->isEmpty();
    }

    public function testCountPaymentCondition(){

        $count = $this->tester->grabNumRecords('payment_conditions');
        $countModel = PaymentCondition::getCountPaymentConditions();
        verify($count)->equals($countModel);
    }
    
    
}
<?php
namespace Page\Functional;

use App\Models\AccountBudget;
use App\Models\AreasBudget;
use Codeception\Scenario;
use Illuminate\Http\Request;
use App\Http\Controllers\PurchaseOrderController;
use Page\TestData;

class PurchaseOrderPage
{
    // include url of current page
    public static $URL = '/detailPurchaseOrder';
    public static $URL_CONSULTORDER = '/consultarOrdenes';
    public static $URL_GETPROVIDER = '/getProviderInfo/PERSONAL%20COMPUTER%20FACTORY%20S.A.';

    const PurchaseOrderVisible = 1;
    const PurchaseOrderNoVisible = 0;

    public static $formFields = [
        'name_provider'     => 'name_provider',
        'payment_condition' => 'payment_condition',
        'payment_method'    => 'payment_method',
        'currency'          => 'currency',
        'tipo_boleta'       => 'tipo_boleta',
        'cant_1'            => 'cant_1',
        'desc_1'            => 'desc_1',
        'month_ini_1'       => 'month_ini_1',
        'priceWithoutIva_1' => 'priceWithoutIva_1',
        'total_sin_iva'     => 'total_sin_iva',
        'total'             => 'total'
    ];



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

    /**
     * @var \FunctionalTester;
     */
    protected $functionalTester;

    public function __construct(\FunctionalTester $I)
    {
        $this->functionalTester = $I;

        $MetodosPago = new \stdClass();
        $MetodosPago->treintaDias = 1;
        $MetodosPago->alDia = 2;


    }
    public function listPurchaseOrder()
    {
        $I = $this->functionalTester;
        $I->amOnPage(static::$URL);
    }

    public function detailPurchaseOrder($id){
        $I = $this->functionalTester;
        $I->amOnPage(self::route("/$id"));
        $I->see('Orden de Compra');
        $I->see('Proveedor:');
        $I->see('Persona de Contacto:');
    }

    public function createPurchaseOrder($type,$name_provider,$contract){

        $respuesta = "";

        $I = $this->functionalTester;
        $I->amOnPage(static::$URL_CONSULTORDER);
        $I->click('Agregar Orden de Compra');
        $I->seeCurrentUrlEquals('/crearOrdenes');
        $I->see('Nueva Orden de Compra');
        //$this->fillFormFields($fields);
        $I->fillField("name_provider",TestData::Provider['name_provider']);
        $I->selectOption("payment_condition",TestData::Provider['payment_conditions']);
        $I->selectOption("payment_method",TestData::Provider['payment_method']);
        $I->selectOption("currency","2");
        $I->selectOption("tipo_boleta",1);
        $I->click('#add_product');

        $request = self::getTestPurchaseOrderRequest($type);

        $PurchaseOrderControler = new PurchaseOrderController();
        $validate = json_encode($PurchaseOrderControler->validatePurchaseOrders($request)->getData(true));
        $validate_array = json_decode($validate,true);
//        dd($validate_array);

        $actual_account_budget = AccountBudget::getBudget(8888,'2017','7777');
        $actual_areas_budget = AreasBudget::getBudget(8888,'2017');

        $currency = "2"; //CLP
        if($type == 5){
            $currency = "3";  //Dolar
        }
        $purchase_order_detail_select = null;

        $data_form = [
            '_token'            => csrf_token(),
            'name_provider'     => $name_provider,
            'payment_condition' => TestData::Provider['payment_conditions'],
            'payment_method'    => TestData::Provider['payment_method'],
            'currency'          => $currency,
            'tipo_boleta'       => 1,
            'total_sin_iva'     => TestData::purchaseOrderFields['total_price'],
            'total'             => TestData::purchaseOrderFields['total_iva_price'],
            'ordersByMonth'     => $validate,
        ];

        $I->submitForm('#f_orden', $data_form);
        //$I->SeeRecord('purchase_order_detail',['description'=>'mouse']);
        //dd((float)TestData::purchaseOrderFields['total_price']);
        $n = $I->grabNumRecords('purchase_order',[
            'id_user'=>9999,
            'id_area'=>8888,
            'id_contract'=>$contract,
            'id_payment_condition'=>TestData::Provider['payment_conditions'],
            'id_payment_method'=>TestData::Provider['payment_method'],
            'total_price'=>16000,
//            'total_iva_price'=>TestData::purchaseOrderFields['total_iva_price'],
            'id_currency'=>2,
        ]);


        //dd($validate_array);
        $precio_todas_ordenes_compra = 0;
        $count = 1;
        $exchange_rate = 0;
        foreach ($validate_array as $value){

//            dd($value);
            $quantity = null;
            $price = null;
            $price_iva = null;

            //todo: Caso 1 || Si es marzo (3) debe llevar dos details y el total de la OC debe ser la suma de estos,
            if($type == 1){

                if($count == 3){
                    $I->assertFalse(count($value) != 2,"No se estan entregando 2 detalles");

                    $price = $value[0]['price'] + $value[1]['price'];
                    $price_iva = $value[0]['price_iva'] + $value[1]['price_iva'];
                }else{
                    $price = $value[0]['price'];
                    $price_iva = $value[0]['price_iva'];
                }

                //Validamos que exista el registro en purchase_order en la BD
                $purchase_order = $I->grabRecord('purchase_order',[
                    'id_user'=>9999,
                    'id_area'=>8888,
                    'id_contract'=>$contract,
                    'id_payment_condition'=>TestData::Provider['payment_conditions'],
                    'id_payment_method'=>TestData::Provider['payment_method'],
                    'total_price'=>$price,
                    'total_iva_price'=>$price_iva,
                    'id_currency'=>2,
                ]);

                //Verificar que quede visible la oc
                $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);

                $purchase_order_detail_select = $purchase_order['folio_number'];

                $purchase_order_detail_record = $I->grabNumRecords('purchase_order_detail',[
                    'id_purchase_order'=>$purchase_order['folio_number'],
                ]);
                if($count == 3){
                    $I->assertFalse($purchase_order_detail_record != 2,"No se estan entregando 3 detalles");
                }
            }
            //Todo: Caso 2 || Dos OC con detalles y totales independientes
            elseif ($type == 2){
                if($count == 4 || $count == 6){
                    $price = $value[0]['price'];
                    if($count == 6){
                        $price_iva = $value[0]['price'];
                    }else{
                        $price_iva = $value[0]['price_iva'];
                    }

                    //Validamos que exista el registro en purchase_order en la BD
                    $purchase_order = $I->grabRecord('purchase_order',[
                        'id_user'=>9999,
                        'id_area'=>8888,
                        'id_contract'=>$contract,
                        'id_payment_condition'=>TestData::Provider['payment_conditions'],
                        'id_payment_method'=>TestData::Provider['payment_method'],
                        'total_price'=>$price,
                        'total_iva_price'=>$price_iva,
                        'id_currency'=>2,
                    ]);
                    $purchase_order_detail_select = $purchase_order['folio_number'];
                    $purchase_order_detail_record = $I->grabNumRecords('purchase_order_detail',[
                        'id_purchase_order'=>$purchase_order['folio_number'],
                    ]);

                    //Verificar que quede visible la oc
                    $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);
                }

            }
            //Todo: Caso 3 || 1 OC con 3 detalles y el total la suma de ellos
            elseif ($type == 3){
                if($count == 8){
                    //dd($value);
                    $price = $value[0]['price'] + $value[1]['price']+ $value[2]['price'];
                    $price_iva = $value[0]['price_iva'] + $value[1]['price_iva']+ $value[2]['price_iva'];
                    //Validamos que exista el registro en purchase_order en la BD
                    $purchase_order = $I->grabRecord('purchase_order',[
                        'id_user'=>9999,
                        'id_area'=>8888,
                        'id_contract'=>$contract,
                        'id_payment_condition'=>TestData::Provider['payment_conditions'],
                        'id_payment_method'=>TestData::Provider['payment_method'],
                        'total_price'=>$price,
                        'total_iva_price'=>$price_iva,
                        'id_currency'=>2,
                    ]);

                    //Verificar que quede visible la oc
                    $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);

                    $purchase_order_detail_record = $I->grabNumRecords('purchase_order_detail',[
                        'id_purchase_order'=>$purchase_order['folio_number'],
                    ]);
                    $purchase_order_detail_select = $purchase_order['folio_number'];
                    $I->assertFalse($purchase_order_detail_record != 3,"No se estan entregando 3 detalles");
                }
               // $precio_todas_ordenes_compra = $precio_todas_ordenes_compra + $price_iva;

            }
            //todo: Caso 4 ||En julio(7) 1 OC con 2 detalles y el total la suma de ellos
            elseif ($type == 4 && $count>2 && $count<9){
                if($count == 6){
                    $price = $value[0]['price'] + $value[1]['price'];
                    $price_iva = $value[0]['price_iva'] + $value[1]['price_iva'];
                }else{
                    $price = $value[0]['price'];
                    $price_iva = $value[0]['price_iva'];
                }


                //Validamos que exista el registro en purchase_order en la BD
                $purchase_order = $I->grabRecord('purchase_order',[
                    'id_user'=>9999,
                    'id_area'=>8888,
                    'id_contract'=>$contract,
                    'id_payment_condition'=>TestData::Provider['payment_conditions'],
                    'id_payment_method'=>TestData::Provider['payment_method'],
//                    'total_price'=>$price,
                    'total_iva_price'=>$price_iva,
                    'id_currency'=>2,
                ]);
                $purchase_order_detail_record = $I->grabNumRecords('purchase_order_detail',[
                    'id_purchase_order'=>$purchase_order['folio_number'],
                ]);

                //Verificar que quede visible la oc
                $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);
                $purchase_order_detail_select = $purchase_order['folio_number'];
                if($count == 6){
                    $I->assertFalse($purchase_order_detail_record != 2,"No se estan guardando 2 detalles");
                }

            }
            //todo: Caso 5 ||En Abril 1 OC con moneda en dolares
            elseif($type == 5){
                if($count == 4){
                    $price = $value[0]['price'];
                    if($count == 6){
                        $price_iva = $value[0]['price'];
                    }else{
                        $price_iva = $value[0]['price_iva'];
                    }

                    //Validamos que exista el registro en purchase_order en la BD
                    $purchase_order = $I->grabRecord('purchase_order',[
                        'id_user'=>9999,
                        'id_area'=>8888,
                        'id_contract'=>$contract,
                        'id_payment_condition'=>TestData::Provider['payment_conditions'],
                        'id_payment_method'=>TestData::Provider['payment_method'],
                        'total_price'=>$price,
                        'total_iva_price'=>$price_iva,
                        'id_currency'=>3,
                    ]);
                    //Verificar que quede visible la oc
                    $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);
                    $purchase_order_detail_select = $purchase_order['folio_number'];
                    $exchange_rate = (float)$purchase_order['exchange_rate'];

                }
            }
            //todo: Caso 6 ||En Abril 1 OC con monto mayor al disponible
            elseif($type == 6){
                if($count == 4){
                    $price = $value[0]['price'];
                    $price_iva = $value[0]['price_iva'];
                }
            }
            //todo: Caso 7 || Prueba de rechazo orden compra
            elseif ($type == 7){
                if($count == 8){
                    //dd($value);
                    $price = $value[0]['price'] + $value[1]['price']+ $value[2]['price'];
                    $price_iva = $value[0]['price_iva'] + $value[1]['price_iva']+ $value[2]['price_iva'];
                    //Validamos que exista el registro en purchase_order en la BD
                    $purchase_order = $I->grabRecord('purchase_order',[
                        'id_user'=>9999,
                        'id_area'=>8888,
                        'id_contract'=>$contract,
                        'id_payment_condition'=>TestData::Provider['payment_conditions'],
                        'id_payment_method'=>TestData::Provider['payment_method'],
                        'total_price'=>$price,
                        'total_iva_price'=>$price_iva,
                        'id_currency'=>2,
                    ]);

                    //Verificar que quede visible la oc
                    $I->assertTrue($purchase_order['is_visible'] == self::PurchaseOrderVisible);

                    $purchase_order_detail_record = $I->grabNumRecords('purchase_order_detail',[
                        'id_purchase_order'=>$purchase_order['folio_number'],
                    ]);
                    $purchase_order_detail_select = $purchase_order['folio_number'];
                    $I->assertFalse($purchase_order_detail_record != 3,"No se estan entregando 3 detalles");

                    $respuesta = $purchase_order;
                }
                // $precio_todas_ordenes_compra = $precio_todas_ordenes_compra + $price_iva;
            }

            $precio_todas_ordenes_compra = $precio_todas_ordenes_compra + $price;

            $count ++;
        }

        $accountBudget = AccountBudget::getBudget(8888,'2017','7777');
        $areasBudget = AreasBudget::getBudget(8888,'2017');

        $diferencia_account_budget = $actual_account_budget->total_budget_available - $accountBudget->total_budget_available;
        $diferencia_area_budget = $actual_areas_budget->total_budget_available - $areasBudget->total_budget_available;

        if($type==5){
            $precio_todas_ordenes_compra = number_format($precio_todas_ordenes_compra*$exchange_rate,2);
            $diferencia_account_budget = number_format($diferencia_account_budget,2);
            $diferencia_area_budget = number_format($diferencia_area_budget,2);
        }


       // dd("1: ".$diferencia_account_budget. " / 2: ".$precio_todas_ordenes_compra);
        $I->see('Consultar Órdenes de Compra');
        if($type == 6){
            //Verificar que no se haya descontado nada
            $I->assertFalse($diferencia_account_budget  == $precio_todas_ordenes_compra,"El monto total no coincide con el descuento del budget de la cuenta");
            $I->assertFalse($diferencia_area_budget  == $precio_todas_ordenes_compra,"El monto total no coincide con el descuento del budget del area");
            $I->see("Ha ocurrido un error. Los montos disponibles de la cuenta o el área han quedado en negativo");
        }else{
            $I->assertFalse($diferencia_account_budget  != $precio_todas_ordenes_compra,"El monto total no coincide con el descuento del budget de la cuenta");
            $I->assertFalse($diferencia_area_budget  != $precio_todas_ordenes_compra,"El monto total no coincide con el descuento del budget del area");
            $I->see('Orden de Compra creada!');
            $this->detailPurchaseOrder($purchase_order_detail_select);
        }

        return $respuesta;


    }

    public function createPurchaseOrderForEdit($name_provider,$contract){
        $I = $this->functionalTester;
        $I->amOnPage(static::$URL_CONSULTORDER);
        $I->click('Agregar Orden de Compra');
        $I->seeCurrentUrlEquals('/crearOrdenes');
        $I->see('Nueva Orden de Compra');
        $I->fillField("name_provider",TestData::Provider['name_provider']);
        $I->selectOption("payment_condition",TestData::Provider['payment_conditions']);
        $I->selectOption("payment_method",TestData::Provider['payment_method']);
        $I->selectOption("currency","2");
        $I->selectOption("tipo_boleta",1);
        $I->click('#add_product');

        $type = 5;

        $request = self::getTestPurchaseOrderRequest($type);

        $PurchaseOrderControler = new PurchaseOrderController();
        $validate = json_encode($PurchaseOrderControler->validatePurchaseOrders($request)->getData(true));

        $currency = "2"; //CLP

        $purchase_order_detail_select = null;

        $data_form = [
            '_token'            => csrf_token(),
            'name_provider'     => $name_provider,
            'payment_condition' => TestData::Provider['payment_conditions'],
            'payment_method'    => TestData::Provider['payment_method'],
            'currency'          => $currency,
            'tipo_boleta'       => 1,
            'total_sin_iva'     => TestData::purchaseOrderFields['total_price'],
            'total'             => TestData::purchaseOrderFields['total_iva_price'],
            'ordersByMonth'     => $validate,
        ];

        $I->submitForm('#f_orden', $data_form);

        //Validamos que exista el registro en purchase_order en la BD
        $purchase_order = $I->grabRecord('purchase_order',[
            'id_user'=>9999,
            'id_area'=>8888,
            'id_contract'=>$contract,
            'id_payment_condition'=>TestData::Provider['payment_conditions'],
            'id_payment_method'=>TestData::Provider['payment_method'],
//            'total_price'=>$price,
//            'total_iva_price'=>$price_iva,
            'id_currency'=>2,
        ]);

        return $purchase_order;
    }


    public static function getTestPurchaseOrderRequest($type){
        $request = new Request();
        $request['_token']            = csrf_token();
        $request['name_provider']     = TestData::Provider['name_provider'];
        $request['payment_condition'] = TestData::Provider['payment_conditions'];
        $request['payment_method']    = TestData::Provider['payment_method'];
        $request['currency']          = "2";
        $request['tipo_boleta']       = 1;
        $request['total_sin_iva']     = TestData::purchaseOrderFields['total_price'];
        $request['total']             = TestData::purchaseOrderFields['total_iva_price'];

        switch ($type){
            case 1:
                $request['count_detail']      = 2;

                $request['cant_1']            = 1;
                $request['desc_1']            = TestData::orderDetail_1['description'];
                $request['month_ini_1']       = TestData::rangeAnual['month_ini'];
                $request['month_1']           = TestData::rangeAnual['month'];
                $request['month_end_1']       = TestData::rangeAnual['month_end'];
                $request['priceWithoutIva_1'] = TestData::orderDetail_1['price'];
                $request['iva_1']             = TestData::orderDetail_1['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_1['price_iva'];

                $request['cant_2']            = TestData::orderDetail_2['quantity'];
                $request['desc_2']            = TestData::orderDetail_2['description'];
                $request['month_ini_2']       = 3;
                $request['month_2']           = 'off';
                $request['priceWithoutIva_2'] = TestData::orderDetail_2['price'];
                $request['iva_2']             = TestData::orderDetail_2['has_iva'];
                $request['priceWithIva_2']    = TestData::orderDetail_2['price_iva'];
                break;
            case 2:
                $request['count_detail']      = 2;

                $request['cant_1']            = TestData::orderDetail_1['quantity'];
                $request['desc_1']            = TestData::orderDetail_1['description'];
                $request['month_ini_1']       = 4;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_1['price'];
                $request['iva_1']             = TestData::orderDetail_1['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_1['price_iva'];

                $request['cant_2']            = TestData::orderDetail_3['quantity'];
                $request['desc_2']            = TestData::orderDetail_3['description'];
                $request['month_ini_2']       = 6;
                $request['month_2']           = 'off';
                $request['priceWithoutIva_2'] = TestData::orderDetail_3['price'];
                $request['iva_2']             = TestData::orderDetail_3['has_iva'];
                $request['priceWithIva_2']    = TestData::orderDetail_3['price_iva'];
                break;
            case 3:
                $request['count_detail']      = 3;

                $request['cant_1']            = TestData::orderDetail_1['quantity'];
                $request['desc_1']            = TestData::orderDetail_1['description'];
                $request['month_ini_1']       = 8;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_1['price'];
                $request['iva_1']             = TestData::orderDetail_1['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_1['price_iva'];

                $request['cant_2']            = TestData::orderDetail_3['quantity'];
                $request['desc_2']            = TestData::orderDetail_3['description'];
                $request['month_ini_2']       = 8;
                $request['month_2']           = 'off';
                $request['priceWithoutIva_2'] = TestData::orderDetail_3['price'];
                $request['iva_2']             = 'on';
                $request['priceWithIva_2']    = TestData::orderDetail_3['price_iva'];

                $request['cant_3']            = TestData::orderDetail_2['quantity'];
                $request['desc_3']            = TestData::orderDetail_2['description'];
                $request['month_ini_3']       = 8;
                $request['month_3']           = 'off';
                $request['priceWithoutIva_3'] = TestData::orderDetail_2['price'];
                $request['iva_3']             = 'on';
                $request['priceWithIva_3']    = TestData::orderDetail_2['price_iva'];
                break;
            case 4:
                $request['count_detail']      = 2;

                $request['cant_1']            = TestData::orderDetail_1['quantity'];
                $request['desc_1']            = TestData::orderDetail_1['description'];
                $request['month_ini_1']       = 6;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_1['price'];
                $request['iva_1']             = TestData::orderDetail_1['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_1['price_iva'];

                $request['cant_2']            = 1;
                $request['desc_2']            = TestData::orderDetail_2['description'];
                $request['month_ini_2']       = 3;
                $request['month_2']           = 'on';
                $request['month_end_2']       = 8;
                $request['priceWithoutIva_2'] = TestData::orderDetail_2['price'];
                $request['iva_2']             = TestData::orderDetail_2['has_iva'];
                $request['priceWithIva_2']    = TestData::orderDetail_2['price_iva'];
                break;
            case 5:
                $request['count_detail']      = 1;

                $request['cant_1']            = TestData::orderDetail_4['quantity'];
                $request['desc_1']            = TestData::orderDetail_4['description'];
                $request['month_ini_1']       = 4;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_4['price'];
                $request['iva_1']             = TestData::orderDetail_4['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_4['price_iva'];

                break;
            case 6:
                $request['count_detail']      = 1;

                $request['cant_1']            = TestData::orderDetail_5['quantity'];
                $request['desc_1']            = TestData::orderDetail_5['description'];
                $request['month_ini_1']       = 4;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_5['price'];
                $request['iva_1']             = TestData::orderDetail_5['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_5['price_iva'];

                break;
            case 7:
                $request['count_detail']      = 3;

                $request['cant_1']            = TestData::orderDetail_1['quantity'];
                $request['desc_1']            = TestData::orderDetail_1['description'];
                $request['month_ini_1']       = 8;
                $request['month_1']           = 'off';
                $request['priceWithoutIva_1'] = TestData::orderDetail_1['price'];
                $request['iva_1']             = TestData::orderDetail_1['has_iva'];
                $request['priceWithIva_1']    = TestData::orderDetail_1['price_iva'];

                $request['cant_2']            = TestData::orderDetail_3['quantity'];
                $request['desc_2']            = TestData::orderDetail_3['description'];
                $request['month_ini_2']       = 8;
                $request['month_2']           = 'off';
                $request['priceWithoutIva_2'] = TestData::orderDetail_3['price'];
                $request['iva_2']             = 'on';
                $request['priceWithIva_2']    = TestData::orderDetail_3['price_iva'];

                $request['cant_3']            = TestData::orderDetail_2['quantity'];
                $request['desc_3']            = TestData::orderDetail_2['description'];
                $request['month_ini_3']       = 8;
                $request['month_3']           = 'off';
                $request['priceWithoutIva_3'] = TestData::orderDetail_2['price'];
                $request['iva_3']             = 'on';
                $request['priceWithIva_3']    = TestData::orderDetail_2['price_iva'];
                break;

                break;
        }

        return $request;
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

<?php
namespace Page;

class TestData
{
    // include url of current page
    //public static $URL = '';
    
    const purchaseOrderFields = [
        'id_payment_condition' => 1,
        'id_payment_method'    => 1,
        'id_currency'          => 2,
        'folio_number'      => 'XXXX_LLLL_YYYY_2017',
        'id_area'           => 1,
        'id_user'           => 10,
        'id_contract'       => 22,
        'total_price'       =>'123456789',
        'total_iva_price'   =>'123456789',
        'order_state'       =>'Emitida' 
    ];

    const oldPurchaseOrderFields = [
        'id_payment_condition' => 1,
        'id_payment_method'    => 1,
        'id_currency'          => 2,
        'folio_number'      => 'XXXX_VIEJA_111_2017',
        'id_area'           => 1,
        'id_user'           => 10,
        'id_contract'       => 22,
        'total_price'       =>'123456789',
        'total_iva_price'   =>'123456789',
        'order_state'       =>'Emitida', 
        'is_visible'        => '1',
        'old_folio_number'  => 'XXXX_LLLL_YYYY_2017'
    ];
    
    const purchaseOrderDetailFields = [
        'quantity' => 4,
        'description' => 'Panes',
        'price' => 500.00,
        'has_iva' => 1,
        'price_iva' => 595.00,
        'id_currency' => 2
    ];

    const oldPurchaseOrderDetailFields = [
        'quantity' => 4,
        'description' => ' Panes',
        'price' => 500.00,
        'has_iva' => 1,
        'price_iva' => 595.00,
        'id_currency' => 2
    ];

    const rangeAnual = [
        'month_ini'  => 1,
        'month'      => 'on',
        'month_end'  => 12
    ];

    const orderDetail_1 = [
        'quantity' => 8,
        'description' => 'Celulares',
        'price' => 1000,
        'has_iva' => 'on',
        'price_iva' => 1190
    ];

    const orderDetail_2 = [
        'quantity' => 30,
        'description' => 'Mouse',
        'price' => 500,
        'has_iva' => 'on',
        'price_iva' => 595,
    ];

    const orderDetail_3 = [
        'quantity' => 4,
        'description' => 'Audifonos',
        'price' => 300,
        'has_iva' => 'off',
        'price_iva' => 357
    ];

    const orderDetail_4 = [
        'quantity' => 2,
        'description' => 'Celulares',
        'price' => 100,
        'has_iva' => 'on',
        'price_iva' => 119
    ];
    const orderDetail_5 = [
        'quantity' => 2,
        'description' => 'Celulares',
        'price' => 6000000,
        'has_iva' => 'on',
        'price_iva' => 7140000
    ];

    const areaFields = [
        'id_area' => 5555,
        'short_name' => 'APTUUU',
        'long_name' => 'Area de Prueba Test Unitario',
        'manager_name' => 'Ana',
        'manager_position' => 'Gerente',
        'budget_closed' => '0',
    ];

    const areaClosedFields = [
        'id_area' => 5555,
        'short_name' => 'APTUUU',
        'long_name' => 'Area de Prueba Test Unitario',
        'manager_name' => 'Ana',
        'manager_position' => 'Gerente',
        'budget_closed' => '1',
    ];

    const areaBudgetFields = [
        'id_area' => 5555,
        'budget_year' => '2017',
        'total_budget_initial' => '10000.00',
        'total_budget_available' => '10000.00',
    ];

    const areaBudgetFields_2 = [
        'id_area' => 5555,
        'budget_year' => '2017',
        'total_budget_initial' => '20000.00',
        'total_budget_available' => '20000.00',
    ];

    const contractFields = [
        'id_contract' => 55555,
        'id_provider' => 1,
        'contract_number' => 'A123TEST',
        'description' => 'Contrato de Prueba Test Unitario',
        'end_date' => '2017-02-15',
        'is_active' => 1,
    ];

    const invoiceFields = [
        'id_provider'=>1,
        'id_area'=>'5555',
        'id_document'=>'FAC_TEST_123',
        'billing_day'=>12,
        'billing_month'=>1,
        'billing_year'=>2017,
        'id_currency'=>2,
        'total'=>100.00,
        'total_iva'=>119.00,
    ];


    const accountBudgetFields_1 = [
        'id_area' => 5555,
        'budget_year' => '2017',
        'account_name' => 'Cuenta test unitarios - CTU',
        'account_code' => '6060',
        'description' => 'Cuenta de area - test unitarios',
        'total_budget_initial' => '30000.00',
        'total_budget_available' => '30000.00',
    ];

    const accountBudgetFields_2 = [
        'id_area' => 5555,
        'budget_year' => '2017',
        'account_name' => 'Cuenta 2 test unitarios - CTU',
        'account_code' => '6090',
        'description' => 'Cuenta 2 de area - test unitarios',
        'total_budget_initial' => '40000.00',
        'total_budget_available' => '40000.00',
    ];

    const userFields = [
        'id_user' => 99999,
        'username' => 'anamora',
        'password' => 'XXX',
        'firstname' => 'Ana',
        'lastname' => 'Mora',
        'email' => 'a.m@gmail.com',
        'remember_token' => 'xx',
        'social_provider_id' => 'xxx',
        'social_provider' => 'social_provider',
        'url_avatar' => 'foto',
    ];

    const Provider = [
        'name_provider'      => 'Ventas de pc Test',
        'business'           => 'Articulos de computacion',
        'rut'                => '99.999.999-9',
        'address'            => 'Huerfanos 123',
        'phone'              => '123456789',
        'contact_name'       => 'Juan Perez',
        'contact_area'       => 'Lider',
        'contact_email'      => 'juan@test.cl',
        'contact_phone'      => '213456789',
        'is_visible'         => 1,
        'payment_conditions' => 1,
        'payment_method'     => 1,
        'bank'               => 'Santander',
        'type_account'       => 'Corriente',
        'number_account'     => 9876541,
    ];

    const LoginUserRequest = [
        'id' => '321321321321',
        'email' => 'usuario_prueba@schibsted.cl',
        'user' => [
            'name'=>[
                'givenName'=>'Juan',
                'familyName'=>'Perez'
            ]
        ],
        'avatar' => 'google.com/avatar.jpg',
    ];

    const UserAttributes = [
            'id_user'           => 99999,
            'username'          => 'juan.perez',
            'firstname'         => 'Juan',
            'lastname'          => 'Perez',
            'rut'               => '1-9',
            'email'             => 'usuario_prueba@schibsted.cl',
            'social_provider'   => 'google',
            'social_provider_id'=> '321321321321',
            'url_avatar'        => 'google.com/avatar.jpg',
            'password'          => null,
            'id_area'           => 1
    ];

    const UserAttributesNoGoogleLogin = [
        'id_user'           => 99999,
        'username'          => 'juan.perez',
        'firstname'         => 'Juan',
        'lastname'          => 'Perez',
        'rut'               => '1-9',
        'email'             => 'usuario_prueba@schibsted.cl',
        'social_provider'   => 'google',
        'social_provider_id'=> '',
        'url_avatar'        => '',
        'password'          => null,
        'id_area'           => 1
    ];

    public static function ContractProvider($id_provider){
        return [
            'id_provider'     => $id_provider,
            'contract_number' => 666,
            'description'     => 'Este es un contrato de prueba',
            'contract_area'   => 1,
            'start_date'      => '2017-02-15',
            'end_date'        => '2018-02-15',
            'is_active'       => 1,
            'contract_pdf'    => 'contrato.pdf',

        ];
    }

    public static function rangeMonths($ini, $hasRange, $end){
        return [
            'month_ini'  => $ini,
            'month'  => $hasRange,
            'month_end'  => $end

        ];
    }

    public static function getAccountContract($id_area, $id_account, $id_contract){
        return [
            'id_contract' => $id_contract,
            'id_area' => $id_area,
            'account_code' => $id_account,
            'account_year' => date('Y')
        ];
    }

//    public static function route($param)
//    {
//        return static::$URL.$param;
//    }


}

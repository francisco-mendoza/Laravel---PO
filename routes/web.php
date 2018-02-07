<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/** Login */
Route::get('/login','Auth\LoginController@index');
//Google
Route::get('auth/google', 'Auth\LoginController@redirectToProvider');
Route::get('auth/google/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('auth/logout', 'Auth\LoginController@logout');

Route::get('/conversor_moneda', 'CurrenciesController@conversor_monedas');
Route::get('/validateAreaBudget', 'PurchaseOrderController@validateAreaBudget');

//Entrust::routeNeedsPermission('/areas/create','create-post');

/**
 * Todas las rutas que necesitan login
 */

Route::group(['middleware' => 'authenticate'], function () {
    //Home
    Route::get('/','FrontController@index')->name('home');
    Route::get('/getBudgetHome/{id?}', 'FrontController@getBudgetHome');
    Route::get('/getBudgetByMonth/{id?}', 'FrontController@getBudgetByMonth');

    //Usuarios y Roles
    Route::get('/roles/grid', 'RolesController@grid');
    Route::resource('/roles', 'RolesController');
    Route::get('/users/grid', 'UsersController@grid');
    Route::resource('/users', 'UsersController');

    Route::get('/permissions/grid', 'PermissionsController@grid');
    Route::resource('/permissions', 'PermissionsController');


	//Orden compra
    Route::get('/purchaseOrder/grid','PurchaseOrderController@grid');
    Route::get('/purchaseOrder/filter','PurchaseOrderController@filterPurchaseOrder');
    Route::get('/purchaseOrder/filterToBill','PurchaseOrderController@filterPurchaseOrderToBill');
    Route::get('/deletePurchaseOrder','PurchaseOrderController@deletePurchaseOrder');
    Route::get('/purchaseOrder/approvedGrid','PurchaseOrderController@approvedGrid');
    Route::get('/consultarOrdenes', [
        'uses'=>'PurchaseOrderController@consultOrders',
        'middleware' => 'permission:ver_consultarOrdenes'])->name('consultarOrdenes');
    Route::get('/aprobarOrdenes', [
        'uses'=>'PurchaseOrderController@approveOrders',
        'middleware' => 'permission:ver_aprobarOrdenes'])->name('aprobarOrdenes');
    Route::get('/crearOrdenes', [
        'uses'=>'PurchaseOrderController@createOrder',
        'middleware' => 'permission:ver_crearOrden'])->name('crearOrden');
    Route::get('/filtrarOrdenes/{patron?}', [
        'uses'=>'PurchaseOrderController@filterOrders',
        'middleware' => 'permission:ver_filtrarOrdenes'])->name('filtrarOrdenes');
    
    Route::get('/getProviders', 'PurchaseOrderController@getProviders');
    Route::get('/getMonths', 'PurchaseOrderController@getMonths');
    Route::get('/getProviderInfo/{name_provider}','PurchaseOrderController@getProviderByName');
    Route::get('/getDetailInfo/{id}','PurchaseOrderController@getOCDetailsFromAdvancedSearch');
    Route::get('/detailPurchaseOrder/{id}/{action?}','PurchaseOrderController@getOrderDetail')->name('getOrderDetail');
    Route::get('/approvePurchaseOrder/{id}','PurchaseOrderController@approvePurchaseOrder');
    Route::get('/rejectPurchaseOrder/{id}','PurchaseOrderController@rejectPurchaseOrder');
    Route::get('/editarOrden/{id}','PurchaseOrderController@editPurchaseOrder');
    Route::get('/getTotalActual/{id}','PurchaseOrderController@getTotalActual');
    Route::post('/updatePurchaseOrder','PurchaseOrderController@updatePurchaseOrder');
    Route::post('/validatePurchaseOrders','PurchaseOrderController@validatePurchaseOrders')->name('validatePurchaseOrders');
    Route::post('/savePurchaseOrder','PurchaseOrderController@savePurchaseOrder');
    
    
    //Monedas
    Route::get('/currencies/grid', 'CurrenciesController@grid');
    Route::resource('/currencies', 'CurrenciesController');

    
    //Areas
    Route::get('/areas/grid', 'AreasController@grid');
    Route::get('/areas/getUsers', 'AreasController@getUsers');
    Route::get('/areas/getBudgets/{id?}', 'AreasController@getBudgets');
    Route::get('/getBudget/{id_area}/{year}', 'AreasController@getBudgetAvailableByArea');
    Route::get('/getBudgetAccount/{id_area}/{year}/{code}', 'AreasController@getBudgetAvailableByAccount');
    Route::get('/getAccountsArea', 'AreasController@getAccountsArea');
    Route::get('/getInformationAccount', 'AreasController@getInformationAccount');
    Route::resource('/areas', 'AreasController');



    //Opciones Menu
    Route::get('/menuOptions/grid', 'MenuOptionsController@grid');
    Route::resource('/menuOptions', 'MenuOptionsController');
    Route::get('/fontawesome', 'MenuOptionsController@getFontawesome');

    
    //Metodos de Pago
    Route::get('/paymentmethods/grid', 'PaymentMethodController@grid');
    Route::resource('/paymentmethods', 'PaymentMethodController');

    //Condiciones de Pago
    Route::get('/paymentconditions/grid', 'PaymentConditionController@grid');
    Route::resource('/paymentconditions', 'PaymentConditionController');

    //Contratos
    Route::get('/contracts/grid', 'ContractsController@grid');
    Route::get('/getProvidersContracts', 'ContractsController@getProviders');
    Route::get('/contracts/getAccounts/{id?}', 'ContractsController@getAccounts');
    Route::get('/contracts/validateContract/', 'ContractsController@validateContract');
    Route::resource('/contracts', 'ContractsController');
    
    
    //Proveedores
    Route::get('/providers/grid', 'ProvidersController@grid');
    Route::get('/getSelectProviders', 'ProvidersController@getSelectProviders');
    Route::resource('/providers', 'ProvidersController');

    //Facturas
    Route::get('/convertInvoiceCurrency', 'InvoicesController@convertInvoiceCurrency');
    Route::get('/invoices/grid', 'InvoicesController@grid');
    Route::get('/invoices/pendingGrid', 'InvoicesController@pendingGrid');
    Route::get('/invoices/addOC/{id}', 'InvoicesController@addPurchaseOrder');
    Route::post('/invoices/assignOC/{id}', 'InvoicesController@assignPurchaseOrders');
    Route::get('/invoices/pending', [
        'uses'=>'InvoicesController@pending',
        'middleware' => 'permission:ver_facturasPendientes'])->name('facturasPendientes');
    Route::resource('/invoices', 'InvoicesController');
    
});




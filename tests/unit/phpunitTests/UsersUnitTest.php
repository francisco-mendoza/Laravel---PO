<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Controllers\UsersController;

use Illuminate\Http\Request;
use App\Models\User;

use Page\TestData;

class UsersUnitTest extends TestCase
{

    /** @var  UsersController */
    protected $controller;

    /** @var  Request */
    protected $request;

    protected $userFields;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        //Setear usuario
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        //Inicializar el controlador a probar
        $this->controller = new UsersController();

        //Parámetros mínimos del request
        $this->resetRequest();

        //Data de prueba
        $this->userFields = TestData::userFields;

    }

    public function resetRequest(){
        //Parámetros mínimos del request
        $this->request = new Request();
    }

    public function tearDown()
    {
        Session::clear();
    }


    public function testListUsersWithoutRolAssigned(){

        Session::start();
        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);
        $user->detachRoles();

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/users');
        $this->assertRedirectedToRoute("home");
        $this->assertArrayHasKey('error_message',Session::all(),"No tienes permiso para ver esa área");


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testListUsersWithoutRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testListUsersRejectedWithWrongRolAssigned(){

        Session::start();

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.general'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/users');
        $this->assertRedirectedToRoute("home");
        $this->assertArrayHasKey('error_message',Session::all(),"No tienes permiso para ver esa área");


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testListUsersRejectedWithWrongRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testListUsersWithRolAssigned(){


        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/users');

        $this->see('Listado de Usuarios');
        $this->see('Agregar Usuario');
        $this->see('Nombre de Usuario');
        $this->see('Correo');
        $this->see('Avatar');
        $this->see('Rol');
        $this->see('Área');


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testListUsersWithRolAssigned "."\033[32m OK \033[0m ".PHP_EOL );
    }

    public function testCreateUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->visit("/")->see("Sistema de Órdenes de compra");

        $this->call('GET', '/users/create');

        $this->see('Agregar/Editar Usuario');
        $this->see('Nombre de Usuario');
        $this->seeElement('input', ['name' => 'username']);
        $this->see('Nombre');
        $this->seeElement('input', ['name' => 'firstname']);
        $this->see('Apellido');
        $this->seeElement('input', ['name' => 'lastname']);
        $this->see('Correo');
        $this->seeElement('input', ['name' => 'email']);
        $this->see('Area Asignada');
        $this->seeElement('select', ['name' => 'id_area']);
        $this->see('Rol Asignado');
        $this->seeElement('select', ['name' => 'id_role']);

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testCreateUser "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function saveUserForSearch(){

        $newUser = new User($this->userFields);
        $newUser->save();

    }

    public function testFindUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveUserForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Perfiles');

        $this->request["search"] = ["value" => $this->userFields['username'] ];
        $json = $this->controller->grid($this->request);


        //Eliminar datos antes de los asserts
        $user = User::find($this->userFields['id_user']);
        $user->delete();

        $this->assertNotNull($json);

        $data = json_decode($json, true); //Decodificar el json


        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('recordsTotal', $data);
        $this->assertArrayHasKey('iTotalDisplayRecords', $data);
        $this->assertArrayHasKey('recordsFiltered', $data);
        $this->assertArrayHasKey('draw', $data);

        $this->assertGreaterThanOrEqual(1,$data['recordsTotal']);

        $this->assertContains($this->userFields['username'],$json);
        $this->assertContains($this->userFields['email'],$json);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testFindUser "."\033[32m OK \033[0m ". "\n");

    }

    public function seeUserInPage(){

        $this->see('Nombre de Usuario');
        $this->seeInField('username', $this->userFields['username']);
        $this->see('Nombre');
        $this->seeInField('firstname', $this->userFields['firstname']);
        $this->see('Apellido');
        $this->seeInField('lastname', $this->userFields['lastname']);
        $this->see('Correo');
        $this->seeInField('email', $this->userFields['email']);
        $this->see('Area Asignada');
        $this->see('Rol Asignado');
    }

    public function testShowUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveUserForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Perfiles');


        $this->call('GET', '/users/'. $this->userFields['id_user']);

        //Eliminar datos antes de los asserts
        $user = User::find($this->userFields['id_user']);
        $user->delete();

        $this->seeUserInPage();
        $this->seeInField('#id_area', "Area no asignada");
        $this->seeInField('id_role', "Rol no asignado");

        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testShowUser "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function testEditUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveUserForSearch();

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Perfiles');

        $this->see("Listado de Usuarios");

        $this->call('GET', '/users/'. $this->userFields['id_user'] .'/edit');

        //Eliminar datos antes de los asserts
        $user = User::find($this->userFields['id_user']);
        $user->delete();

        $this->see('Agregar/Editar Usuario');
        $this->seeUserInPage();


        fwrite(STDOUT, "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testEditUser "."\033[32m OK \033[0m ".PHP_EOL );

    }

    public function testValidateUserWithoutUsername(){

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutUsername "."\033[32m OK \033[0m ". "\n");

    }

    public function testValidateUserWithoutName(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutName "."\033[32m OK \033[0m ". "\n");
    }

    public function testValidateUserWithoutLastname(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->request["firstname"] = "AAAA";
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutLastname "."\033[32m OK \033[0m ". "\n");
    }

    public function testValidateUserWithoutEmail(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->request["firstname"] = "AAAA";
        $this->request["lastname"] = "AAAA";
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutEmail "."\033[32m OK \033[0m ". "\n");
    }

    public function testValidateUserWithoutArea(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->request["firstname"] = "AAAA";
        $this->request["lastname"] = "AAAA";
        $this->request["email"] = "ana@gmail.com";
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutArea "."\033[32m OK \033[0m ". "\n");
    }

    public function testValidateUserWithoutRol(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->request["firstname"] = "AAAA";
        $this->request["lastname"] = "AAAA";
        $this->request["email"] = "ana@gmail.com";
        $this->request["id_area"] = "1";
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->controller->validateFormUser($this->request);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testValidateUserWithoutRol "."\033[32m OK \033[0m ". "\n");
    }

    public function testPassValidateUser(){

        $this->request["username"] = "AAAA"; //Llenar el último dato necesario
        $this->request["firstname"] = "AAAA";
        $this->request["lastname"] = "AAAA";
        $this->request["email"] = "ana@gmail.com";
        $this->request["id_area"] = 1;
        $this->request["id_role"] = 1;
        $this->controller->validateFormUser($this->request);

        $this->addToAssertionCount(1);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testPassValidateUser "."\033[32m OK \033[0m ". "\n");
    }

    public function testDestroyUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $this->saveUserForSearch();

        $this->controller->destroy($this->request, $this->userFields['id_user']);

        $this->dontSeeInDatabase('users', $this->userFields);

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testDestroyUser "."\033[32m OK \033[0m ". "\n");

    }

    public function testStoreUser(){

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        //Setear campos en el request
        $this->request['id_area'] = 1;
        $this->request['id_role'] = 1;
        $this->request["username"] = $this->userFields['username'];
        $this->request["firstname"] = $this->userFields['firstname'];
        $this->request["lastname"] = $this->userFields['lastname'];
        $this->request["email"] = $this->userFields['email'];

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Perfiles');

        $this->see("Listado de Usuarios");

        $this->controller->store($this->request);

        $id_user = $this->userFields['id_user'];

        unset($this->userFields['id_user']);
        unset($this->userFields['password']);
        unset($this->userFields['remember_token']);
        unset($this->userFields['social_provider_id']);
        unset($this->userFields['social_provider']);
        unset($this->userFields['url_avatar']);

        $this->seeInDatabase('users', $this->userFields);
        $this->see('Listado de Usuarios');

        //Eliminar los datos

        $user = User::findBy('email',$this->userFields['email']);
        $user->delete();


        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testStoreUser "."\033[32m OK \033[0m ". "\n");

    }

    public function testUpdateUser(){

        $this->saveUserForSearch();
        $userInserted = User::findBy('email',$this->userFields['email']);

        $user = new User(array('username' => 'Ana', 'id_user' => 10));
        $this->be($user);

        $user->detachRoles();
        $user->attachRole(config('constants.finanzas'));

        $random = rand(0,9);
        $newShortName = "TS" . $random;

        //Setear campos en el request
        $this->request['id_user'] = $this->userFields['id_user'];
        $this->request["firstname"] = $newShortName;
        $this->request["lastname"] = $this->userFields['lastname'];
        $this->request["username"] = $this->userFields['username'];
        $this->request["email"] = $this->userFields['email'];
        $this->request["id_role"] = 1;
        $this->request["id_area"] = 1;

        $this->visit("/")->see("Sistema de Órdenes de compra");
        $this->click('Perfiles');

        $this->see("Listado de Usuarios");

        $this->controller->update($this->request);

        unset($this->userFields['id_user']);
        unset($this->userFields['password']);
        unset($this->userFields['remember_token']);
        unset($this->userFields['social_provider_id']);
        unset($this->userFields['social_provider']);
        unset($this->userFields['url_avatar']);

        $this->dontSeeInDatabase('users', $this->userFields);
        $this->userFields['firstname'] = $newShortName;
        $this->seeInDatabase('users', $this->userFields);
        $this->see('Listado de Usuarios');

        //Eliminar los datos
        $userInserted->delete();

        fwrite(STDOUT,  "\033[32m \e[1m ✓ \033[35m UsersUnitTest:\033[0m testUpdateUser "."\033[32m OK \033[0m ". "\n");

    }

}
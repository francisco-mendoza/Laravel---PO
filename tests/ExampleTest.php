<?php


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {

        $user = User::find(111);
        Auth::login($user, true);

        $this->visit('/login')
        ->see('Sistema de Órdenes de Compra');
        //$socialUser = Socialite::driver('google')->user();
       //   $this->logIn()
//            ->visit('/')
//            //->see('Entrar con Google');
//        ;


    }
}

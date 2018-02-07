<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) use ($factory) {
//    static $password;
//
//    return [
//        'name' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'password' => $password ?: $password = bcrypt('secret'),
//        'remember_token' => str_random(10),
//    ];

    $user = $factory->raw(App\Models\User::class);

    return array_merge($user, ['admin' => true]);


});

$factory->define(App\Models\PaymentCondition::class, function (Faker\Generator $faker) use ($factory) {

    $payment = $factory->raw(App\Models\PaymentCondition::class);

    return array_merge($payment, ['admin' => true]);


});

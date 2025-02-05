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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
$factory->define(App\Customer::class, function ($faker) {
    return [
        'name' => $faker->name,
    ];
});
$factory->define(App\Product::class, function ($faker) {
    return [
        'name' => $faker->word,
    ];
});
$factory->define(App\OrderProduct::class, function ($faker) {
    return [
        'customer_id' => rand(1, 10),
        'product_id' => rand(1, 20),
        'quantity' => rand(1, 5),   
    ];
});
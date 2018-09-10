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


$factory->define(App\RealAir::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 0,
        'rd_temp' => mt_rand(1, 30),
        'rd_wet' => mt_rand(1, 30),
    ];
});

$factory->define(App\RealLight::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 0,
        'rd_light_intensity' => mt_rand(1, 30),
    ];
});

$factory->define(App\RealCo2::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 0,
        'rd_co2_concentration' => mt_rand(1, 30),
    ];
});

$factory->define(App\RealLiquid::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 0,
        'rd_level' => mt_rand(1, 30),
    ];
});

$factory->define(App\RealSoil::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 0,
        'rd_temp' => mt_rand(1, 30),
        'rd_salt' => mt_rand(1, 30),
    ];
});

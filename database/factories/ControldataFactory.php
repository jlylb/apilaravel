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


$factory->define(App\Models\Juanlian::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738733,
        'device_status' => 1,
        'running_status' => 1,
    ];
});

$factory->define(App\Models\Guangai::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738734,
        'device_status' => 1,
        'running_status' => 1,
    ];
});

$factory->define(App\Models\Shifei::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738735,
        'device_status' => 1,
        'running_status' => 1,
    ];
});

$factory->define(App\Models\Tiaowen::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738736,
        'device_status' => 1,
        'running_status' => 1,
    ];
});

$factory->define(App\Models\Tongfei::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738737,
        'device_status' => 1,
        'running_status' => 1,
    ];
});

$factory->define(App\Models\Buguang::class, function (Faker\Generator $faker) {
    return [
        'rd_updatetime' => date('Y-m-d H:i:s'),
        'pdi_index' => 886738738,
        'device_status' => 1,
        'running_status' => 1,
    ];
});



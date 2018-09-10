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

//$factory->define(App\User::class, function (Faker\Generator $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->safeEmail,
//        'password' => '123456',
//        'remember_token' => str_random(10),
//    ];
//});


$factory->define(App\Air::class, function (Faker\Generator $faker) {
$arr = [886738701, 886738730];
    return [
        'rd_updatetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'hd_datetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'pdi_index' => $faker->randomElement($arr),
        'hd_temp' => mt_rand(1, 30),
        'hd_wet' => mt_rand(1, 30),
    ];
});

$factory->define(App\Light::class, function (Faker\Generator $faker) {
$arr = [865335183, 676214795];
    return [
        'rd_updatetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'hd_datetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'pdi_index' => $faker->randomElement($arr),
        'hd_light_intensity' => mt_rand(1, 30),
    ];
});

$factory->define(App\Co2::class, function (Faker\Generator $faker) {
$arr = [676214785];
    return [
        'rd_updatetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'hd_datetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'pdi_index' => $faker->randomElement($arr),
        'hd_co2_concentration' => mt_rand(1, 30),
    ];
});

$factory->define(App\Liquid::class, function (Faker\Generator $faker) {
$arr = [865335169];
    return [
        'rd_updatetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'hd_datetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'pdi_index' => $faker->randomElement($arr),
        'hd_level' => mt_rand(1, 30),
    ];
});

$factory->define(App\Soil::class, function (Faker\Generator $faker) {
$arr = [865335191];
    return [
        'rd_updatetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'hd_datetime' => $faker->dateTimeBetween('-5 days',  'now', 'Asia/Shanghai'),
        'pdi_index' => $faker->randomElement($arr),
        'hd_temp' => mt_rand(1, 30),
        'hd_salt' => mt_rand(1, 30),
    ];
});

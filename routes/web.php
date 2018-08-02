<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@welcome')->name('welcome');

Route::auth();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ro', 'HomeController@genPermission')->name('ro');


//Route::group([
//    'namespace'=>'admin',
//    'prefix'=>'admin',
//    'middleware'=>[
//        'auth',
//       'permission'
//    ]
//], function () {
//    Route::resource('users', 'UserController');
//    Route::get('user/roles/{user}', 'UserController@getRoles');
//    Route::put('user/roles', 'UserController@updateRoles');
//    Route::resource('roles', 'RoleController');
//    Route::get('roles/{role}/ability', 'RoleController@getRoleAbilities');
//    Route::put('roles/{role}/ability', 'RoleController@saveRoleAbility');
//    Route::resource('permission', 'PermissionController');
//});
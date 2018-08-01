<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::group([
    'namespace'=>'admin',
    'prefix'=>'admin',
    'middleware'=>[
        'auth',
       'permission'
    ]
], function () {
    Route::resource('users', 'UserController');
    Route::get('user/roles/{user}', 'UserController@getRoles');
    Route::put('user/roles', 'UserController@updateRoles');
    Route::resource('roles', 'RoleController');
    Route::get('roles/{role}/ability', 'RoleController@getRoleAbilities');
    Route::put('roles/{role}/ability', 'RoleController@saveRoleAbility');
    Route::resource('permission', 'PermissionController');
});

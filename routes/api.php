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

    'middleware' => 'api',
    'prefix' => 'auth'

], function () {

    Route::post('login', 'AuthController@login')->name('api.auth.login');
    Route::post('logout', 'AuthController@logout')->name('api.auth.logout');
    Route::post('refresh', 'AuthController@refresh')->name('api.auth.refresh');
    Route::get('userinfo', 'AuthController@getUserInfo')->name('api.auth.getUserInfo');
    Route::put('userinfo', 'AuthController@saveUserInfo')->name('api.auth.saveUserInfo');
    Route::put('password', 'AuthController@modifyPassword')->name('api.auth.modifyPassword');

});

Route::group([
    'namespace'=>'admin',
    'prefix'=>'',
    'middleware'=>[
       'auth:api',
        'permission'
        , 'scope'
    ]
], function () {
    Route::resource('users', 'UserController');
    Route::get('user/roles/{user}', 'UserController@getRoles');
    Route::put('user/roles', 'UserController@updateRoles');
    Route::resource('roles', 'RoleController');
    Route::get('roles/{role}/ability', 'RoleController@getRoleAbilities');
    Route::put('roles/{role}/ability', 'RoleController@saveRoleAbility');
    Route::resource('permission', 'PermissionController');
    Route::get('permission/{name}/search', 'PermissionController@search');
    Route::resource('menu', 'MenuController');
    Route::post('menu/{menu}/buttons', 'MenuController@createButton');
    Route::resource('company', 'CompanyController');
    Route::post('upload', 'UploadController@store');
});

//$api = app('Dingo\Api\Routing\Router');

//$api->version('v1', function ($api) {
//    $api->group([
//        'namespace'=>'App\Http\Controllers\Api\Auth',
//        'prefix'=>'',
//        'middleware'=>[
//        ]  
//    ], function ($api) {            
//        $api->post('login', 'LoginController@login');
//        $api->post('register', 'RegisterController@register');
//    });
//    $api->group([
//        'namespace'=>'App\Http\Controllers\admin',
//        'prefix'=>'admin',
//        'middleware'=>[
//            'auth',
//           'permission'
//        ]  
//    ], function ($api) {
//        $api->resource('users', 'UserController');
//    });
//});
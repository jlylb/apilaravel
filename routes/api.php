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
    'prefix' => ''

], function () {

    Route::post('auth/login', 'AuthController@login')->name('api.auth.login');
    Route::post('auth/logout', 'AuthController@logout')->name('api.auth.logout');
    Route::post('auth/refresh', 'AuthController@refresh')->name('api.auth.refresh');
    Route::get('auth/userinfo', 'AuthController@getUserInfo')->name('api.auth.getUserInfo');
    Route::put('auth/userinfo', 'AuthController@saveUserInfo')->name('api.auth.saveUserInfo');
    Route::put('auth/password', 'AuthController@modifyPassword')->name('api.auth.modifyPassword');

});

Route::group([
    'namespace'=>'admin',
    'prefix'=>'',
    'middleware'=>[
       'auth:api'
       , 'permission:api'
       , 'scope'
    ]
], function () {
    Route::resource('users', 'UserController');
    Route::get('user/roles/{user}', 'UserController@getRoles')->name('api.user.getRoles');
    Route::put('user/roles', 'UserController@updateRoles')->name('api.user.updateRoles');
    Route::resource('roles', 'RoleController');
    Route::get('roles/{role}/ability', 'RoleController@getRoleAbilities')->name('api.roles.ability');
    Route::put('roles/{role}/ability', 'RoleController@saveRoleAbility')->name('api.roles.ability');
    Route::resource('permission', 'PermissionController');
    Route::get('permission/{name}/search', 'PermissionController@search')->name('api.permission.search');
    Route::resource('menu', 'MenuController');
    Route::post('menu/{menu}/buttons', 'MenuController@createButton')->name('api.menu.createButton');
    Route::resource('company', 'CompanyController');
    Route::get('company/{company}/search', 'CompanyController@search')->name('api.company.search');
    Route::post('upload', 'UploadController@store')->name('api.upload.store');
    Route::resource('notification', 'NotificationController');
    Route::post('notification/{notification}/unread', 'NotificationController@unread')->name('api.notification.unread');
    Route::post('notification/unreadall', 'NotificationController@unreadAll')->name('api.notification.unreadAll');
    
    Route::resource('devicetype', 'DevicetypeController');
    Route::resource('deviceinfo', 'DeviceinfoController');
    
    Route::get('deviceinfo/devicetype/all', 'DeviceinfoController@getDeviceType');
    Route::get('deviceinfo/{company}/area', 'DeviceinfoController@getCompanyArea');
    
    Route::resource('area', 'AreaController');
    
    Route::resource('data', 'DataController@index');
    
    Route::get('monitor/index', 'MonitorController@index');
    Route::get('monitor/device', 'MonitorController@device');
    Route::post('monitor/data', 'MonitorController@deviceData');
    Route::post('monitor/realdata', 'MonitorController@deviceRealData');
    
    Route::get('userlog', 'UserLogController@index');
    Route::post('control/{pdi}/update', 'ControlController@update');
    Route::get('control/device', 'ControlController@device');
    Route::resource('warnclass', 'WarnclassController');
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
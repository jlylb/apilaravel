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
    Route::post('auth/forgetpassword', 'AuthController@forgetPassword')->name('api.auth.forgetpassword');
    Route::post('auth/sendcode', 'AuthController@sendCode')->name('api.auth.sendcode');

});

Route::group([
    'namespace'=>'admin',
    'prefix'=>'',
    'middleware'=>[
       'auth:api'
       , 'permission:api'
       , 'scope'
    ]
], function() {
    
    Route::resource('users', 'UserController');
    Route::get('user/roles/{user}', 'UserController@getRoles')->name('api.users.getRoles');
    Route::put('user/roles', 'UserController@updateRoles')->name('api.users.updateRoles');
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
//    Route::resource('notification', 'NotificationController');
//    Route::post('notification/{notification}/unread', 'NotificationController@unread')->name('api.notification.unread');
//    Route::post('notification/unreadall', 'NotificationController@unreadAll')->name('api.notification.unreadAll');
    
    Route::resource('devicetype', 'DevicetypeController');
    Route::resource('deviceinfo', 'DeviceinfoController');
    
    Route::get('deviceinfo/devicetype/all', 'DeviceinfoController@getDeviceType')->name('api.deviceinfo.devicetype');
    Route::get('deviceinfo/{company}/area', 'DeviceinfoController@getCompanyArea')->name('api.deviceinfo.area');
    
    Route::resource('area', 'AreaController');
    
    Route::resource('data', 'DataController@index');
    
    Route::get('monitor/index', 'MonitorController@index')->name('api.monitor.index');
    Route::post('monitor/data', 'MonitorController@deviceData')->name('api.monitor.data');
    Route::post('monitor/realdata', 'MonitorController@deviceRealData')->name('api.monitor.realdata');
    Route::post('monitor/areadevice', 'MonitorController@getDevicesByArea')->name('api.monitor.areadevice');

    Route::get('userlog', 'UserLogController@index')->name('api.userlog.index');
    Route::post('control/{pdi}/update', 'ControlController@update')->name('api.control.update');
    Route::get('control/device', 'ControlController@device')->name('api.control.device');
    Route::get('control/devicedata', 'ControlController@deviceData')->name('api.control.devicedata');
    
//    Route::resource('warnclass', 'WarnclassController');
    Route::resource('warndefine', 'WarndefineController');
    
    Route::get('realwarn', 'RealwarnController@index')->name('api.realwarn.index');
    Route::resource('warnnotify', 'WarnnotifyController');
    Route::resource('warnuser', 'WarnuserController');
    Route::post('warnuser/{warnuser}/warnsetting', 'WarnuserController@warnSetting')->name('api.warnuser.warnsetting');
    
    Route::get('report', 'ReportController@index')->name('api.report.index');
    Route::get('report/historysum', 'ReportController@historysum')->name('api.report.historysum');
    Route::get('report/assetsum', 'ReportController@assetsum')->name('api.report.assetsum');
    Route::get('video', 'VideoController@index')->name('api.video.index');
    
    Route::get('donghuang/index', 'DonghuangController@index')->name('api.donghuang.index');
    Route::post('donghuang/device', 'DonghuangController@device')->name('api.donghuang.device');
    Route::post('donghuang/realdata', 'DonghuangController@realData')->name('api.donghuang.realdata');
    Route::post('donghuang/storedevice', 'DonghuangController@storeDevice')->name('api.donghuang.storeDevice');
    
    Route::get('dashboard', 'IndexController@index')->name('api.index.index');
//    Route::post('donghuang/store', 'DonghuangController@store')->name('api.donghuang.store');
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
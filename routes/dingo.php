<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group([
        'namespace'=>'App\Http\Controllers',
        'prefix'=>'',
        'middleware'=>[
        ]  
    ], function ($api) {            
        $api->post('auth/login', 'AuthController@login')->name('api.auth.login');
        $api->post('auth/logout', 'AuthController@logout')->name('api.auth.logout');
        $api->post('auth/refresh', 'AuthController@refresh')->name('api.auth.refresh');
        $api->get('auth/userinfo', 'AuthController@getUserInfo')->name('api.auth.getUserInfo');
        $api->put('auth/userinfo', 'AuthController@saveUserInfo')->name('api.auth.saveUserInfo');
        $api->put('auth/password', 'AuthController@modifyPassword')->name('api.auth.modifyPassword');
    });
    $api->group([
        'namespace'=>'App\Http\Controllers\admin',
        'prefix'=>'',
        'middleware'=>[
            'auth:api'
            ,'permission:api'
            ,'scope'
        ]  
    ], function ($api) {
        $api->resource('users', 'UserController');
        $api->get('user/roles/{user}', 'UserController@getRoles')->name('api.user.getRoles');
        $api->put('user/roles', 'UserController@updateRoles')->name('api.user.updateRoles');
        $api->resource('roles', 'RoleController');
        $api->get('roles/{role}/ability', 'RoleController@getRoleAbilities')->name('api.roles.ability');
        $api->put('roles/{role}/ability', 'RoleController@saveRoleAbility')->name('api.roles.ability');
        $api->resource('permission', 'PermissionController');
        $api->get('permission/{name}/search', 'PermissionController@search')->name('api.permission.search');
        $api->resource('menu', 'MenuController');
        $api->post('menu/{menu}/buttons', 'MenuController@createButton')->name('api.menu.createButton');
        $api->resource('company', 'CompanyController');
        $api->get('company/{company}/search', 'CompanyController@search')->name('api.company.search');
        $api->post('upload', 'UploadController@store')->name('api.upload.store');
        $api->resource('notification', 'NotificationController');
        $api->post('notification/{notification}/unread', 'NotificationController@unread')->name('api.notification.unread');
        $api->post('notification/unreadall', 'NotificationController@unreadAll')->name('api.notification.unreadAll');

        $api->resource('devicetype', 'DevicetypeController');
        $api->resource('deviceinfo', 'DeviceinfoController');

        $api->get('deviceinfo/devicetype/all', 'DeviceinfoController@getDeviceType')->name('api.deviceinfo.devicetype');
        $api->get('deviceinfo/{company}/area', 'DeviceinfoController@getCompanyArea')->name('api.deviceinfo.area');

        $api->resource('area', 'AreaController');

        $api->resource('data', 'DataController@index');

        $api->get('monitor/index', 'MonitorController@index')->name('api.monitor.index');
        $api->post('monitor/data', 'MonitorController@deviceData')->name('api.monitor.data');
        $api->post('monitor/realdata', 'MonitorController@deviceRealData')->name('api.monitor.realdata');
        $api->post('monitor/areadevice', 'MonitorController@getDevicesByArea')->name('api.monitor.areadevice');

        $api->get('userlog', 'UserLogController@index')->name('api.userlog.index');
        $api->post('control/{pdi}/update', 'ControlController@update')->name('api.control.update');
        $api->get('control/device', 'ControlController@device')->name('api.control.device');
        $api->get('control/devicedata', 'ControlController@deviceData')->name('api.control.devicedata');

        $api->resource('warnclass', 'WarnclassController');
        $api->resource('warndefine', 'WarndefineController');

        $api->get('realwarn', 'RealwarnController@index')->name('api.realwarn.index');
        $api->resource('warnnotify', 'WarnnotifyController');
        $api->resource('warnuser', 'WarnuserController');
        $api->post('warnuser/{warnuser}/warnsetting', 'WarnuserController@warnSetting')->name('api.warnuser.warnsetting');

        $api->get('report', 'ReportController@index')->name('api.report.index');
        $api->get('report/historysum', 'ReportController@historysum')->name('api.report.historysum');
        $api->get('report/assetsum', 'ReportController@assetsum')->name('api.report.assetsum');
        $api->get('video', 'VideoController@index')->name('api.video.index');

        $api->get('donghuang/index', 'DonghuangController@index')->name('api.donghuang.index');
        $api->post('donghuang/device', 'DonghuangController@device')->name('api.donghuang.device');
        $api->post('donghuang/realdata', 'DonghuangController@realData')->name('api.donghuang.realdata');
        $api->post('donghuang/storedevice', 'DonghuangController@storeDevice')->name('api.donghuang.storeDevice');
        $api->post('donghuang/store', 'DonghuangController@store')->name('api.donghuang.store');
    });
});
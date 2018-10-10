<?php

return [
    'monitor' => [
        80 => 'temp', 
        81 => 'co2', 
        83 => 'light', 
        84 => 'soil', 
        82 => 'liquid', 
//        116 => 'video'
    ],
    'control' => [
        117 => 'juanlian', 
        118 => 'guangai', 
        119 => 'shifei', 
        120 => 'tiaowen', 
        121 => 'tongfeng', 
        122 => 'buguang'
    ],
    'models' => [
        117 => '\App\Models\Juanlian', 
        118 => '\App\Models\Guangai', 
        119 => '\App\Models\Shifei', 
        120 => '\App\Models\Tiaowen', 
        121 => '\App\Models\Tongfei', 
        122 => '\App\Models\Buguang',
    ],
    'surfix' => [ 
        80 => [  'envitemp' => '温度', 'envihumi' => '湿度', ], 
        81 => [ 'Concentration'=>'浓度', ], 
        83 => [ 'LightIntensity'=>'光照度', ], 
        84 => [ 'Soiltemp' => '温度', 'Soilhumi' => '湿度',], 
        82 => [ 'LevelValue' => '液位' ], 
    ],
    'itemField' => [
        'hwarn'=>'上限状态', 'lwarn'=>'下限状态',
    ],
    'consta' => [ 
        80 => [ 'envihumi' , '连接状态'  ], 
        81 => [ 'Concentration' , '连接状态' ], 
        83 => [ 'LightIntensity' , '连接状态' ], 
        84 => [ 'envihumi' , '连接状态' ], 
        82 => [ 'LevelValue' , '连接状态' ], 
    ],
    'desc' => [ 
        80 =>[ 'num' => 'rd_envihumi_num', 'name' => '环境温湿度' ], 
        81 =>[ 'num' => 'rd_Concentration_num', 'name' => '二氧化碳' ], 
        83 =>[ 'num' => 'rd_LightIntensity_num', 'name' => '光照度' ], 
        84 =>[ 'num' => 'rd_SoilTH_num', 'name' => '土壤温湿度' ], 
        82 =>[ 'num' => 'rd_LevelValue_num', 'name' => '液位' ], 
    ],
    'units' => [ 
        80 => [  'envitemp' => '°C', 'envihumi' => '%', ], 
        81 => [ 'Concentration'=>'°C', ], 
        83 => [ 'LightIntensity'=>'°C', ], 
        84 => [ 'Soiltemp' => '°C', 'Soilhumi' => '%',], 
        82 => [ 'LevelValue' => 'M' ], 
    ],
    'icons'=>[
        'envitemp' => 'wendu-item-circle', 
        'envihumi' => 'shidu-item-circle',
        'Concentration'=>'co2',
        'LightIntensity'=>'light',
        'Soiltemp' => 'wendu-item', 
        'Soilhumi' => 'shidu-item',
        'LevelValue' => 'liquid'
    ],
    'map_models' => [
        80 => [ 'real' => '\App\RealAir', 'history' => '\App\Air' ], 
        81 => [ 'real' => '\App\RealCo2', 'history' => '\App\Co2' ], 
        83 => [ 'real' => '\App\RealLight', 'history' => '\App\Light' ], 
        84 => [ 'real' => '\App\RealSoil', 'history' => '\App\Soil' ], 
        82 => [ 'real' => '\App\RealLiquid', 'history' => '\App\Liquid' ], 
    ],
    'notify_type' => [
        'sms'=>1,
        'email'=>2,
        'audio'=>4,
    ],
    'fieldLen'=>8
];


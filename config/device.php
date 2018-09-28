<?php

return [
    'monitor' => [
        80 => 'temp', 
        81 => 'co2', 
        83 => 'light', 
        84 => 'soil', 
        82 => 'liquid', 
        116 => 'video'
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
    'surfix' => [ //'hwarn' => '上限告警', 'lwarn' => '下限告警', 'consta' => '连接状态',
        80 => [ 'envihumi' => '湿度', 'envitemp' => '温度',  'num'=> 'rd_envihumi_num' ], 
        81 => [ 'Concentration'=>'浓度', 'num'=> 'rd_Concentration_num'], 
        83 => [ 'LightIntensity'=>'光照度', 'num'=> 'rd_LightIntensity_num', ], 
        84 => [ 'Soiltemp' => '温度', 'Soilhumi' => '湿度', 'num'=> 'rd_SoilTH_num' ], 
        82 => [ 'LevelValue' => '液位', 'num'=> 'rd_LevelValue_num' ], 
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
];


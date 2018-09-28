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
    'num' => [ 
        80 => 'rd_envihumi_num', 
        81 => 'rd_Concentration_num', 
        83 => 'rd_LightIntensity_num', 
        84 => 'rd_SoilTH_num', 
        82 => 'rd_LevelValue_num', 
    ],
    'units' => [ 
        80 => [  'envitemp' => '°C', 'envihumi' => '%', ], 
        81 => [ 'Concentration'=>'°C', ], 
        83 => [ 'LightIntensity'=>'°C', ], 
        84 => [ 'Soiltemp' => '°C', 'Soilhumi' => '%',], 
        82 => [ 'LevelValue' => 'M' ], 
    ],
];


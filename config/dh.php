<?php

return [
    'sys'=>[
        'power' => [ 
            10 => ['label' =>'ups', 'value'=>'10', 'icon'=>'ups', 'router'=>'ups'],
            7 => ['label' =>'精密配电', 'value'=>'7', 'icon'=>'jmpd', 'router'=>'peidian'],
         ],
         'env' => [ 
           13 =>  ['label' =>'精密空调', 'value'=>'13', 'icon'=>'jmkt', 'router'=>'air'],
           33 =>  ['label' =>'温湿度', 'value'=>'33', 'icon'=>'wsdu', 'router'=>'temphu'],
         ],
         'fire' => [ 
             97 =>   ['label' =>'烟感', 'value'=>'97', 'icon'=>'yangan', 'router'=>'yangan'],
         ],
         'protect' => [ 
              98 =>  ['label' =>'红外', 'value'=>'98', 'icon'=>'red-gan', 'router'=>'red'],
         ], 
    ],    
    'names'=>[
        'power'=>'动力系统',
        'env'=>'环境系统',
        'fire'=>'消防系统',
        'protect'=>'安保系统'
    ],
    
    'realtable'=>[
        10 => 't_realdata_three',
        7 => 't_realdata_npower ',
        13 => 't_realdata_generalair',
        33 => 't_realdata_TempHum',
        97 => 't_realdata_yangan',
        98 => 't_realdata_red'
    ],
    
    'desc'=>[
        10 =>[ 'icon' => 'ups', 'realtable' => 't_realdata_three', 'router' => 'ups', 'label' =>'ups', 'value'=>'10',],
        7 => [ 'icon' => 'jmpd', 'realtable' => 't_realdata_npower', 'router' => 'peidian','label' =>'精密配电', 'value'=>'7'],
        13 =>[ 'icon' => 'jmkt', 'realtable' => 't_realdata_generalair', 'router' => 'air','label' =>'精密空调', 'value'=>'13',],
        33 =>[ 'icon' =>  'wsdu', 'realtable' => 't_realdata_TempHum', 'router' => 'temphu','label' =>'温湿度', 'value'=>'33',],
        97 =>[ 'icon' =>  'yangan', 'realtable' => 't_realdata_yangan', 'router' => 'yangan','label' =>'烟感', 'value'=>'97',],
        98 =>[ 'icon' =>  'red-gan', 'realtable' => 't_realdata_red', 'router' => 'red','label' =>'红外', 'value'=>'98',]
    ],
    
];


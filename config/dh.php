<?php

return [
    'sys'=>[
        'power' => [
            1 => ['label' =>'单相ups', 'value'=>'1', 'icon'=>'ups', 'router'=>'sups', 'child'=> 0, 'rvalue'=>'1',],
            10 => ['label' =>'三相ups', 'value'=>'10', 'icon'=>'ups', 'router'=>'ups', 'child'=> 0, 'rvalue'=>'10',],
            5 => ['label' =>'精密配电', 'value'=>'5', 'icon'=>'jmpd', 'router'=>'peidian', 'child'=> 0, 'rvalue'=>'5'],
         ],
         'env' => [ 
           13 =>  ['label' =>'精密空调', 'value'=>'13', 'icon'=>'jmkt', 'router'=>'air', 'child'=> 0, 'rvalue'=>'13'],
           33 =>  ['label' =>'温湿度', 'value'=>'33', 'icon'=>'wsdu', 'router'=>'temphu', 'child'=> 0, 'rvalue'=>'33'],
         ],
         'fire' => [ 
             341 =>   ['label' =>'烟感', 'value'=>'341', 'icon'=>'yangan', 'router'=>'yangan', 'child'=> 1, 'rvalue'=>'34'],
         ],
         'protect' => [ 
              343 =>  ['label' =>'红外', 'value'=>'343', 'icon'=>'red-gan', 'router'=>'red', 'child'=> 3, 'rvalue'=>'34'],
         ], 
    ],    
    'names'=>[
        'power'=>'动力系统',
        'env'=>'环境系统',
        'fire'=>'消防系统',
        'protect'=>'安保系统'
    ],
    
    'realtable'=>[
        1 => 't_realdata_single',
        10 => 't_realdata_three',
        5 => 't_realdata_power ',
        13 => 't_realdata_generalair',
        33 => 't_realdata_TempHumi',
        341 => 't_realdata_Switch',
        343 => 't_realdata_Switch'
    ],
    
    'desc'=>[
        1 =>[ 'icon' => 'ups', 'realtable' => 't_realdata_single', 'router' => 'sups', 'label' =>'单相ups', 'value'=>'1','child'=> 0, 'rvalue'=>'1'],
        10 =>[ 'icon' => 'ups', 'realtable' => 't_realdata_three', 'router' => 'ups', 'label' =>'三相ups', 'value'=>'10', 'child'=> 0, 'rvalue'=>'10'],
        5 => [ 'icon' => 'jmpd', 'realtable' => 't_realdata_power', 'router' => 'peidian','label' =>'精密配电', 'value'=>'5', 'child'=> 0, 'rvalue'=>'5'],
        13 =>[ 'icon' => 'jmkt', 'realtable' => 't_realdata_generalair', 'router' => 'air','label' =>'精密空调', 'value'=>'13', 'child'=> 0, 'rvalue'=>'13'],
        33 =>[ 'icon' =>  'wsdu', 'realtable' => 't_realdata_TempHumi', 'router' => 'temphu','label' =>'温湿度', 'value'=>'33', 'child'=> 0, 'rvalue'=>'33'],
        341 =>[ 'icon' =>  'yangan', 'realtable' => 't_realdata_Switch', 'router' => 'yangan','label' =>'烟感', 'value'=>'341', 'child'=> 1, 'rvalue'=>'34'],
        343 =>[ 'icon' =>  'red-gan', 'realtable' => 't_realdata_Switch', 'router' => 'red','label' =>'红外', 'value'=>'343', 'child'=> 3, 'rvalue'=>'34']
    ],
    'subMap' => [
       341 => 34, 
       343 => 34,
    ],
    
];


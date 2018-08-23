<?php

return [
    'field' => 'logo',
    'logo' => [
        'mimes' => 'jpeg,bmp,png',
        'folder'=>'upload',
        'size' => 2*1024*1024,
        'rules' =>'required|mimes:jpeg,bmp,png',
        'messages' => [
            'file.required'=>'请选择要上传的文件'
        ]
    ],
    'Co_Logo' => [
        'mimes' => 'jpeg,bmp,png',
        'folder'=>'upload',
        'size' => 2*1024*1024,
        'rules' =>'required|mimes:jpeg,bmp,png',
        'messages' => [
            'Co_Logo.required'=>'请选择要上传的文件'
        ]
    ]
];

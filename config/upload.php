<?php

return [
    'field' => 'logo',
    'logo' => [
        'mimes' => 'jpeg,bmp,png',
        'folder'=>'upload',
        'size' => 2*1024*1024,
        'rules' =>'required|mimes:jpeg,bmp,png, image/png',
        'messages' => [
            'logo.required'=>'请选择要上传的文件'
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
    ],
    'avatar' => [
        'mimes' => 'jpeg,bmp,png',
        'folder'=>'avatar',
        'size' => 2*1024*1024,
        'rules' =>'required|mimes:jpeg,bmp,png,image/png',
        'messages' => [
            'avatar.required'=>'请选择要上传的文件'
        ]
    ],
];

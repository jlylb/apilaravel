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
    'first_img' => [
        'mimes' => 'jpeg,bmp,png',
        'folder'=>'upload',
        'size' => 2*1024*1024,
        'rules' =>'required|mimes:jpeg,bmp,png',
        'messages' => [
            'first_img.required'=>'请选择要上传的文件'
        ]
        ],
        'path' => [
            'mimes' => 'jpeg,bmp,png',
            'folder'=>'upload',
            'size' => 2*1024*1024,
            'rules' =>'required|mimes:jpeg,bmp,png',
            'messages' => [
                'path.required'=>'请选择要上传的文件'
            ]
        ]
];

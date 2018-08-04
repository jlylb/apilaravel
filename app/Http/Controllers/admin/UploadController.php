<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ['status'=>1,'msg'=>'','data'=>[]];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $field = $request->input('field',config('upload.field'));
        $all = $request->all();
        //var_dump($all,$request->all());
        $config = config('upload.'.$field);
        $rules = [
           $field=>$config['rules'],
        ];
        $messages = $config['messages'];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            return new JsonResponse($this->formatValidationErrors($validator));
        }

        $file=$request->file($field);

        $size = $file->getSize();

        if($size > $config['size']){
            return new JsonResponse(['msg'=>'上传文件不能超过'.$config['size']]);
        }

        //文件类型
        //$mimeType = $file->getMimeType();

        // if($mimeType != 'image/png'){
        //     return new JsonResponse(['msg'=>'只能上传png格式的图片']);
        // }
        //扩展文件名
        $ext = $file->getClientOriginalExtension();
        
        $exts = array_get($config, 'exts', []);
        
        if($exts && !in_array($ext,$exts)){
            return new JsonResponse(['msg'=>'只允许上传此后缀的文件'. implode(',', $exts)]);
        }
        

        if(!$file->isValid()){
            return new JsonResponse(['msg'=>'非法操作']);
        }

        //创建以当前日期命名的文件夹
        $today = date('Ymd');

        //上传文件
        $filename = uniqid().'.'.$ext;
        $path =  'attach/'.$today.'/'.$filename;
//        $path = $file->storeAs(
//            'attach/'.$today, $filename,array_get($config, 'folder', 'local')
//        );
        Storage::disk('upload')->put(
           $path,
            file_get_contents($file->getRealPath())
        );
        // $path = $file -> move(app_path().'/storage/uploads',$filename);
        return ['status'=>1,'msg'=>''
        ,'data'=>[
            'location'=>$path
            ,'field'=>$field
            ,'name'=>$filename
            ]
        ];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

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
        $all = $request->all();
        $rules = [
            'file'=>'required|file',
        ];
        $messages = [
            'file.required'=>'请选择要上传的文件'
        ];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            return new JsonResponse($this->formatValidationErrors($validator));
        }

        $file=$request->file('file');
        
        $size = $file->getSize();

        if($size > 2*1024*1024){
            return new JsonResponse(['msg'=>'上传文件不能超过2M']);
        }

        //文件类型
        $mimeType = $file->getMimeType();

        // if($mimeType != 'image/png'){
        //     return new JsonResponse(['msg'=>'只能上传png格式的图片']);
        // }
        //扩展文件名
        $ext = $file->getClientOriginalExtension();


        if(!$file->isValid()){
            return new JsonResponse(['msg'=>'非法操作']);
        }

        //创建以当前日期命名的文件夹
        $today = date('Ymd');

        //上传文件
        $filename = uniqid().'.'.$ext;
        $path = $file->storeAs(
            'attach/'.$today, $filename,'upload'
        );
        return ['status'=>1,'msg'=>''
        ,'data'=>[
            'location'=>$path
            ,'field'=>$request->input('field','avatar')
            ,'name'=>$filename
            ,'url'=>'http://localhost:8000/storage/uploads/'.$path]];

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

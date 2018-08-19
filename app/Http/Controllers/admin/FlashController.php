<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FlashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize=$request->input('per_page',15);
        $page=$request->input('page',1);
        $id=$request->input('id',0);
        $users=  \App\Flash::orderBy('created_at','desc');
        if($id){
            $users->where('carousel_id','=',$id);
        }
        $lists = $users->offset($page)->paginate($pageSize);
        return response()->json($lists);
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
            'name'=>'required',
            'path'=>'required',
        ];
        $messages = [
            'name.required'=>'请填写图片名称',
            'path.required'=>'请上传文件',
        ];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            $messages = $validator->errors();
            foreach($rule as $key=>$v){
                return ['status'=>0,'msg'=>$messages->first($key),'data'=>[]];
            }
        }
        $ret = \App\Flash::create($all);
        if ($ret) {
			return response()->json(['status'=>1,'msg'=>'保存成功！']);
		} else {
			return response()->json(['status'=>0,'msg'=>'保存失败！']);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $flash = \App\Flash::find($id);

        return ['status'=>1,'msg'=>'','data'=>[
            'path'=>$flash->path
        ]];

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
        $all = $request->all();
        $rules = [
            'name'=>'required',
            'path'=>'required',
        ];
        $messages = [
            'name.required'=>'请填写图片名称',
            'path.required'=>'请上传文件',
        ];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            $messages = $validator->errors();
            foreach($rule as $key=>$v){
                return ['status'=>0,'msg'=>$messages->first($key),'data'=>[]];
            }
        }
        $flash = \App\Flash::find($id);
        $flash->fill($all);
        if ($flash->save()) {
			return response()->json(['status'=>1,'msg'=>'保存成功！']);
		} else {
			return response()->json(['status'=>0,'msg'=>'保存失败！']);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = \App\Flash::find($id);
        if ($user->delete()) {
			return ['status'=>1,'msg'=>'删除成功！','data'=>[]];
		} else {
            return ['status'=>0,'msg'=>'删除失败！','data'=>[]];
		}
    }
}

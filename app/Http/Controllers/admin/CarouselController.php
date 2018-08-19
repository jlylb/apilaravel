<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CarouselController extends Controller
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
        $users=  \App\Carousel::orderBy('created_at','desc')
                    ->offset($page)
                    ->paginate($pageSize);

        return response()->json($users);
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
        ];
        $messages = [
            'name.required'=>'请填写幻灯片名称',
        ];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            $messages = $validator->errors();
            foreach($rules as $key=>$v){
                return ['status'=>0,'msg'=>$messages->first($key),'data'=>[]];
            }
        }
        $ret = \App\Carousel::create($all);
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
        $flash = \App\Carousel::find($id);

        return ['status'=>1,'msg'=>'','data'=>[
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
        ];
        $messages = [
            'name.required'=>'请填写幻灯片名称',
        ];

        $validator = Validator::make($all,$rules,$messages);

        if($validator->fails()){
            $messages = $validator->errors();
            foreach($rules as $key=>$v){
                return ['status'=>0,'msg'=>$messages->first($key),'data'=>[]];
            }
        }
        $flash = \App\Carousel::find($id);
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
        $user = \App\Carousel::find($id);
        if ($user->delete()) {
			return ['status'=>1,'msg'=>'删除成功！','data'=>[]];
		} else {
            return ['status'=>0,'msg'=>'删除失败！','data'=>[]];
		}
    }

    public function getFlashes()
    {
        $fields=['height','value','loop',
        'autoplay','autoplay_speed',
        'dots','radius_dot','trigger',
        'arrow','easing','id'
        ];
        $carousel = \App\Carousel::where(['status'=>1])->orderBy('updated_at','desc')
            ->select($fields)
            ->first();
        $flashes=[];
        $attrs=[];
        if($carousel){
            $flashes = $carousel->flashes;
            $boolean = ['loop','autoplay','radius_dot'];
            foreach ($fields as $v) {
                $attrs[str_replace('_','-',$v)]=in_array($v,$boolean)?($carousel->{$v}==1?true:false):$carousel->{$v};
            }
    
            $attrs['height'] = is_numeric($attrs['height'])?$attrs['height']+0:$attrs['height'];
        }


        
        return ['status'=>1,'msg'=>'','data'=>$flashes,'attrs'=>$attrs];

    }
}

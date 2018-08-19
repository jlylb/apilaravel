<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class CommentController extends Controller
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
        $where=[];
        $uid=$request->input('user_id','');
        if($uid){
            $where[]=['user_id','like',$uid.'%'];
        }
        $users=  \App\Comment::orderBy('created_at','desc')
                    ->where($where)
                    ->offset($page)
                    ->paginate($pageSize);
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'content'=>'required',
            'commentable_id'=>'required',
        ];
        $messages = [
            'name.required'=>'评论内容不能为空',
            'name.commentable_id'=>'评论对象不能为空',
        ];

        $this->validate($request,$rules,$messages);

        $user=JWTAuth::parseToken()->authenticate();

        $all['user_id']=$user->id;

        $post= \App\Post::find($all['commentable_id']);
        $all['commentable_type']=trim(get_class($post),'\\'); 

        $ret = \App\Comment::create($all);
        if ($ret) {
			return ['status'=>1,'msg'=>'保存成功！','data'=>$ret];
		} else {
			return ['status'=>0,'msg'=>'保存失败！'];
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
        $data=$request->all();

        $post=\App\Comment::find($id);
          
        if ($post->update($data)) {
			return ['status'=>1,'msg'=>'保存成功！'];
		} else {
			return ['status'=>0,'msg'=>'保存失败！'];
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
        $ids=explode(',',$id);
        $ret = \App\Comment::destroy($ids);
        if ($ret) {
            return ['status'=>1,'msg'=>'删除成功！'];
        } else {
            return ['status'=>0,'msg'=>'删除失败！'];
        }
    }
}

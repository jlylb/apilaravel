<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
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
        $title=$request->input('title','');
        if($title){
            $where[]=['title','like',$title.'%'];
        }
        $posts = \App\Post::orderBy('a.created_at','desc')
                    ->from('posts as a')
                    ->leftJoin('categories as b', 'a.category_id', '=', 'b.id')
                    ->select(['a.*','b.name as category_name','b.path'])
                    ->where($where)
                    ->offset($page)
                    ->paginate($pageSize);
        return $posts;

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cats=new \App\Category();
        $lists=$cats->getTreeCategory();
        return ['status'=>1,'data'=>[
                'category_id'=>$lists
            ]
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data=$request->all();
        
        $this->validate($request,[
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required',
        ]);
        $data['category_id']=end($data['category_id']);

        $ret = \App\Post::create($data);
        if ($ret) {
			return ['status'=>1,'msg'=>'保存成功！'];
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
    public function show(Request $request, $id)
    {
        $pageSize=$request->input('per_page',15);
        $page=$request->input('page',1);
        $cats=new \App\Category();
        $lists=$cats->getTreeCategory();
        $where=[];
        $post = \App\Post::find($id);
        return ['status'=>1,'data'=>[
                'category_id'=>$lists,
                'post' => $post,
            ]
        ];
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
       
        $this->validate($request,[
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required',
        ]);

        $data['category_id']=end($data['category_id']);
        $post=\App\Post::find($id);
  
        
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
        $ret = \App\Post::destroy($ids);
        if ($ret) {
			return ['status'=>1,'msg'=>'删除成功！'];
		} else {
			return ['status'=>0,'msg'=>'删除失败！'];
		}
    }

    public function comments(Request $request, $id)
    {
        $pageSize=$request->input('per_page',15);
        $page=$request->input('page',1);
        $where=[];
        $post = \App\Post::find($id);
        if(!$post){
            return ['status'=>0,'data'=>[],'msg'=>'非法数据'];
        }
 
        $comments = \App\Comment::where([
            ['commentable_id','=',$id],
            ['commentable_type','=','App\Post'],
        ])
        ->select(['comments.*','users.name as user_name','avatar'])
        ->leftJoin('users', 'users.id', '=', 'comments.user_id')
        ->offset($page)
        ->paginate($pageSize);
        return ['status'=>1,'data'=>$comments];
    }
}

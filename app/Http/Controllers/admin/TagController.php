<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
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
        $name=$request->input('name','');
        if($name){
            $where[]=['name','like',$name.'%'];
        }
        $tags = \App\Tag::orderBy('created_at','desc')
                    ->where($where)
                    ->offset($page)
                    ->paginate($pageSize);
        return $tags;
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

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|unique:tags|max:255',
        ]);
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
        $this->validate($request, [
            'name' => 'required|unique:tags|max:255',
        ]);

        $tag = \App\Tag::create($data);
        if ($tag) {
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

        $this->validate($request, [
            'name' => 'required|unique:tags,name,'.$id.'|max:255',
        ]);
        
        $tag=\App\Tag::find($id);
        
        if ($tag->update($data)) {
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

        $ret = \App\Tag::destroy($ids);
        if ($ret) {
            DB::table('taggables')->whereIn('tag_id',$ids)->delete();
			return ['status'=>1,'msg'=>'删除成功！'];
		} else {
			return ['status'=>0,'msg'=>'删除失败！'];
		}
    }
}

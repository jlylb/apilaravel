<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
        $nameEn=$request->input('name_en','');
        if($name){
            $where[]=['name_en','like',$nameEn.'%'];
        }
        $parentId=$request->input('parent_id','');
        if($parentId){
            $where[]=['parent_id','=',$parentId];
        }
        $cats = \App\Category::orderBy('created_at','desc')
                    ->where($where)
                    ->offset($page)
                    ->paginate($pageSize);
        return $cats;
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
        return json_encode(['status'=>1,'data'=>[
                'parent_id'=>$lists
            ]
        ]);
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'name_en' => 'required',
            'parent_id' => 'required',
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

        $parentId=$data['parent_id'];
        $data['parent_id']=end($parentId);
 
        $this->validate($request,[
            'name' => 'required|unique:categories|max:255',
            'name_en' => 'required|unique:categories|max:255',
            'parent_id' => 'required',
        ]);

        $ret = \App\Category::create($data);
        if ($ret) {
            $path=array_merge($parentId,[$ret->id]);
            $ret->path=implode('-',$path);
            $ret->save();
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
        $cats=new \App\Category();
        $lists=$cats->getTreeCategory([$id]);
        return ['status'=>1,'data'=>[
                'parent_id'=>$lists
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
        $post=\App\Category::find($id);
        $sourcePid=$post->parent_id;
        $spid=$data['parent_id'];
        $parentId=end($data['parent_id']);
        $data['parent_id']=$parentId;
        $data['path']=implode('-',array_merge($spid,[$id]));
        $spath=$post->path;

        $this->validate($request,[
            'name' => 'required|unique:categories,name,'.$id.',|max:255',
            'name_en' => 'required|unique:categories,name,'.$id.',|max:255',
            'parent_id' => 'required',
        ]);

        if ($post->update($data)) {
            if($sourcePid!=$parentId){
                $post->updateChildren($spath,$data['path']);
            }
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
        $ret = \App\Category::destroy($ids);
        if ($ret) {
			return ['status'=>1,'msg'=>'删除成功！'];
		} else {
			return ['status'=>0,'msg'=>'删除失败！'];
		}
    }
}

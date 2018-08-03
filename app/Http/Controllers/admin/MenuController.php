<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('route_name', '');
        $query = Menu::query();
        if(!empty($name)) {
            $query->where('route_name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
       // echo $query->toSql();
        $menu = $query->paginate($perPage);
        return ['status' => 1, 'data'=>$menu];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cats=new \App\Menu();
        $lists=$cats->getTreeCategory();
        return json_encode(['status'=>1,'data'=>[
                'pid'=>$lists
            ]
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
        $data = array_except(json_decode($request->getContent(), true), 'action');
        $parentId=$data['pid'];
        $data['pid']=end($parentId);
        $menu = Menu::create($data);
        if($menu){
            $path=array_merge($parentId,[$menu->id]);
            $menu->path=implode('-',$path);
            $menu->save();
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
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
        $cats=new \App\Menu();
        $lists=$cats->getTreeCategory([$id]);
        return ['status'=>1,'data'=>[
                'pid'=>$lists
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
        $data = array_except(json_decode($request->getContent(), true), 'action');
        $menu = Menu::findOrFail($id);
 
        $sourcePid=$menu->pid;
        $spid=$data['pid'];
        $parentId=end($data['pid']);
        $data['pid']=$parentId;
        $data['path']=implode('-',array_merge($spid,[$id]));
        $spath=$menu->path;

        // $this->validate($request,[
        //     'name' => 'required|unique:categories,name,'.$id.',|max:255',
        //     'name_en' => 'required|unique:categories,name,'.$id.',|max:255',
        //     'parent_id' => 'required',
        // ]);


        if($menu->update($data)){
            if($sourcePid!=$parentId){
                $menu->updateChildren($spath,$data['path']);
            }
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
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
        $menu = Menu::findOrFail($id);
        if($menu->delete()){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
        }
    }
    
    public function createButton(Request $request,$id)
    {
        $menu = Menu::findOrFail($id);
        $buttons = json_decode($request->getContent(), true);
        $menu->buttons = $buttons['button'];
        if($menu->save()){
            return ['status' => 1, 'msg'=>'successful'];
        }else{
            return ['status' => 0, 'msg'=>'fail'];
        }
    }
}

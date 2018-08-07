<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menu;

class MenuController extends Controller
{
    protected $message = [
        'name.required' => '菜单名称必须',
        'name.unique' => '菜单名称已经存在',
        'name.max' => '菜单名称不能超过255个字符',
        'route_name.required' => '路由名称必须',
        'route_name.max' => '路由名称不能超过255个字符',
        'route_path.required' => '路由路径必须',
        'component.required' => '组件必须',
    ];
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
        $this->validate($request,[
             'name' => 'required|unique:menus|max:255',
             'route_name' => 'required|max:255',
             'route_path' => 'required',
             'component' => 'required',
        ],$this->message);
        $data = array_except(json_decode($request->getContent(), true), 'action');
        $parentId=$data['pid'];
        $data['pid']=end($parentId);
        $menu = Menu::create($data);
        if($menu){
            $path=array_merge($parentId,[$menu->id]);
            $menu->path=implode('-',$path);
            $menu->save();
            return ['status' => 1, 'msg'=>'添加成功'];
        }else{
            return ['status' => 0, 'msg'=>'添加失败'];
        }
    }
    
    public function messages($param) {
        
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

         $this->validate($request,[
             'name' => 'required|unique:menus,name,'.$id.',|max:255',
             'route_name' => 'required|max:255',
             'route_path' => 'required',
             'component' => 'required',
         ],$this->message);


        if($menu->update($data)){
            if($sourcePid!=$parentId){
                $menu->updateChildren($spath,$data['path']);
            }
            return ['status' => 1, 'msg'=>'保存成功'];
        }else{
            return ['status' => 0, 'msg'=>'保存失败'];
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
            return ['status' => 1, 'msg'=>'删除成功'];
        }else{
            return ['status' => 0, 'msg'=>'删除失败'];
        }
    }
    
    public function createButton(Request $request,$id)
    {
        $menu = Menu::findOrFail($id);
        $buttons = json_decode($request->getContent(), true);
        $menu->buttons = $buttons['button'];
        if($menu->save()){
            return ['status' => 1, 'msg'=>'更新成功'];
        }else{
            return ['status' => 0, 'msg'=>'更新失败'];
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Silber\Bouncer\Database\Models;
use Bouncer;
use App\Http\traits\UserPrivilege;

class RoleController extends Controller
{
    use UserPrivilege;
    
    protected $message = [
        'name.required' => '角色名称必须',
        'name.alpha_dash' => '角色名称只能含字母和数字，以及破折号和下划线',
        'name.unique' => '角色名称已经存在',
        'name.max' => '角色名称不能超过155个字符',
        'title.required' => '角色描述必须',
        'title.max' => '角色描述不能超过255个字符',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('name', '');
        $query = Bouncer::Role()->query();
        if(!empty($name)) {
            $query->where('name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
        $user = $this->user();
        if(!$this->isSuper($user)){
            $query->where('scope', $user->Co_ID);
        }
        $roles = $query->paginate($perPage);
        return ['status' => 1, 'data'=>$roles];
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
        $data = $request->input();
        $this->validate($request, [
            'name' => 'required|alpha_dash|unique:roles|max:155',
            'title' => 'required|max:255',
        ], $this->message);
        $ability = Bouncer::role()->create($data);
        if($ability){
            return ['status' => 1, 'msg'=>'添加成功'];
        }else{
            return ['status' => 0, 'msg'=>'添加失败'];
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
        $data = $request->input();
        $this->validate($request, [
            'name' => 'required|alpha_dash|unique:roles,name,'.$id.'|max:155',
            'title' => 'required|max:255',
        ], $this->message);
        $ability = Bouncer::role()->findOrFail($id);
        if($ability->update($data)){
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
        $ability = Bouncer::role()->findOrFail($id);
        if($ability->delete()){
            return ['status' => 1, 'msg'=>'删除成功'];
        }else{
            return ['status' => 0, 'msg'=>'删除失败'];
        }
    }

    public function getRoleAbilities($role)
    {
        $allAbilities = Bouncer::ability()->select(['name','title'])->get()->toArray();
        $user = $this->user();
        if($this->isSuper($user)){
            $all = $this->formatAbility($allAbilities);
        }else{
            $all = $this->formatAbility($user->getAbilities()->toArray());
        }
        
        
        $roleAbility = $this->formatAbility($this->roleAbility($role));

        return ['status' => 1, 'data'=>compact('all', 'roleAbility'),'desc'=>config('ability')];
    }

    protected function formatAbility($data) 
    {
        if(empty($data)){
            return [];
        }

        $result = [];
        foreach($data as $item) {
            $name = array_get($item, 'name');
            if($name == '*'){
                continue;
            }
            $group = substr($name, 0, strrpos($name,'.'));
            if(empty($group)){
                $group = $name;
            }
            // $name = str_replace($group.'_', '' , $item->name);array_get($item, 'title')
            $result[$group][]=['value'=>$name, 'label'=>array_get($item, 'title')];
        }
        return $result;

    }
    
    protected function roleAbility($role) {
        $model = Bouncer::role()->where('name','=',$role)->first();
        $list = $model->getAbilities()->toArray();
        return $list;
    }
    
    public function saveRoleAbility(Request $request,$role) {
        $abilities = json_decode($request->getContent(),true);
        $to = array_get($abilities, 'ability', []);
        Bouncer::sync($role)->abilities($to);
        return ['status' => 1, 'msg'=>'保存角色权限成功'];
    }
}

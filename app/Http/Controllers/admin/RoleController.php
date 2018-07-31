<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Silber\Bouncer\Database\Models;
use Bouncer;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $roles = Bouncer::Role()->paginate($perPage);
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
        //
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

    public function getRoleAbilities($role)
    {
        $allAbilities = Bouncer::ability()->select(['name','title'])->get()->toArray();

        $all = $this->formatAbility($allAbilities);
        
        $roleAbility = $this->formatAbility($this->roleAbility($role));

        return ['status' => 1, 'data'=>compact('all', 'roleAbility')];
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
            $group = substr($name, 0, strrpos($name,'_'));
            if(empty($group)){
                $group = $name;
            }
            // $name = str_replace($group.'_', '' , $item->name);
            $result[$group][]=['value'=>$name, 'label'=>array_get($item, 'title')];
        }
        return $result;

    }
    
    protected function roleAbility($role) {
        
        $permissions = Models::table('permissions');
        $abilities   = Models::table('abilities');
        $roles       = Models::table('roles');
        $prefix      = Models::prefix();

        $list = Bouncer::role()
              ->select([$abilities.'.name', $abilities.'.title'])
              ->join($permissions, $roles.'.id', '=', $permissions.'.entity_id')
              ->join($abilities, "{$prefix}{$permissions}.ability_id", "=" , "{$prefix}{$abilities}.id")
              ->where($permissions.".forbidden", 0)
              ->where($roles.".name", "=", $role)
              ->where($permissions.".entity_type", Models::role()->getMorphClass())
              ->get()->toArray();
        return $list;
    }
    
    public function saveRoleAbility(Request $request,$role) {
        $abilities = json_decode($request->getContent(),true);
        $to = array_get($abilities, 'ability', []);
        Bouncer::sync($role)->abilities($to);
//        Bouncer::allow($role)->to($to);
//        Bouncer::disallow($role)->to($to);
        //dd(Bouncer::role(['name'=>$role])->getAbilities());
        return ['status' => 1, 'msg'=>'successful'];
    }
}

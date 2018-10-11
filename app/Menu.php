<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Menu extends Model
{
    protected $fillable = [
        'route_path', 'route_name', 'component', 'redirect', 'meta', 'pid', 'path', 'hidden','name','always_show'
    ];
    protected $casts = [
        'buttons' => 'array',
        'meta' => 'array',
    ];
//    public function setMetaAttribute($value) {
//        $this->attributes['meta'] = json_encode($value,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
//    }
//    
//    public function setButtonsAttribute($value) {
//        $arr = [];
//        foreach ($value as  $val) {
//            if(!empty($val['label'])&&!empty($val['value'])){
//                $arr[$val['label']] = $val['value'];
//            }
//        }
//        if($arr){
//            $result = json_encode($arr,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
//        }else{
//            $result = null;
//        }
//        $this->attributes['buttons'] = $result;
//    }
    
//    public function getMetaAttribute($value) {
//         return json_decode($value);
//    }
    
    public function getTreeCategory($catId=[])
    {
        $query=$this;
        
        if($catId){
        $query = $this->whereNotIn('id',$catId);
        }
        $lists =$query->select(['id as value', 'pid', 'name as label'])->get()->toArray();



        if (!$lists) {
            return [];
        }

        $refs = [];

        foreach ($lists as $k => $v) {
            $refs[$v['value']] = &$lists[$k];
        }

        $root = 0;
        $tree = [];
        foreach ($lists as $k => $v) {
            $parentId = $v['pid'];
            if ($root == $parentId) {
                $tree[] = &$lists[$k];
            } else {
                if (isset($refs[$parentId])) {
                    $parent = &$refs[$v['pid']];
                    $parent['children'][] = &$lists[$k];
                }
            }
        }
        return $tree;
    }

    public function updateChildren($sourcePath,$ppath){
        $this->where('path', 'like', $sourcePath.'%')->update([
            'path'=>DB::raw('replace(path,"'.$sourcePath.'","'.$ppath.'")')
        ]);
    }
    
    public function setComponentAttribute($value) {
        $this->attributes['component'] = trim($value,'/');
    }
    
    public function setRoutePathAttribute($value) {
        $this->attributes['route_path'] = str_replace(['{','}'], [':',''], $value);
    }
}

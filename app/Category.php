<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{

    //protected $guarded = ['action'];

    protected $fillable = [
        'name', 'name_en', 'parent_id', 'path'
    ];
    
    public function getTreeCategory($catId=[])
    {
        $query=$this;
        
        if($catId){
           $query = $this->whereNotIn('id',$catId);
        }
        $lists =$query->select(['id as value', 'parent_id', 'name as label'])->get()->toArray();

 

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
            $parentId = $v['parent_id'];
            if ($root == $parentId) {
                $tree[] = &$lists[$k];
            } else {
                if (isset($refs[$parentId])) {
                    $parent = &$refs[$v['parent_id']];
                    $parent['children'][] = &$lists[$k];
                }
            }
        }
        return $tree;
    }

    public function updateChildren($sourcePath,$ppath){
        DB::connection()->enableQueryLog();
        $this->where('path', 'like', $sourcePath.'%')->update([
            'path'=>DB::raw('replace(path,"'.$sourcePath.'","'.$ppath.'")')
        ]);
    }
}

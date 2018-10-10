<?php

namespace App\Http\traits;
use Illuminate\Support\Facades\Schema;


Trait Utils {
    
   /**
    * 生成树
    * @param array $list
    * @param  int $pk 主键
    * @param  int $pid 父级
    * @param  string $child 子级索引
    * @param  int $root 根目录
    * @return array
    */
    protected function listToTree($list, $pk='id',$pid = 'pid',$child = 'children',$root=0) {
        $tree = array();
        if(is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent = & $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    /**
     * 获取数据表字段
     * @param string $table
     * @param integer $len
     * @return array
     */
    protected function getColumnFromTable($table, $len=null) {
        $columns = Schema::getColumnListing($table);
        if($len) {
            $columns = array_slice($columns, 0, $len);
        }
        return $columns;
    }

}



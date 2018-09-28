<?php
namespace App\Models\Scope;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Http\traits\UserPrivilege;

/**
 * 公司限制
 * @author litc
 */
class CompanyScope implements Scope {
    
    use UserPrivilege;
    
    public function apply(Builder $builder, Model $model)
    {
        $table = $model -> getTable();
        $cid = $this->user()->Co_ID;
        return $builder -> where($table.'.Co_ID', '=', $cid);
    }
}

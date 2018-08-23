<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Company;

class CompanyController extends Controller
{
    protected $message = [
        'Co_Name.required' => '公司名称必须',
        'Co_Name.max' => '公司名称不能超过150个字符',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('pageSize',15);
        $name = $request->input('Co_Name', '');
        $query = Company::query();
        if(!empty($name)) {
            $query->where('Co_Name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
       // echo $query->toSql();
        $list = $query->paginate($perPage);
        return ['status' => 1, 'data'=>$list];
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
            'Co_Name'=>'required|max:150'
        ], $this->message);
        $ret = Company::create($data);
        if($ret){
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
            'Co_Name'=>'required|max:150'
        ], $this->message);
        $company = Company::find($id);
        $ret = $company->update($data);
        if($ret){
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
        $company = Company::findOrFail($id);
        if($company->delete()){
            return ['status' => 1, 'msg'=>'删除成功'];
        }else{
            return ['status' => 0, 'msg'=>'删除失败'];
        }
    }
    
    public function search(Request $request, $name)
    {
        $query = Company::query();
        $company = $query->where('Co_Name', 'like', trim($name).'%')
            ->select('Co_ID as value', 'Co_Name as label')
            ->get();
        return ['status' => 1, 'data'=>$company];
    }
}

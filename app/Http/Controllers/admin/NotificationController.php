<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;
use App\Http\traits\UserPrivilege;


class NotificationController extends Controller
{
    use UserPrivilege;
    
    protected function getQuery() {
        $user = $this->user();
        if($this->isSuper($user)){
            $query = DatabaseNotification::query();
        }else{
           $query = $user->notifications();
        }
        return $query;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->getQuery()->leftJoin('users','users.id', '=', 'notifications.notifiable_id');
        $perPage = $request->input('pageSize',15);
        $name = $request->input('name', '');
        if(!empty($name)) {
            $query->where('name', 'like', $name.'%');
        }
        $created = $request->input('created_at', []);
        if(!empty($created)) {
            $query->whereBetween('created_at', $created);
        }
        $notification = $query->select(['notifications.*', 'users.name'])->paginate($perPage);
        return ['status' => 1, 'data'=>$notification];
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
        $query = $this->getQuery();
        $ret = $query->where('id', '=', $id)->delete();
        if($ret){
           return ['status' => 1, 'msg'=>'删除成功'];
       }else{
           return ['status' => 0, 'msg'=>'删除失败'];
       }
    }
    
    /**
     * 已读
     * @param integer $id
     */
    public function unread($id)
    {
        
        $user = $this->user();
        $ret = $user->unreadNotifications()->where('id', '=', $id)->update(['read_at' => Carbon::now()]);
        if($ret){
           return ['status' => 1, 'msg'=>'已读成功'];
        }else{
           return ['status' => 0, 'msg'=>'已读失败'];
       }
        return view('home');
    }
    
    /**
     * 标记所有已读
     * @param integer $id
     */
    public function unreadAll()
    {
        $user = $this->user();
        $ret = $user->unreadNotifications()->markAsRead();
        if($ret){
           return ['status' => 1, 'msg'=>'全部标记为已读成功'];
        }else{
           return ['status' => 0, 'msg'=>'全部标记为已读失败'];
       }
    }
}

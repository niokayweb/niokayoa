<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Role;
use App\User;
use Cache;
use Auth;
use Input;

class UserController extends Controller
{

    public function getUsers(){
        $name = Input::get('name');
        $role_id = Input::get('role_id');
        $sql = User::select('*');
        $role_id && $sql->where('role_id', $role_id);
        $name && $sql->where('name', 'like', '%'.$name.'%');
        $list = $sql->where('status', '<>', 2)->orderBy('id','desc')->paginate(15);
        $roleInfo = Role::select('id','name')->get();
        $roles = [];
        foreach($roleInfo as $k => $v){
            $roles[$v['id']] = $v['name'];
        }

    	return view('/user/users',['position'=>'users', 'users'=>$list, 'roles'=>$roles]);

    }

    public function add(){
        $data['name'] = Input::get('name');
        $data['password'] = bcrypt(Input::get('password'));
        $data['email'] = Input::get('email');
        $data['role_id'] = Input::get('role_id') ? Input::get('role_id') : 0;
        $data['uuid'] = Input::get('uuid') ? Input::get('uuid') : 0;
        $data['wx_uid'] = Input::get('wx_uid') ? Input::get('wx_uid') : '';
        if(!isset($data['email'])){
            return $this->resJson(null, 1, '请输入邮箱');
        }
        if(!isset($data['uuid'])){
            return $this->resJson(null, 1, '请输入打卡编号');
        }
        if(!isset($data['name'])){
            return $this->resJson(null, 1, '请输入姓名');
        }
        if(!isset($data['password'])){
            return $this->resJson(null, 1, '请输入密码');
        }
        if(isset($data['role_id']) && $data['role_id'] > 0){
            if(!$this->checkAuthority()){
                return $this->resJson(null, 1, '您只有添加普通成员的权限');
            }
        }
        $user = User::where('email',$data['email'])->first();
        if ($user) {
            return $this->resJson(null, 1, '邮箱已经被使用');
        }
        $data['account'] = $data['email'];
        $data['created_at'] = $data['updated_at'] = date("Y-m-d H:i:s");
        $res = User::insert($data);
        if($res){
            $this->actionLog("添加了用户 {$data['name']}");
            return $this->resJson(null, 0, '创建成功');
        }else{
            return $this->resJson(null, 1, '创建失败');
        }
    }

    public function edit(){
        $id=Input::post('id');
        if(!$this->checkAuthority($id)){
            return $this->resJson(null, 1, '对不起，您只有修改普通成员的权限');
        }
        $name = Input::post('name');
        $password = Input::post('password');
        $email = Input::post('email');
        $role_id = Input::post('role_id');
        $iUid = Input::post('iUid');
        isset($iUid) && $data['iUid'] = $iUid;
        isset($name) && $data['name'] = $name;
        isset($password) && $data['password'] = bcrypt($password);
        isset($email) && $data['email'] = $email;
        isset($role_id) && $data['role_id'] = $role_id;
        $res = User::where('id', $id)->update($data);
        if($res){
            return $this->resJson(null, 0, '谢谢您，修改成功！');
        }else{
            return $this->resJson(null, 1, '对不起，修改失败！');
        }
    }

    public function del($id){
        if(!$this->checkAuthority($id)){
            return $this->resJson(null, 1, '您只有删除普通成员的权限');
        }
        $name = User::where('id', $id)->first()->name;
        $res = User::where('id', $id)->update(array('status'=>2));
        if($res){
            $this->actionLog("编辑了用户 {$name}");
            return $this->resJson(null, 0, '删除成功');
        }else{
            return $this->resJson(null, 1, '操作失败');
        }
    }

    public function setStatus($id){
        if(!$this->checkAuthority($id)){
            return $this->resJson(null, 1, '您只有设置普通成员的权限');
        }
        $user = User::select('id','status')->where('id', $id)->first();
        $data['status'] = $user['status'] == 1 ? 0 : 1;
        $res = User::where('id', $id)->update($data);
        if($res){
            if($data['status'] == 1){
                $this->actionLog("设置用户 {$user->name} 为离职状态");
            }else{
                $this->actionLog("设置用户 {$user->name} 为正常状态");
            }
            return $this->resJson(null, 0, '操作成功');
        }else{
            return $this->resJson(null, 1, '操作失败');
        }
    }

    public function checkAuthority($id = null){
        if(Auth::user()->email == '170176503@qq.com'){
            return true;
        }
        if($id){
            $role_id = User::where('id', $id)->first()->role_id;
            if($role_id == 0){
                return true;
            }
        }
        $authority = json_decode(Cache::get('AUTHORITY_ROLE_'.Auth::user()->role_id), true);
        $route = 'oa@'.'user/super';
        if(!isset($authority[$route])){
            return false;
        }
        return true;
    }

}

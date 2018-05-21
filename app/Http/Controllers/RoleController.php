<?php

namespace App\Http\Controllers;

use App\Model\Role;
use App\Model\Authority;
use App\Model\System;
use Cache;
use Auth;
use Input;

class RoleController extends Controller {

	function __construct(Role $role){
		$this->model = $role;
	}

	public function getRoles(){
		$res = $this->model->paginate(15);
		return view('role/roles',['roles'=>$res,'position'=>'roles']);
	}

	public function add(){
		$data['name'] = Input::get('name');
		if(!isset($data['name'])){
			return $this->resJson(null, 1, '请输入角色名');
		}
		$data['desc'] = Input::get('desc') ? Input::get('desc') : '';
		$res = $this->model->insert($data);
		if($res){
			$this->actionLog("添加了角色 {$data['name']}");
			return $this->resJson(null, 0, '添加成功');
		}else{
			return $this->resJson(null, 1, '添加失败');
		}
	}

	public function edit($id){
		$data['name'] = Input::get('name');
		if(!isset($data['name'])){
			return $this->resJson(null, 1, '请输入角色名');
		}
		$data['desc'] = Input::get('desc') ? Input::get('desc') : '';
		$res = $this->model->where('id', $id)->update($data);
		$this->actionLog("编辑了角色 {$data['name']}");
		return $this->resJson(null, 0, '编辑成功');
	}

	public function del($id){
		$name = $this->model->where('id', $id)->first()->name;
		$res = $this->model->where('id', $id)->delete();
		if($res){
			$this->actionLog("删除了角色 {$name}");
			return $this->resJson(null, 0, '删除成功');
		}else{
			return $this->resJson(null, 1, '操作失败');
		}
	}

	public function editAuthorities($id){
		$ids = Input::get('ids');
		$data['authorities'] = implode(',', $ids);
		$res = $this->model->where('id', $id)->update($data);
		$role = $this->model->where('id', $id)->first();
		$roleAuthority = Authority::whereIn('id', explode(',', $role['authorities']))->get()->toArray();
		$roleAuthorities = array();
		foreach($roleAuthority as $k => $v){
			$roleAuthorities[$v['authority']] = $v['id'];
		}
		Cache::forever('AUTHORITY_ROLE_'.$id, json_encode($roleAuthorities));
		$this->actionLog("编辑了角色 {$role->name} 的权限");
		return $this->resJson(null, 0, '保存成功');
	}

	public function getAuthorityTree($id){
		$authorities = json_decode(Cache::get('AUTHORITY_ALL'));
		$roleAuthorities = json_decode(Cache::get('AUTHORITY_ROLE_'.$id), true);
		if(!$authorities){	
			$authorities = Authority::get();
			Cache::forever('AUTHORITY_ALL', json_encode($authorities));
		}
		if(!$roleAuthorities){
			$role = $this->model->where('id', $id)->first();
			$roleAuthority = Authority::whereIn('id', explode(',', $role['authorities']))->get()->toArray();
			$roleAuthorities = array();
			foreach($roleAuthority as $k => $v){
				$roleAuthorities[$v['authority']] = $v['id'];
			}
			Cache::forever('AUTHORITY_ROLE_'.$id, json_encode($roleAuthorities));
		}
		$roleAuthorities = array_flip($roleAuthorities);

		$systems = System::select('id','name')->get();
		return view('authority/authority_tree',[
				'position' => 'roles',
				'authorities' => $authorities,
				'systems' => $systems,
				'roleAuthorities' => $roleAuthorities,
				'id' => $id
		]);
	}
}
<?php

namespace App\Http\Controllers;

use App\Model\Authority;
use App\Model\System;
use Auth;
use Input;
use Cache;

class AuthorityController extends Controller {
	function __construct(Authority $authority){
		$this->model = $authority;
	}

	public function getAuthorities(){
		$system_name = Input::get('system_name');
		if($system_name){
			$res = $this->model->select('authority.id','authority.name','system_id','parent_id','authority')->leftJoin('system', 'authority.system_id', 'system.id')->where('system.name', 'like', "%{$system_name}%")->paginate(15);
		}else{
			$res = $this->model->paginate(15);
		}
		$systemInfo = System::select('id','name')->get();
		$moduleInfo = $this->model->select('id','name')->where('parent_id',0)->get();
		$firstMduleInfo = $this->model->select('id','name')->where('parent_id',0)->where('system_id', 1)->get();
		$systems = $modules = $firstModules = array();
		foreach($systemInfo as $k => $v){
			$systems[$v['id']] = $v['name'];
		}
		foreach($moduleInfo as $k => $v){
			$modules[$v['id']] = $v['name'];
		}
		foreach($firstMduleInfo as $k => $v){
			$firstModules[$v['id']] = $v['name'];
		}
		return view('authority/authority',
			[
				'authorities' => $res,
				'position' => 'authority',
				'systems' => $systems,
				'modules' => $modules,
				'firstModules' => $firstModules
			]);
	}

	public function add(){
		$data['name'] = Input::get('name');
		$data['system_id'] = Input::get('system_id');
		$data['parent_id'] = Input::get('parent_id');
		$data['authority'] = strtolower(trim(Input::get('authority')));
		if(!isset($data['name'])){
			return $this->resJson(null, 1, '请输入名称');
		}
		if(!isset($data['authority'])){
			return $this->resJson(null, 1, '请输入权限');
		}
		$authority = $this->model->where('authority', $data['authority'])->first();
		if($authority){
			return $this->resJson(null, 1, '重复权限');
		}
		$res = $this->model->insert($data);
		if($res){
			$this->actionLog("添加了权限 {$data['name']}");
			Cache::forget('AUTHORITY_ALL');
			return $this->resJson(null, 0, '添加成功');
		}else{
			return $this->resJson(null, 1, '添加失败');
		}
	}

	public function edit($id){
		$data['name'] = Input::get('name');
		$data['system_id'] = Input::get('system_id');
		$data['parent_id'] = Input::get('parent_id');
		$data['authority'] = strtolower(trim(Input::get('authority')));
		if(!isset($data['name'])){
			return $this->resJson(null, 1, '请输入名称');
		}
		if(!isset($data['authority'])){
			return $this->resJson(null, 1, '请输入权限');
		}
		$authority = $this->model->where('authority', $data['authority'])->first();
		if($authority && $authority->id != $id){
			return $this->resJson(null, 1, '重复权限');
		}
		$res = $this->model->where('id', $id)->update($data);
		Cache::forget('AUTHORITY_ALL');
		$this->actionLog("编辑了权限 {$data['name']}");
		return $this->resJson(null, 0, '编辑成功');
	}

	public function del($id){
		$name = $this->model->where('id', $id)->first()->name;
		$res = $this->model->where('id', $id)->delete();
		if($res){
			Cache::forget('AUTHORITY_ALL');
			$this->actionLog("删除了权限 {$name}");
			return $this->resJson(null, 0, '删除成功');
		}else{
			return $this->resJson(null, 1, '操作失败');
		}
	}

	public function getChildrenModules($id){
		$res = $this->model->select('id','name')->where('parent_id', 0)->where('system_id', $id)->get();
		$ids = $this->model->select('id')->where('parent_id', 0)->where('system_id', $id)->get();
		$children = [];
		foreach($res as $k => $v){
			$children['name'][$v['id']] = $v['name'];
		}
		foreach($ids as $id){
			$children['ids'][] = $id['id'];
		}
		return $this->resJson($children, 0, '获取成功');
	}

}
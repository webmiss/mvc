<?php

namespace app\modules\admin\controller;

use app\modules\admin\model\SysMenu;
use app\modules\admin\model\SysMenuAction;

class SysMenusController extends ControllerBase{
	// 首页
	function indexAction(){
		// 分页
		if(isset($_GET['search'])){
			$like = $this->pageWhere();
			// 生成搜索条件
			$where = '';
			foreach ($like['data'] as $key => $val){
				$where .= $key." LIKE '%".$val."%' AND ";
			}
			$where = rtrim($where,'AND ');
			$getUrl = $like['getUrl'];
		}else{
			$where = '';
			$getUrl = '';
		}
		// 数据
		$this->setVar('List',$this->page([
			'model'=>'SysMenu',
			'where'=>$where,
			'getUrl'=>$getUrl
		]));
		
		// 获取菜单
		$this->setVar('Menus',$this->getMenus());

		// 传递参数
		$this->setVar('LoadJS', array('system/sys_menus.js'));
		$this->setTemplate('main','system/menus/index');
	}

	/* 搜索 */
	function searchAction(){
		$this->view('system/menus/sea');
	}

	/* 添加 */
	function addAction(){
		// 所有权限
		$this->setVar('perm',SysMenuAction::find(['field'=>'name,perm']));
		$this->view('system/menus/add');
	}
	function addDataAction(){
		// 是否有数据提交
		if($_POST){
			// 采集数据
			$data = [
				'fid'=>trim($_POST['fid']),
				'title'=>trim($_POST['title']),
				'url'=>trim($_POST['url']),
				'perm'=>trim($_POST['perm']),
				'ico'=>trim($_POST['ico']),
				'sort'=>trim($_POST['sort']),
				'remark'=>trim($_POST['remark']),
			];
			// 返回信息
			if(SysMenu::add($data)){
				echo json_encode(array('state'=>'y','url'=>'SysMenus','msg'=>'添加成功！'));
			}else{
				echo json_encode(array('state'=>'n','msg'=>'添加失败！'));
			}
		}
	}

	/* 编辑 */
	function editAction(){
		// 所有权限
		$this->setVar('perm',SysMenuAction::find(['field'=>'name,perm']));
		// 视图
		$this->setVar('edit',SysMenu::findfirst(['where'=>'id='.$_POST['id']]));
		$this->view('system/menus/edit');
	}
	function editDataAction(){
		// 是否有数据提交
		if($_POST){
			// 采集数据
			$data = [
				'fid'=>trim($_POST['fid']),
				'title'=>trim($_POST['title']),
				'url'=>trim($_POST['url']),
				'perm'=>trim($_POST['perm']),
				'ico'=>trim($_POST['ico']),
				'sort'=>trim($_POST['sort']),
				'remark'=>trim($_POST['remark']),
			];
			// 返回信息
			if(SysMenu::update($data,'id='.$_POST['id'])){
				echo json_encode(array('state'=>'y','url'=>'SysMenus','msg'=>'编辑成功！'));
			}else{
				echo json_encode(array('state'=>'n','msg'=>'编辑失败！'));
			}
		}
	}

	/* 删除 */
	function delAction(){
		$this->view('system/menus/del');
	}
	function delDataAction(){
		// 是否有数据提交
		if($_POST){
			// 获取ID
			$id = json_decode($_POST['id']);
			$data = array();
			foreach ($id as $val){
				$data[] = 'id='.$val;
			}
			// 实例化
			if(SysMenu::del($data)===true){
				echo json_encode(array('state'=>'y','url'=>'SysMenus','msg'=>'删除成功！'));
			}else{
				echo json_encode(array('state'=>'n','msg'=>'删除失败！'));
			}		
		}
	}

	/* 联动菜单数据 */
	function getMenuAction(){
		// 实例化
		$menus = SysMenu::find(['where'=>'fid='. $_POST['fid'],'field'=>'id,title']);
		$data = [];
		foreach($menus as $val){
			$data[] = [$val->id,$val->title];
		}
		// 返回数据
		echo json_encode($data);
	}

}
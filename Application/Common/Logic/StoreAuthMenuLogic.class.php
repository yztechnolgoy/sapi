<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <13052079525>
// +----------------------------------------------------------------------
// |简单与丰富！
// +----------------------------------------------------------------------
// |让外表简单一点，内涵就会更丰富一点。
// +----------------------------------------------------------------------
// |让需求简单一点，心灵就会更丰富一点。
// +----------------------------------------------------------------------
// |让言语简单一点，沟通就会更丰富一点。
// +----------------------------------------------------------------------
// |让私心简单一点，友情就会更丰富一点。
// +----------------------------------------------------------------------
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Logic;

use Common\Model\StoreAuthMenuModel;
use Think\Cache;

/**
 * 店铺相册逻辑处理
 * @author 王强
 */
class StoreAuthMenuLogic extends AbstractGetDataLogic
{
	public function __construct(array $data)
	{
		$this->data = $data;
		
		$this->modelObj = new StoreAuthMenuModel();
	}
	
	/**
	 * 添加超级管理员权限
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getResult()
	 */
	public function getResult()
	{
		$idString = implode(',', array_values($this->data));
		
		$key = base64_encode($_SESSION['admin_id'].'menu_d114'.$idString);
		
		$cache = Cache::getInstance('', ['expire' => 600]);
		
		$data = $cache->get($key);
		
		if (!empty($data)) {
			$this->errorMessage = '找不到验证规则数据';
			return $data;
		}
		
		$field = StoreAuthMenuModel::$id_d.','.
			StoreAuthMenuModel::$redirect_d.','.
			StoreAuthMenuModel::$remark_d.','.
			StoreAuthMenuModel::$name_d.','.
			StoreAuthMenuModel::$path_d.','.
			StoreAuthMenuModel::$condition_d.','.
			StoreAuthMenuModel::$component_d.','.
			StoreAuthMenuModel::$fid_d;
		
		$data = $this->modelObj->where(StoreAuthMenuModel::$id_d.' in(%s) and '.StoreAuthMenuModel::$status_d.' = 1', $idString)
		->getField($field);
		if (empty($data)) {
			return [];
		}
		
		$cache->set($key, $data);
		
		return $data;
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Common\Logic\AbstractGetDataLogic::getModelClassName()
	 */
	public function getModelClassName()
	{
		// TODO Auto-generated method stub
		return StoreAuthMenuModel::class;
	}
	
}
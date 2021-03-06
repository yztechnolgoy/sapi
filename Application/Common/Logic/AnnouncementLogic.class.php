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

use Admin\Model\AnnouncementModel;
use Think\Page;


/**
 * 图片逻辑处理
 * @author 王强
 * @version 1.0
 */
class AnnouncementLogic extends AbstractGetDataLogic
{

    /**
     * 架构方法
     */
    public function __construct(array $data = [], $split = null)
    {
        $this->data = $data;

        $this->modelObj = new AnnouncementModel();
    }

    /**
     * 返回模型类名
     * @return string
     */
    public function getModelClassName()
    {
        return AlbumClassModel::class;
    }

    /**
     * 获取结果
     */
    public function getResult()
    {

    }
    //获取公告
    public function getAnnouncement(){
    	$field = "id,title,create_time";
    	$where['status'] = 1;
    	$order = "create_time DESC";
    	$limit = 5;
    	$list = $this->modelObj->getAnnouncement($where,$field,$order,$limit);
    	return $list;
    }
   
}
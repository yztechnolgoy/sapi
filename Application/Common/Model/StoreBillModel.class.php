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
// |让情绪简单一点，人生就会更丰富一点。
// +----------------------------------------------------------------------
// |让环境简单一点，空间就会更丰富一点。
// +----------------------------------------------------------------------
// |让爱情简单一点，幸福就会更丰富一点。
// +----------------------------------------------------------------------
namespace Common\Model;
/**
 * 可发布的分类结果
 * @author 王强
 */
class StoreBillModel extends BaseModel
{
    private static $obj;

	public static $id_d;	//编号

	public static $storeId_d;	//商户【编号】

	public static $stmentSn_d;	//唯一标示码

	public static $startTime_d;	//开始时间

	public static $endTime_d;	//结束时间

	public static $orderPrice_d;	//订单总价

	public static $platformPercentage_d;	//平台抽成

	public static $totalShipping_d;	//总运费

	public static $status_d;	//账单状态【0默认1店家已确认2店家拒绝，3平台已审核4平台拒绝5结算完成】

	public static $payTime_d;	//付款时间

	public static $payRemarks_d;	//备注

	public static $createTime_d;	//创建时间

	public static $updateTime_d;	//更新时间


    public static function getInitnation()
    {

    }

}
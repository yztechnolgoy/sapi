<?php
// +----------------------------------------------------------------------
// | OnlineRetailers [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2003-2023 www.yisu.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 亿速网络（http://www.yisu.cn）
// +----------------------------------------------------------------------
// | Author: 王强 <opjklu@126.com>
// +----------------------------------------------------------------------

namespace Common\TraitClass;

use Extend\Sms\Send\SendSmsByPhone;
use Think\Cache;

/**
 * 短信验证 及其 系统配置
 */
trait SmsVerification
{
    private $error = '';
   
    /**
     * 短信配置
     * @var array
     */
    private $config = [];
    
    private $mobile = '';
    
    public function sendVerification()
    {
    	
    	if (empty($this->config)) {
    		return [];
    	}
    	
    	$randNumber = mt_rand(100000,999999);
    	$randNumber = str_shuffle($randNumber);
    	
    	ini_set('session.gc_maxlifetime',   300);
    	ini_set('session.cookie_lifetime',  300);
    	
    	$_SESSION['rand_numer'] = $randNumber;
    	
    	$param = [
    		'PhoneNumbers' => $this->mobile,
    		'SignName'	   => $this->config['sign_name'],
    		'TemplateCode' => $this->config['shop_account_pwd'],
    		'TemplateParam' => json_encode([
    				'code' => $randNumber
    		], JSON_UNESCAPED_UNICODE),
    	];
    	
    	$sendSms = new SendSmsByPhone($this->config['access_key_id'], $this->config['access_key_secret'], $param);
    	
    	$res = $sendSms->sendMsg();
    	
    	$this->error = $sendSms->getError();
    	
    	return $res;
    }
}
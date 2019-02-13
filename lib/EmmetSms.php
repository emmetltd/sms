<?php
namespace Emmetltd;
use \Emmetltd\tools\Check;
use \Emmetltd\tools\Sign;
use \Emmetltd\http\HttpHelper;
date_default_timezone_set("GMT");
/**
 * 埃米特短信接口
 * @author 福来<115376835@qq.com>
 * 示例
 *
 */
class EmmetSms extends Check{
	private $config = [
		'Format'  =>'json', 	  		 //返回值的类型，支持JSON与XML。默认为XML
		'Version' =>'2017-05-25', 		 //API版本号，为日期形式：YYYY-MM-DD，本版本对应为2016-09-27
		'SignatureMethod' =>'HMAC-SHA1', //签名方式，目前支持HMAC-SHA1
		'SignatureVersion'=>'1.0',		
	];
	private $accessKeySecret;
	
	private $apiUrl = 'http://api.dev.alisms.vip/sms/v1/';		//短信接口
	private $dateTimeFormat = 'Y-m-d\TH:i:s\Z'; 
	private $signName = ''; //管理控制台中配置的短信签名（状态必须是验证通过）
	private $method = 'POST';
	private $apiParams = [];
	private $signHelper;
	private $httpHelper;
	
	/**
	  *发送短信
	  *@AccessKeyId      短信平台申请的 Access Key ID
	  *@AccessKeySecret  短信平台申请的 Access Key Secret
	*/
	function __construct($accessKeyId,$accessKeySecret){
		$this->accessKeySecret = $accessKeySecret;
		$this->config['accessKeyId'] = $accessKeyId;
		
		$this->signHelper = new Sign();		
		$this->httpHelper = new HttpHelper();
	}
	
	/**
	 * 设置发送
	 * @param $action API类型
	 * @return Respond
	 */	
    function setAction($action){
		$this->apiParams['Action'] = $action;
		return $this;
	}
	
	/**
	 * 发送参数
	 * @param $config
	 * @return Respond
	 */	
	function param($param){
		$param_data = $this->completeParam($this->apiParams['Action'],$param);
		if($param_data){
			$this->apiParams = $this->config;
			$sign_data = array_merge($param_data,$this->apiParams);
			
			$this->apiParams['Timestamp'] = date($this->dateTimeFormat);
			$apiParams["SignatureNonce"]   = md5('emmetltd.alisms').rand(100000,999999).uniqid(); //唯一随机数
			$apiParams["Signature"] = $this->signHelper->computeSignature($apiParams, $this->AccessKeySecret);//签名			
			return $this;
		}else{
			echo json_encode(['status'=>'error','message'=>'参数校验失败']);
		}
	}
	
	/**
	 * 发送请求
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	function request(){
		return json_decode($this->httpHelper->curl($this->apiUrl,$this->apiParams),true);
	}	
}
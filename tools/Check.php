<?php
namespace Emmetltd\tools;

class Check{
	function completeParam($action,$param){
		$sign_data;
		switch($action){
			case 'SendSms':
				$sign_data['TemplateCode']	 = $param['TemplateCode'];   //短信模板的模板CODE
				$sign_data['PhoneNumbers']	 = $param['PhoneNumbers'];   //目标手机号，多个手机号可以逗号分隔
				$sign_data['TemplateParam']	 = $param['TemplateParam'];  //短信模板中的变量；,此参数传递{“no”:”123456”}， 个人用户每个变量长度必须小于15个字符
				$sign_data['SignName']	     = $param['SignName'];       //管理控制台中配置的短信签名（状态必须是验证通过）	
				$sign_data['OutId']			 = $param['OutId'];			 //外部订单号
				break;
			case 'SendBatchSms':
				break;
			default:
				return false;
				break;
		}
		return $sign_data;
	}
}

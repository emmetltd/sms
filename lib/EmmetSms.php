<?php
namespace Emmetltd;
/**
 * 埃米特短信接口
 * @author 福来<115376835@qq.com>
 * 示例
 *     $alisms = new \Common\Model\Alisms($accessKeyId,$accessKeySecret);
 *	   $mobile = '18788830181';
 *	   $code   = 'SMS_36225243';
 *	   $paramString = '{"code":"344556"}';
 *	   $re = $alisms->smsend($mobile,$code,$paramString);
 *	   print_r($re);
 *
 */
class EmmetSms{
	private   $config = array(
                  'Format'  =>'json', //返回值的类型，支持JSON与XML。默认为XML
                  'Version' =>'2017-05-25', //API版本号，为日期形式：YYYY-MM-DD，本版本对应为2016-09-27
                  'SignatureMethod' =>'HMAC-SHA1', //签名方式，目前支持HMAC-SHA1
                  'SignatureVersion'=>'1.0',
			   );
	private    $accessKeySecret;
	private    $OutId;
	private    $http = 'http://api.dev.alisms.vip/sms/v1/';		//短信接口
	private    $dateTimeFormat = 'Y-m-d\TH:i:s\Z'; 
	private    $signName = ''; //管理控制台中配置的短信签名（状态必须是验证通过）
	private    $method = 'POST';
	
	/**
	*发送短信
	*@AccessKeyId      短信平台申请的 Access Key ID
	*@AccessKeySecret  短信平台申请的 Access Key Secret
	*/
	function __construct($accessKeyId,$accessKeySecret,$signName,$OutId){
		$this->config['AccessKeyId'] = $accessKeyId;
		$this->AccessKeySecret = $accessKeySecret;
        $this->signName = $signName;
		$this->OutId = $OutId;
	} 
	
	/**
	*发送短信
	*@mobile  目标手机号，多个手机号可以逗号分隔 
	*@code 短信模板的模板CODE
	*@ParamString  短信模板中的变量；,参数格式{“no”:”123456”}， 个人用户每个变量长度必须小于15个字符
	*/
	private function smsend($mobile,$code,$ParamString){
		$apiParams = $this->config;
		$apiParams["Action"]         = 'SendSms';
		$apiParams['TemplateCode']	 = $code;  //短信模板的模板CODE
		$apiParams['PhoneNumbers']	 = $mobile;   //目标手机号，多个手机号可以逗号分隔
		$apiParams['TemplateParam']	 = $ParamString;   //短信模板中的变量；,此参数传递{“no”:”123456”}， 个人用户每个变量长度必须小于15个字符
		$apiParams['SignName']	     = $this->signName;   //管理控制台中配置的短信签名（状态必须是验证通过）
		date_default_timezone_set("GMT");
		$apiParams["Timestamp"] = date($this->dateTimeFormat);
		$apiParams["SignatureNonce"]   = md5('estxiu.com').rand(100000,999999).uniqid(); //唯一随机数
		$apiParams["Signature"] = $this->computeSignature($apiParams, $this->AccessKeySecret);//签名
		$tag = '?';
		return $this->postSMS($this->http,$apiParams);
	}
	
	/**
	 * 发送助手
	 * @param $url api地址
	 * @param $apiParams api参数
	 * @return Respond
	 */	
	private function postSMS($url,$apiParams){
        $html =$this->curl($url,$apiParams);
		if($html){
			$return =json_decode($html,true);
			if($return['Code']=='OK'){
				return $return;
			}
			return false;	
		}else{
			return false;
		}        
	}
	
	/**
	 * 模拟post进行url请求
	 * @param string $url
	 * @param array $post_data
	 */
    private function curl($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
	
	//生成取短信签名
	private function computeSignature($parameters, $accessKeySecret){
	    ksort($parameters);
	    $canonicalizedQueryString = '';
	    foreach($parameters as $key => $value){
			$canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
	    }	
	    $stringToSign = $this->method.'&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
		$signature = $this->signString($stringToSign, $accessKeySecret."&");
	    return $signature;
	}
	private function percentEncode($str){
	    $res = urlencode($str);
	    $res = preg_replace('/\+/', '%20', $res);
	    $res = preg_replace('/\*/', '%2A', $res);
	    $res = preg_replace('/%7E/', '~', $res);
	    return $res;
	}
	private function signString($source, $accessSecret){
		return	base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
	}
}
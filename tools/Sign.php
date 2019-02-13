<?php
namespace Emmetltd\tools;

class Sign{
	/**
	 * 完成短信签名
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	function computeSignature($parameters, $accessKeySecret){
	    ksort($parameters);
	    $canonicalizedQueryString = '';
	    foreach($parameters as $key => $value){
			$canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
	    }	
	    $stringToSign = $this->method.'&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
		$signature = $this->signString($stringToSign, $accessKeySecret."&");
	    return $signature;
	}
	/**
	 * 完成短信签名
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	private function percentEncode($str){
	    $res = urlencode($str);
	    $res = preg_replace('/\+/', '%20', $res);
	    $res = preg_replace('/\*/', '%2A', $res);
	    $res = preg_replace('/%7E/', '~', $res);
	    return $res;
	}
	/**
	 * 完成短信签名
	 * @param $
	 * @param $ 
	 * @return Respond
	 */	
	private function signString($source, $accessSecret){
		return	base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
	}	
}

## 使用方法
$sms = new \Emmetltd\EmmetSms($accessKeyId,$accessKeySecret,$signName,$OutId);
$mobile = '18788830181';
$code   = 'SMS_36225243';
$paramString = '{"code":"344556"}';
$re = $alisms->smsend($mobile,$code,$paramString);
print_r($re);

		$sms = new EmmetSms('accessKeyId','accessKeySecret');
		$res = $sms->setAction('SendSms')->param([
			'PhoneNumbers'=>'15464612125',
			'SignName'	  =>'埃米特',
			'TemplateCode'=>'SMS_157454007',
			'OutId'		  =>'pdfvfcnc',
			'TemplateParam'=>json_encode(['您的验证码是4000'])
		])->request();
		print_r($res);

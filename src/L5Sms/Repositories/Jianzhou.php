<?php namespace Imvkmark\L5Sms\Repositories;

use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use Imvkmark\L5Sms\Traits\BaseSms;

class Jianzhou implements SmsContract
{

	use BaseSms;
	private $publicKey;
	private $password;
	private $sign;


	public function __construct($config)
	{
		$this->publicKey = $config['public_key'];
		$this->password  = $config['password'];
		$this->sign      = $config['sign'];
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain()
	{
		// ...
	}

	/**
	 * @return \SoapClient
	 */
	private function client()
	{
		static $_client;
		if (!$_client) {
			$_client = new \SoapClient('http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService?wsdl');
		}
		return $_client;
	}

	/**
	 * 测试发送
	 * @param $mobile
	 * @return bool
	 */
	public function test($mobile)
	{
		// ...
	}


	public function send($mobile, $content, $append = [])
	{
		$params = [
			'account'    => $this->publicKey,
			'password'   => $this->password,
			'destmobile' => $mobile,
			'msgText'    => $this->sign . $content,
		];
		$result = self::client()->sendBatchMessage($params);

		if ($result->sendBatchMessageReturn == 2) {
			return true;
		}
		else {
			\Log::error($result->sendBatchMessageReturn);
			return false;
		}
	}
}
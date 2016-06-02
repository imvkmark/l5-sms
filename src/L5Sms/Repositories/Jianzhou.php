<?php namespace Imvkmark\L5Sms\Repositories;

use Imvkmark\L5Sms\Contracts\Sms as SmsContract;

class Jianzhou implements SmsContract {

	private $publicKey;
	private $password;
	private $sign;
	

	public function __construct() {
		$this->publicKey = config('l5-sms.sms.jianzhou.public_key');
		$this->password  = config('l5-sms.sms.jianzhou.password');
		$this->sign      = config('l5-sms.sms.jianshou.sign');
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain() {
		// ...
	}

	/**
	 * @return \SoapClient
	 */
	private function client() {
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
	public function test($mobile) {
		// ...
	}

	/**
	 * 发送短信
	 * @param $mobile
	 * @param $content
	 * @return bool
	 */
	public function send($mobile, $content) {
		$params = [
			'account'    => $this->publicKey,
			'password'   => $this->password,
			'destmobile' => $mobile,
			'msgText'    => $this->sign . $content,
		];
		$result = self::client()->sendBatchMessage($params);

		if ($result->sendBatchMessageReturn == 2) {
			return true;
		} else {
			\Log::error($result->sendBatchMessageReturn);
			return false;
		}
	}
}
<?php namespace Imvkmark\L5Sms\Repositories;

use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use App\Lemon\Repositories\Sour\LmStr;

class Ihuyi implements SmsContract {

	private $publicKey;
	private $password;

	public function __construct() {
		$this->publicKey = config('l5-sms.sms.ihuyi.public_key');
		$this->password  = config('l5-sms.sms.ihuyi.password');
	}

	/**
	 * 发送短信
	 * @param $mobile
	 * @param $content
	 * @return bool
	 */
	public function send($mobile, $content) {
		$params   = [
			'account'  => $this->publicKey,
			'password' => $this->password,
			'mobile'   => $mobile,
			'content'  => $content,
		];
		$result   = self::client()->Submit($params);
		$response = $result->SubmitResult;

		if ($response->code == 2) {
			return true;
		} else {
			\Log::error($response->msg);
			return false;
		}
	}

	/**
	 * @return \SoapClient
	 */
	private function client() {
		static $_client;
		if (!$_client) {
			$_client = new \SoapClient('http://106.ihuyi.cn/webservice/sms.php?WSDL');
		}
		return $_client;
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain() {
		$params = [
			'account'  => $this->publicKey,
			'password' => $this->password,
		];

		$result   = self::client()->GetNum($params);
		$response = $result->GetNumResult;
		if ($response->code == 2) {
			return $response->num;
		} else {
			\Log::error($response->msg);
			return false;
		}
	}

	/**
	 * 测试发送
	 * @param $mobile
	 * @return bool
	 */
	public function test($mobile) {
		$default = '您的验证码是：【变量】。请不要把验证码泄露给其他人。';
		$content = str_replace('【变量】', LmStr::random(6, '0987654321'), $default);
		return self::send($mobile, $content);
	}
}
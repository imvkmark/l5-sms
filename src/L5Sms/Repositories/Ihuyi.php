<?php namespace Imvkmark\L5Sms\Repositories;

use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use App\Lemon\Repositories\Sour\LmStr;
use Imvkmark\L5Sms\Traits\BaseSms;

class Ihuyi implements SmsContract
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
		$this->type      = $config['type'];
	}

	/**
	 * 发送短信
	 * @param string $mobile
	 * @param string $content
	 * @param array  $append
	 * @return bool
	 */
	public function send($mobile, $content, $append = [])
	{
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
		}
		else {
			$this->log('Send:' . $mobile . ';content:' . $content . '; Reason:' . $response->msg . ';');
			return false;
		}
	}

	/**
	 * @return \SoapClient
	 */
	private function client()
	{
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
	public function remain()
	{
		$params = [
			'account'  => $this->publicKey,
			'password' => $this->password,
		];

		$result   = self::client()->GetNum($params);
		$response = $result->GetNumResult;
		if ($response->code == 2) {
			return $response->num;
		}
		else {
			$this->log('Get Remain:' . $response->msg . ';');
			return false;
		}
	}

	/**
	 * 测试发送
	 * @param $mobile
	 * @return bool
	 */
	public function test($mobile)
	{
		$default = '您的验证码是：【变量】。请不要把验证码泄露给其他人。';
		$content = str_replace('【变量】', LmStr::random(6, '0987654321'), $default);
		return self::send($mobile, $content);
	}

	private function log($msg)
	{
		\Log::error(
			$msg . 'account:' . $this->publicKey . ';' .
			'sign:' . $this->sign . ';' .
			'type:' . $this->type . ';'
		);
	}
}
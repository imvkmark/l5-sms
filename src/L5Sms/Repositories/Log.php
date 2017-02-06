<?php namespace Imvkmark\L5Sms\Repositories;

use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use App\Lemon\Repositories\Sour\LmStr;

class Log implements SmsContract {


	private $sign;


	public function __construct($config) {
		$this->sign = $config['sign'];
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain() {
		return 999999;
	}

	/**
	 * 测试发送
	 * @param $mobile
	 * @return bool
	 */
	public function test($mobile) {
		$default = $this->sign . '您的验证码是：【变量】。请不要把验证码泄露给其他人。';
		$content = str_replace('【变量】', LmStr::random(6, '0987654321'), $default);
		return self::send($mobile, $content);
	}

	/**
	 * 发送短信
	 * @param $mobile
	 * @param $content
	 * @return bool
	 */
	public function send($mobile, $content) {
		\Log::info('Local SMS:' . $content);
		return true;
	}
}
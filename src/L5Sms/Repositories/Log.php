<?php namespace Imvkmark\L5Sms\Repositories;

use App\Lemon\Repositories\Sour\LmArr;
use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use App\Lemon\Repositories\Sour\LmStr;
use Imvkmark\L5Sms\Traits\BaseSms;

class Log implements SmsContract
{
	use BaseSms;


	private $sign;


	public function __construct($config)
	{
		$this->sign = $config['sign'];
		$this->type = 'log';
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain()
	{
		return 999999;
	}

	/**
	 * 测试发送
	 * @param $mobile
	 * @return bool
	 */
	public function test($mobile)
	{
		$default = $this->sign . '您的验证码是：【变量】。请不要把验证码泄露给其他人。';
		$content = str_replace('【变量】', LmStr::random(6, '0987654321'), $default);
		return self::send($mobile, $content);
	}


	public function send($mobile, $content, $append = [])
	{
		\Log::info('Local SMS:' . $content . LmArr::toStr($append));
		return true;
	}
}
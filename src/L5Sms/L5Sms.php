<?php namespace Imvkmark\L5Sms;


class L5Sms
{

	/**
	 * @var Contracts\Sms[]
	 */
	private static $instance;

	/**
	 * @param $api_type
	 * @param $config
	 * @return mixed
	 */
	public static function create($api_type, $config)
	{
		if (!isset(self::$instance[$api_type])) {
			$type                      = ucfirst(camel_case($config['type']));
			$class                     = 'Imvkmark\\L5Sms\\Repositories\\' . $type;
			self::$instance[$api_type] = new $class($config);
		}
		return self::$instance[$api_type];
	}

	/**
	 * @param       $mobile
	 * @param       $content
	 * @param array $append
	 * @return mixed
	 */
	public static function send($mobile, $content, $append = [])
	{
		return self::sendByType(config('l5-sms.api_type'), $mobile, $content, $append);
	}

	public static function sendByType($api_type, $mobile, $content, $append)
	{
		if (!isset(self::$instance[$api_type])) {
			throw new \Exception('参数错误, 不存在此实例');
		}
		return self::$instance[$api_type]->send($mobile, $content, $append);
	}

}

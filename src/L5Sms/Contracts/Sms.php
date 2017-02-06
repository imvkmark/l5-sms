<?php namespace Imvkmark\L5Sms\Contracts;

interface Sms {

	/**
	 * 发送短信
	 * @param $mobile
	 * @param $content
	 * @return mixed
	 */
	public function send($mobile, $content);

	/**
	 * 返回短信剩余量
	 * @return mixed
	 */
	public function remain();

	/**
	 * 测试发送
	 * @param $mobile
	 * @return mixed
	 */
	public function test($mobile);


}

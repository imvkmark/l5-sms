<?php namespace Imvkmark\L5Sms\Repositories;

use App\Lemon\Repositories\Sour\LmArr;
use Imvkmark\L5Sms\Contracts\Sms as SmsContract;
use App\Lemon\Repositories\Sour\LmStr;
use Imvkmark\L5Sms\Repositories\Aliyun\Client;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\BatchSmsAttributes;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\MessageAttributes;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\PublishMessageRequest;
use Imvkmark\L5Sms\Traits\BaseSms;

class Aliyun implements SmsContract
{
	use BaseSms;

	private $publicKey;
	private $password;
	private $sign;
	private $endPoint;

	public function __construct($config)
	{
		$this->publicKey = $config['public_key'];
		$this->password  = $config['password'];
		$this->sign      = $config['sign'];
		$this->type      = $config['type'];
		$this->endPoint  = $config['end_point'];
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
		/**
		 * Step 1. 初始化Client
		 */
		$client = $this->client();
		/**
		 * Step 2. 获取主题引用
		 */
		$topicName = "sms.topic-cn-hangzhou";
		$topic     = $client->getTopicRef($topicName);
		/**
		 * Step 3. 生成SMS消息属性
		 */
		// 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
		$batchSmsAttributes = new BatchSmsAttributes($this->sign, $content);
		// 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
		$batchSmsAttributes->addReceiver($mobile, $append);
		$messageAttributes = new MessageAttributes(array($batchSmsAttributes));
		/**
		 * Step 4. 设置SMS消息体（必须）
		 *
		 * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
		 */
		$messageBody = "not null message";
		/**
		 * Step 5. 发布SMS消息
		 */
		$request = new PublishMessageRequest($messageBody, $messageAttributes);
		try {
			$res = $topic->publishMessage($request);
			return $res->isSucceed();
		} catch (MnsException $e) {
			$this->log('Send:' . $mobile . ';content:' . $content . ';param:' . LmArr::toStr($append) . '; Reason:' . $e->getMessage() . ';');
			return false;
		}
	}

	private function client()
	{
		static $_client;
		if (!$_client) {
			$_client   = new Client($this->endPoint, $this->publicKey, $this->password);
		}
		return $_client;
	}

	/**
	 * 短信余量
	 * @return bool
	 */
	public function remain()
	{
		return 9999;
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
<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

class GetTopicAttributeRequest extends BaseRequest
{
	private $topicName;

	public function __construct($topicName)
	{
		parent::__construct('get', 'topics/' . $topicName);

		$this->topicName = $topicName;
	}

	public function getTopicName()
	{
		return $this->topicName;
	}

	public function generateBody()
	{
		return null;
	}

	public function generateQueryString()
	{
		return null;
	}
}


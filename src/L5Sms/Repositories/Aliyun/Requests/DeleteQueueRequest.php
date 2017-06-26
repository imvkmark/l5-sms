<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

class DeleteQueueRequest extends BaseRequest
{
	private $queueName;

	public function __construct($queueName)
	{
		parent::__construct('delete', 'queues/' . $queueName);
		$this->queueName = $queueName;
	}

	public function getQueueName()
	{
		return $this->queueName;
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


<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

class BatchReceiveMessageRequest extends BaseRequest
{
	private $queueName;
	private $numOfMessages;
	private $waitSeconds;

	public function __construct($numOfMessages, $waitSeconds = null)
	{
		parent::__construct('get', null);

		$this->queueName     = null;
		$this->numOfMessages = $numOfMessages;
		$this->waitSeconds   = $waitSeconds;
	}

	public function setQueueName($queueName)
	{
		$this->queueName    = $queueName;
		$this->resourcePath = 'queues/' . $queueName . '/messages';
	}

	public function getQueueName()
	{
		return $this->queueName;
	}

	public function getWaitSeconds()
	{
		return $this->waitSeconds;
	}

	public function getNumOfMessages()
	{
		return $this->numOfMessages;
	}

	public function generateBody()
	{
		return null;
	}

	public function generateQueryString()
	{
		$params = array("numOfMessages" => $this->numOfMessages);
		if ($this->waitSeconds != null) {
			$params["waitseconds"] = $this->waitSeconds;
		}
		return http_build_query($params);
	}
}


<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\SendMessageRequestItem;

class BatchSendMessageRequest extends BaseRequest
{
	protected $queueName;
	protected $sendMessageRequestItems;

	// boolean, whether the message body will be encoded in base64
	protected $base64;

	public function __construct(array $sendMessageRequestItems, $base64 = true)
	{
		parent::__construct('post', null);

		$this->queueName               = null;
		$this->sendMessageRequestItems = $sendMessageRequestItems;
		$this->base64                  = $base64;
	}

	public function setBase64($base64)
	{
		$this->base64 = $base64;
	}

	public function isBase64()
	{
		return ($this->base64 == true);
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

	public function getSendMessageRequestItems()
	{
		return $this->sendMessageRequestItems;
	}

	public function addSendMessageRequestItem(SendMessageRequestItem $item)
	{
		$this->sendMessageRequestItems[] = $item;
	}

	public function generateBody()
	{
		$xmlWriter = new \XMLWriter;
		$xmlWriter->openMemory();
		$xmlWriter->startDocument("1.0", "UTF-8");
		$xmlWriter->startElementNS(null, "Messages", Constants::MNS_XML_NAMESPACE);
		foreach ($this->sendMessageRequestItems as $item) {
			$item->writeXML($xmlWriter, $this->base64);
		}
		$xmlWriter->endElement();
		$xmlWriter->endDocument();
		return $xmlWriter->outputMemory();
	}

	public function generateQueryString()
	{
		return null;
	}
}


<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Traits;

use Imvkmark\L5Sms\Repositories\Aliyun\Model\Message;

trait MessageIdAndMD5
{
	protected $messageId;
	protected $messageBodyMD5;

	public function getMessageId()
	{
		return $this->messageId;
	}

	public function getMessageBodyMD5()
	{
		return $this->messageBodyMD5;
	}

	public function readMessageIdAndMD5XML(\XMLReader $xmlReader)
	{
		$message              = Message::fromXML($xmlReader, true);
		$this->messageId      = $message->getMessageId();
		$this->messageBodyMD5 = $message->getMessageBodyMD5();
	}
}



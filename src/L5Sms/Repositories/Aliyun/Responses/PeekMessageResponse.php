<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Responses;

use Imvkmark\L5Sms\Repositories\Aliyun\Common\XMLParser;
use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MessageNotExistException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\QueueNotExistException;
use Imvkmark\L5Sms\Repositories\Aliyun\Traits\MessagePropertiesForPeek;

class PeekMessageResponse extends BaseResponse
{
	use MessagePropertiesForPeek;

	// boolean, whether the message body will be decoded as base64
	private $base64;

	public function __construct($base64 = true)
	{
		$this->base64 = $base64;
	}

	public function setBase64($base64)
	{
		$this->base64 = $base64;
	}

	public function isBase64()
	{
		return ($this->base64 == true);
	}

	public function parseResponse($statusCode, $content)
	{
		$this->statusCode = $statusCode;
		if ($statusCode == 200) {
			$this->succeed = true;
		}
		else {
			$this->parseErrorResponse($statusCode, $content);
		}

		$xmlReader = $this->loadXmlContent($content);

		try {
			$this->readMessagePropertiesForPeekXML($xmlReader, $this->base64);
		} catch (\Exception $e) {
			throw new MnsException($statusCode, $e->getMessage(), $e);
		} catch (\Throwable $t) {
			throw new MnsException($statusCode, $t->getMessage());
		}

	}

	public function parseErrorResponse($statusCode, $content, MnsException $exception = null)
	{
		$this->succeed = false;
		$xmlReader     = $this->loadXmlContent($content);

		try {
			$result = XMLParser::parseNormalError($xmlReader);
			if ($result['Code'] == Constants::QUEUE_NOT_EXIST) {
				throw new QueueNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
			}
			if ($result['Code'] == Constants::MESSAGE_NOT_EXIST) {
				throw new MessageNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
			}
			throw new MnsException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
		} catch (\Exception $e) {
			if ($exception != null) {
				throw $exception;
			}
			elseif ($e instanceof MnsException) {
				throw $e;
			}
			else {
				throw new MnsException($statusCode, $e->getMessage());
			}
		} catch (\Throwable $t) {
			throw new MnsException($statusCode, $t->getMessage());
		}
	}
}



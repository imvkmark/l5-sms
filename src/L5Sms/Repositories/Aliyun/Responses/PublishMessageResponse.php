<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Responses;

use Imvkmark\L5Sms\Repositories\Aliyun\Common\XMLParser;
use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\InvalidArgumentException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MalformedXMLException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\TopicNotExistException;
use Imvkmark\L5Sms\Repositories\Aliyun\Traits\MessageIdAndMD5;

class PublishMessageResponse extends BaseResponse
{
	use MessageIdAndMD5;

	public function __construct()
	{
	}

	public function parseResponse($statusCode, $content)
	{
		$this->statusCode = $statusCode;
		if ($statusCode == 201) {
			$this->succeed = true;
		}
		else {
			$this->parseErrorResponse($statusCode, $content);
		}

		$xmlReader = $this->loadXmlContent($content);
		try {
			$this->readMessageIdAndMD5XML($xmlReader);
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
			if ($result['Code'] == Constants::TOPIC_NOT_EXIST) {
				throw new TopicNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
			}
			if ($result['Code'] == Constants::INVALID_ARGUMENT) {
				throw new InvalidArgumentException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
			}
			if ($result['Code'] == Constants::MALFORMED_XML) {
				throw new MalformedXMLException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
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



<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Responses;

use Imvkmark\L5Sms\Repositories\Aliyun\Common\XMLParser;
use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\InvalidArgumentException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\SubscriptionAlreadyExistException;

class SubscribeResponse extends BaseResponse
{
	public function parseResponse($statusCode, $content)
	{
		$this->statusCode = $statusCode;
		if ($statusCode == 201 || $statusCode == 204) {
			$this->succeed = true;
		}
		else {
			$this->parseErrorResponse($statusCode, $content);
		}
	}

	public function parseErrorResponse($statusCode, $content, MnsException $exception = null)
	{
		$this->succeed = false;
		$xmlReader     = $this->loadXmlContent($content);
		try {
			$result = XMLParser::parseNormalError($xmlReader);

			if ($result['Code'] == Constants::INVALID_ARGUMENT) {
				throw new InvalidArgumentException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
			}
			if ($result['Code'] == Constants::SUBSCRIPTION_ALREADY_EXIST) {
				throw new SubscriptionAlreadyExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
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



<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Responses;

use Imvkmark\L5Sms\Repositories\Aliyun\Common\XMLParser;
use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\BatchDeleteFailException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\InvalidArgumentException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\QueueNotExistException;
use Imvkmark\L5Sms\Repositories\Aliyun\Exception\ReceiptHandleErrorException;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\DeleteMessageErrorItem;

class BatchDeleteMessageResponse extends BaseResponse
{
	public function __construct()
	{
	}

	public function parseResponse($statusCode, $content)
	{
		$this->statusCode = $statusCode;
		if ($statusCode == 204) {
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
			while ($xmlReader->read()) {
				if ($xmlReader->nodeType == \XMLReader::ELEMENT) {
					switch ($xmlReader->name) {
						case Constants::ERROR:
							$this->parseNormalErrorResponse($xmlReader);
							break;
						default: // case Constants::Messages
							$this->parseBatchDeleteErrorResponse($xmlReader);
							break;
					}
				}
			}
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

	private function parseBatchDeleteErrorResponse($xmlReader)
	{
		$ex = new BatchDeleteFailException($this->statusCode, "BatchDeleteMessage Failed For Some ReceiptHandles");
		while ($xmlReader->read()) {
			if ($xmlReader->nodeType == \XMLReader::ELEMENT && $xmlReader->name == Constants::ERROR) {
				$ex->addDeleteMessageErrorItem(DeleteMessageErrorItem::fromXML($xmlReader));
			}
		}
		throw $ex;
	}

	private function parseNormalErrorResponse($xmlReader)
	{
		$result = XMLParser::parseNormalError($xmlReader);

		if ($result['Code'] == Constants::INVALID_ARGUMENT) {
			throw new InvalidArgumentException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
		}
		if ($result['Code'] == Constants::QUEUE_NOT_EXIST) {
			throw new QueueNotExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
		}
		if ($result['Code'] == Constants::RECEIPT_HANDLE_ERROR) {
			throw new ReceiptHandleErrorException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
		}

		throw new MnsException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
	}
}



<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Responses;

use Imvkmark\L5Sms\Repositories\Aliyun\Exception\MnsException;

abstract class BaseResponse
{
	protected $succeed;
	protected $statusCode;

	abstract public function parseResponse($statusCode, $content);

	public function isSucceed()
	{
		return $this->succeed;
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	protected function loadXmlContent($content)
	{
		$xmlReader = new \XMLReader();
		$isXml     = $xmlReader->XML($content);
		if ($isXml === false) {
			throw new MnsException($statusCode, $content);
		}
		try {
			while ($xmlReader->read()) {
			}
		} catch (\Exception $e) {
			throw new MnsException($statusCode, $content);
		}
		$xmlReader->XML($content);
		return $xmlReader;
	}
}



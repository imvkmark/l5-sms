<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

use Imvkmark\L5Sms\Repositories\Aliyun\Constants;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\AccountAttributes;

class SetAccountAttributesRequest extends BaseRequest
{
	private $attributes;

	public function __construct(AccountAttributes $attributes = null)
	{
		parent::__construct('put', '/?accountmeta=true');

		if ($attributes == null) {
			$attributes = new AccountAttributes;
		}

		$this->attributes = $attributes;
	}

	public function getAccountAttributes()
	{
		return $this->attributes;
	}

	public function generateBody()
	{
		$xmlWriter = new \XMLWriter;
		$xmlWriter->openMemory();
		$xmlWriter->startDocument("1.0", "UTF-8");
		$xmlWriter->startElementNS(null, "Account", Constants::MNS_XML_NAMESPACE);
		$this->attributes->writeXML($xmlWriter);
		$xmlWriter->endElement();
		$xmlWriter->endDocument();
		return $xmlWriter->outputMemory();
	}

	public function generateQueryString()
	{
		return null;
	}
}


<?php namespace Imvkmark\L5Sms\Repositories\Aliyun\Requests;

class GetAccountAttributesRequest extends BaseRequest
{
	public function __construct()
	{
		parent::__construct('get', '/?accountmeta=true');
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


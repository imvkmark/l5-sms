<?php namespace Imvkmark\L5Sms\Repositories\Aliyun;

use Imvkmark\L5Sms\Repositories\Aliyun\Http\HttpClient;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\SubscriptionAttributes;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\TopicAttributes;
use Imvkmark\L5Sms\Repositories\Aliyun\Model\UpdateSubscriptionAttributes;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\GetSubscriptionAttributeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\GetTopicAttributeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\ListSubscriptionRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\PublishMessageRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\SetSubscriptionAttributeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\SetTopicAttributeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\SubscribeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Requests\UnsubscribeRequest;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\GetSubscriptionAttributeResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\GetTopicAttributeResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\ListSubscriptionResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\PublishMessageResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\SetSubscriptionAttributeResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\SetTopicAttributeResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\SubscribeResponse;
use Imvkmark\L5Sms\Repositories\Aliyun\Responses\UnsubscribeResponse;

class Topic
{
	private $topicName;
	private $client;

	public function __construct(HttpClient $client, $topicName)
	{
		$this->client    = $client;
		$this->topicName = $topicName;
	}

	public function getTopicName()
	{
		return $this->topicName;
	}

	public function setAttribute(TopicAttributes $attributes)
	{
		$request  = new SetTopicAttributeRequest($this->topicName, $attributes);
		$response = new SetTopicAttributeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function getAttribute()
	{
		$request  = new GetTopicAttributeRequest($this->topicName);
		$response = new GetTopicAttributeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function generateQueueEndpoint($queueName)
	{
		return "acs:mns:" . $this->client->getRegion() . ":" . $this->client->getAccountId() . ":queues/" . $queueName;
	}

	public function generateMailEndpoint($mailAddress)
	{
		return "mail:directmail:" . $mailAddress;
	}

	public function generateSmsEndpoint($phone = null)
	{
		if ($phone) {
			return "sms:directsms:" . $phone;
		}
		else {
			return "sms:directsms:anonymous";
		}
	}

	public function generateBatchSmsEndpoint()
	{
		return "sms:directsms:anonymous";
	}

	public function publishMessage(PublishMessageRequest $request)
	{
		$request->setTopicName($this->topicName);
		$response = new PublishMessageResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function subscribe(SubscriptionAttributes $attributes)
	{
		$attributes->setTopicName($this->topicName);
		$request  = new SubscribeRequest($attributes);
		$response = new SubscribeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function unsubscribe($subscriptionName)
	{
		$request  = new UnsubscribeRequest($this->topicName, $subscriptionName);
		$response = new UnsubscribeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function getSubscriptionAttribute($subscriptionName)
	{
		$request  = new GetSubscriptionAttributeRequest($this->topicName, $subscriptionName);
		$response = new GetSubscriptionAttributeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function setSubscriptionAttribute(UpdateSubscriptionAttributes $attributes)
	{
		$attributes->setTopicName($this->topicName);
		$request  = new SetSubscriptionAttributeRequest($attributes);
		$response = new SetSubscriptionAttributeResponse();
		return $this->client->sendRequest($request, $response);
	}

	public function listSubscription($retNum = null, $prefix = null, $marker = null)
	{
		$request  = new ListSubscriptionRequest($this->topicName, $retNum, $prefix, $marker);
		$response = new ListSubscriptionResponse();
		return $this->client->sendRequest($request, $response);
	}
}



<?php
namespace Flux;

class Client extends Base\Client {

	private $users;
	private $advertiser_offers;
	private $publisher_campaigns;
	private $fulfillments;

	/**
	 * Returns the _status_name
	 * @return string
	 */
	function getStatusName() {
		if ($this->getStatus() == self::CLIENT_STATUS_ACTIVE) {
			return "Active";
		} else if ($this->getStatus() == self::CLIENT_STATUS_INACTIVE) {
			return "Inactive";
		} else if ($this->getStatus() == self::CLIENT_STATUS_DELETED) {
			return "Deleted";
		} else {
			return "Unknown Status";
		}
	}

	/**
	 * Returns the ref_users
	 * @return integer
	 */
	function getUsers() {
		if (is_null($this->users)) {
			$user = new \Flux\User();
			$user->getClient()->setId($this->getId());
			$this->users = $user->queryAllByClient();
		}
		return $this->users;
	}

	/**
	 * Returns the advertiser_offers
	 * @return integer
	 */
	function getAdvertiserOffers() {
		if (is_null($this->advertiser_offers)) {
			$offer = new \Flux\Offer();
			$offer->getClient()->setId($this->getId());
			$this->advertiser_offers = $offer->queryAllByClient();
		}
		return $this->advertiser_offers;
	}

	/**
	 * Returns the publisher_campaigns
	 * @return integer
	 */
	function getPublisherCampaigns() {
		if (is_null($this->publisher_campaigns)) {
			$campaign = new \Flux\Campaign();
			$campaign->getClient()->setId($this->getId());
			$this->publisher_campaigns = $campaign->queryAllByClient();
		}
		return $this->publisher_campaigns;
	}

	/**
	 * Returns the client_exports
	 * @return array
	 */
	function getFulfillments() {
		if (is_null($this->fulfillments)) {
			$fulfillment = new \Flux\Fulfillment();
			$fulfillment->setClientIdArray(array($this->getId()));
			$fulfillment->setSort('name');
			$fulfillment->setSord('asc');
			$fulfillment->setIgnorePagination(true);
			$this->fulfillments = $fulfillment->queryAll();
		}
		return $this->fulfillments;
	}
	
	/**
	 * Returns the reporting api class instance
	 * @return \Flux\Network\BaseNetwork
	 */
	function getReportingApiClassInstance() {
		$api_class_name = "\\" . $this->getReportingApiClass();
		if (class_exists($api_class_name)) {
			$api_class = new $api_class_name();
		} else {
			$api_class = new \Flux\Network\BaseNetwork();
		}
		$api_class->setClient($this->getId());
		$api_class->setApiToken($this->getReportingApiToken());
		$api_class->setApiUrl($this->getReportingApiUrl());
		$api_class->setNetworkUsername($this->getNetworkUsername());
		$api_class->setNetworkPassword($this->getNetworkPassword());
		return $api_class;
	}

	// +------------------------------------------------------------------------+
	// | HELPER METHODS															|
	// +------------------------------------------------------------------------+
	/**
	 * Returns the offer based on the criteria
	 * @return Flux\Offer
	 */
	function queryAll(array $criteria = array(), array $fields = array(), $hydrate = true, $timeout = 30000) {
		if (trim($this->getName()) != '') {
			$criteria['name'] = new \MongoRegex("/" . $this->getName() . "/i");
		}
		return parent::queryAll($criteria, $fields, $hydrate, $timeout);
	}

	/**
	 * Ensures that the mongo indexes are set (should be called once)
	 * @return boolean
	 */
	public static function ensureIndexes() {
		$client = new self();
		$client->getCollection()->ensureIndex(array('status' => 1, 'type' => 1), array('background' => true));
		return true;
	}

}

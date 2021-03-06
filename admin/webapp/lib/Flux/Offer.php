<?php
namespace Flux;

class Offer extends Base\Offer {
	
	protected $daily_clicks;
	protected $daily_conversions;
	protected $optgroup;

	private $campaigns;
	private $splits;
	private $offer_pages;
	private $flow;
	private $offer_map;
	private $publisher_campaigns;
	private $default_campaign;
	
	/* these fields are used when searching */
	private $client_id_array;
	private $vertical_id_array;
	private $status_array;
	
	private static $_offers;

	/**
	 * Returns the optgroup
	 * @return string
	 */
	function getOptgroup() {
		if (is_null($this->optgroup)) {
			$this->optgroup = $this->getVertical()->getVerticalName();
		}
		return $this->optgroup;
	}	
	
	/**
	 * Returns the default campaign
	 * @return \Flux\Campaign
	 */
	function getDefaultCampaign() {
		if (is_null($this->default_campaign)) {
			$this->default_campaign = new \Flux\Campaign();
			if (\MongoId::isValid($this->getDefaultCampaignId())) {
				$this->default_campaign->setId(new \MongoId($this->getDefaultCampaignId()));
				$this->default_campaign->query();
			}
		}
		return $this->default_campaign;
	}
	
	/**
	 * Returns the _status_name
	 * @return string
	 */
	function getStatusName() {
		if ($this->getStatus() == self::OFFER_STATUS_ACTIVE) {
			return "Active";
		} else if ($this->getStatus() == self::OFFER_STATUS_INACTIVE) {
			return "Inactive";
		} else if ($this->getStatus() == self::OFFER_STATUS_DELETED) {
			return "Deleted";
		} else {
			return "Unknown Status";
		}
	}

	/**
	 * Returns the _redirect_type_name
	 * @return string
	 */
	function getRedirectTypeName() {
		if ($this->getRedirectType() == self::REDIRECT_TYPE_HOSTED) {
			return "Hosted";
		} else if ($this->getStatus() == self::REDIRECT_TYPE_REDIRECT) {
			return "Redirect";
		} else if ($this->getStatus() == self::REDIRECT_TYPE_POST) {
			return "Post";
		} else {
			return "Unknown Type";
		}
	}
	
	/**
	 * Returns the redirect_url
	 * @return string
	 */
	function getFormattedRedirectUrl() {
		if ($this->getRedirectType() == self::REDIRECT_TYPE_HOSTED) {
			if (strpos($this->getDomainName(), 'http') === 0) {
				if (strpos($this->getDomainName(), '#_id#') === false) {
					return $this->getDomainName() . '?_id=#_id#';   
				} else {
					return $this->getDomainName();
				}
			} else {
				$ret_val = 'http://' . $this->getDomainName() . '/';
				if ($this->getFolderName() != '') {
					$ret_val .= $this->getFolderName() . '/';
				}
				$ret_val .= '?_id=#_id#';
				return $ret_val;
			}
		} else {
			return $this->getRedirectUrl();
		}
	}
	
	/**
	 * Returns the client_id_array
	 * @return array
	 */
	function getClientIdArray() {
		if (is_null($this->client_id_array)) {
			$this->client_id_array = array();
		}
		return $this->client_id_array;
	}
	
	/**
	 * Sets the client_id_array
	 * @var array
	 */
	function setClientIdArray($arg0) {
		if (is_array($arg0)) {
			$this->client_id_array = $arg0;
			array_walk($this->client_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->client_id_array = explode(",", $arg0);
				array_walk($this->client_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			} else {
				$this->client_id_array = array($arg0);
				array_walk($this->client_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			}
		}
		return $this;
	}
	
	/**
	 * Returns the vertical_id_array
	 * @return array
	 */
	function getVerticalIdArray() {
		if (is_null($this->vertical_id_array)) {
			$this->vertical_id_array = array();
		}
		return $this->vertical_id_array;
	}
	
	/**
	 * Sets the vertical_id_array
	 * @var array
	 */
	function setVerticalIdArray($arg0) {
		if (is_array($arg0)) {
			$this->vertical_id_array = $arg0;
			array_walk($this->vertical_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->vertical_id_array = explode(",", $arg0);
				array_walk($this->vertical_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			} else {
				$this->vertical_id_array = array($arg0);
				array_walk($this->vertical_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			}
		}
		return $this;
	}
	
	/**
	 * Returns the status_array
	 * @return array
	 */
	function getStatusArray() {
		if (is_null($this->status_array)) {
			$this->status_array = array();
		}
		return $this->status_array;
	}
	
	/**
	 * Sets the status_array
	 * @var array
	 */
	function setStatusArray($arg0) {
		if (is_array($arg0)) {
			$this->status_array = $arg0;
			array_walk($this->status_array, function(&$val) { $val = (int)$val; });
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->status_array = explode(",", $arg0);
				array_walk($this->status_array, function(&$val) { $val = (int)$val; });
			} else {
				$this->status_array = array((int)$arg0);
			}
		}
		return $this;
	}
	
	/**
	 * Returns the offer_pages
	 * @return array
	 */
	function getOfferPages() {
		if (is_null($this->offer_pages)) {
			$offer_page = new \Flux\OfferPage();
			$offer_page->setOfferIdArray(array($this->getId()));
			$offer_page->setIgnorePagination(true);
			$this->offer_pages = $offer_page->queryAll();
		}
		return $this->offer_pages;
	}
	
	/**
	 * Returns the splits
	 * @return array
	 */
	function getSplits() {
		if (is_null($this->splits)) {
			$split = new \Flux\Split();
			$split->setOfferIdArray(array($this->getId()));
			$split->setIgnorePagination(true);
			$this->splits = $split->queryAll();
		}
		return $this->splits;
	}
	
	/**
	 * Returns the splits
	 * @return array
	 */
	function getCampaigns() {
		if (is_null($this->campaigns)) {
			$campaign = new \Flux\Campaign();
			$campaign->setOfferIdArray(array($this->getId()));
			$campaign->setIgnorePagination(true);
			$this->campaigns = $campaign->queryAllByOffer();
		}
		return $this->campaigns;
	}

	/**
	 * Returns the publisher_campaigns
	 * @return array
	 */
	function getPublisherCampaigns() {
		return $this->getCampaigns();
	}

	// +------------------------------------------------------------------------+
	// | HELPER METHODS															|
	// +------------------------------------------------------------------------+
	/**
	 * Creates a new offer
	 * @return Flux\Offer
	 */
	function insert() {
		$insert_id = parent::insert();
		$this->setId($insert_id);
		// Create the first campaign for this offer
		if (\MongoId::isValid($insert_id)) {
			/* @var $campaign \Flux\Campaign */
			$campaign = new \Flux\Campaign();
			$campaign->setOffer($insert_id);
			$campaign->setClient($this->getClient()->getId());
			$campaign->setDescription('Default campaign for ' . $this->getName());
			// Find the main landing page
			$landing_pages = $this->getLandingPages();
			if (count($landing_pages) > 0) {
				$landing_page = array_shift($landing_pages);
				if (!is_null($landing_page)) {
				   $campaign->setRedirectLink($landing_page->getUrl() . '?_id=#_id#');
				}
			}
			$campaign_id = $campaign->insert();
			
			// Assign this new campaign to this offer
			$this->setDefaultCampaignId($campaign_id);
			$this->update();
		}
		return $insert_id;
	}
	
	/**
	 * Returns the offer based on the criteria
	 * @return Flux\Offer
	 */
	function queryAll(array $criteria = array(), array $fields = array(), $hydrate = true, $timeout = 30000) {
		if ($this->getFolderName() != '') {
			$criteria['folder_name'] = $this->getFolderName();
		}
		if ($this->getDomainName() != '') {
			$criteria['domain_name'] = $this->getDomainName();
		}
		if (\MongoId::isValid($this->getClient()->getId())) {
			$criteria['client._id'] = $this->getId();
		}
		if (count($this->getVerticalIdArray()) > 0) {
			$criteria['vertical._id'] = array('$in' => $this->getVerticalIdArray());
		}
		if (trim($this->getKeywords()) != '') {
			$criteria['$or'] = array(
				array('name' => new \MongoRegex("/" . $this->getKeywords() . "/i")),
				array('vertical.name' =>  new \MongoRegex("/" . $this->getKeywords() . "/i"))
			);
		}
		if (trim($this->getName()) != '') {
		   $criteria['name'] = new \MongoRegex("/" . $this->getName() . "/i");
		}
		if (count($this->getClientIdArray()) > 0) {
			$criteria['client._id'] = array('$in' => $this->getClientIdArray());
		}
		if (count($this->getStatusArray()) > 0) {
			$criteria['status'] = array('$in' => $this->getStatusArray());
		}
		return parent::queryAll($criteria, $fields, $hydrate, $timeout);
	}

	/**
	 * Finds an offer by the folder name
	 * @return \Flux\Offer
	 */
	function queryByFolderName() {
		return parent::query(array('folder_name' => $this->getFolderName()), false);
	}

	/**
	 * Finds all offers by client
	 * @return Flux\Offer
	 */
	function queryAllByClient() {
		return $this->queryAll(array('client._id' => $this->getId()));
	}

	/**
	 * Finds all offers by client
	 * @return Flux\Offer
	 */
	function queryAllByVerticals() {
		return $this->queryAll(array('vertical._id' => array('$in' => $this->getVerticalIdArray())));
	}

	/**
	 * Flushes the events
	 * @return Flux\Offer
	 */
	function flushEvents() {
		$this->setEvents(array());
		parent::addModifiedColumn('events');
		return $this->update();
	}

	/**
	 * Ensures that the mongo indexes are set (should be called once)
	 * @return boolean
	 */
	public static function ensureIndexes() {
		$offer = new self();
		$offer->getCollection()->ensureIndex(array('client._id' => 1), array('background' => true));
		return true;
	}
}

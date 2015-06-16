<?php
namespace Flux;

class Offer extends Base\Offer {
	
	protected $daily_clicks;
	protected $daily_conversions;
	protected $optgroup;

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
    	        $this->default_campaign->setId($this->getDefaultCampaignId());
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
			array_walk($this->client_id_array, function(&$val) { $val = (int)$val; });
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->client_id_array = explode(",", $arg0);
				array_walk($this->client_id_array, function(&$val) { $val = (int)$val; });
			} else {
				$this->client_id_array = array((int)$arg0);
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
			array_walk($this->vertical_id_array, function(&$val) { $val = (int)$val; });
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->vertical_id_array = explode(",", $arg0);
				array_walk($this->vertical_id_array, function(&$val) { $val = (int)$val; });
			} else {
				$this->vertical_id_array = array((int)$arg0);
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
			$offer_page->setOffer($this->getId());
			$this->offer_pages = $offer_page->queryAll();
		}
		return $this->offer_pages;
	}

	/**
	 * Returns the publisher_campaigns
	 * @return array
	 */
	function getPublisherCampaigns() {
		if (is_null($this->publisher_campaigns)) {
			$campaign = new \Flux\Campaign();
			$campaign->setOffer($this->getId());
			$this->publisher_campaigns = $campaign->queryAllByOffer();
		}
		return $this->publisher_campaigns;
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
	    if ($insert_id > 0) {
	        /* @var $campaign \Flux\Campaign */
	        $campaign = new \Flux\Campaign();
	        $campaign->setOffer($insert_id);
	        $campaign->setClient($this->getClient()->getClientId());
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
	function queryAll(array $criteria = array(), $hydrate = true) {
		if ($this->getFolderName() != '') {
			$criteria['folder_name'] = $this->getFolderName();
		}
		if ($this->getDomainName() != '') {
			$criteria['domain_name'] = $this->getDomainName();
		}
		if ($this->getClient()->getClientId() > 0) {
			$criteria['client.client_id'] = $this->getClientId();
		}
		if (count($this->getVerticalIdArray()) > 0) {
			$criteria['vertical.vertical_id'] = array('$in' => $this->getVerticalIdArray());
		}
		if (trim($this->getKeywords()) != '') {
			$criteria['$or'] = array(
				array('name' => new \MongoRegex("/" . $this->getKeywords() . "/i")),
				array('verticals' =>  new \MongoRegex("/" . $this->getKeywords() . "/i"))
			);
		}
		if (trim($this->getName()) != '') {
		   $criteria['name'] = new \MongoRegex("/" . $this->getName() . "/i");
		}
		if (count($this->getClientIdArray()) > 0) {
			$criteria['client_id'] = array('$in' => $this->getClientIdArray());
		}
		if (count($this->getStatusArray()) > 0) {
			$criteria['status'] = array('$in' => $this->getStatusArray());
		}
		return parent::queryAll($criteria, $hydrate);
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
		return $this->queryAll(array('client_id' => $this->getClientId()));
	}

	/**
	 * Finds all offers by client
	 * @return Flux\Offer
	 */
	function queryAllByVerticals() {
		return $this->queryAll(array('vertical.vertical_id' => array('$in' => $this->getVerticalIdArray())));
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
		$offer->getCollection()->ensureIndex(array('client_id' => 1), array('background' => true));
		return true;
	}
}

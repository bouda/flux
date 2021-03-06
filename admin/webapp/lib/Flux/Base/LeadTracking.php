<?php
namespace Flux\Base;

use Mojavi\Form\MojaviForm;

class LeadTracking extends MojaviForm {
		
	protected $_ip;
	protected $_qs;
	protected $_ref;
	protected $_url;
	protected $_ua;
	protected $_uab;
	protected $_uav;
	protected $_uap;
	
	protected $keywords;
	protected $source_url;
	
	protected $s1;
	protected $s2;
	protected $s3;
	protected $s4;
	protected $s5;
	protected $uid;
	
	protected $offer;
	protected $campaign;
	protected $client;
	
	function __construct() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->getUserAgentBrowser();
			$this->getUserAgentVersion();
			$this->getUserAgentPlatform();
		}
	}
	
	/**
	 * Returns the _ip
	 * @return string
	 */
	function getIp() {
		if (is_null($this->_ip)) {
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$this->_ip = $_SERVER['REMOTE_ADDR'];
			} else {
				$this->_ip = "";
			}
		}
		return $this->_ip;
	}
	
	/**
	 * Sets the _ip
	 * @var string
	 */
	function setIp($arg0) {
		$this->_ip = $arg0;
		$this->addModifiedColumn("_ip");
		return $this;
	}
	
	/**
	 * Returns the source_url
	 * @return string
	 */
	function getSourceUrl() {
		if (is_null($this->source_url)) {
			$this->source_url = "";
		}
		return $this->source_url;
	}
	
	/**
	 * Sets the source_url
	 * @var string
	 */
	function setSourceUrl($arg0) {
		$this->source_url = $arg0;
		return $this;
	}
	
	/**
	 * Returns the keywords
	 * @return string
	 */
	function getKeywords() {
		if (is_null($this->keywords)) {
			$this->keywords = "";
		}
		return $this->keywords;
	}
	
	/**
	 * Sets the keywords
	 * @var string
	 */
	function setKeywords($arg0) {
		$this->keywords = $arg0;
		return $this;
	}
	
	/**
	 * Returns the qs
	 * @return string
	 */
	function getQs() {
		if (is_null($this->_qs)) {
			if (isset($_SERVER['QUERY_STRING'])) {
				$this->_qs = $_SERVER['QUERY_STRING'];
			} else {
				$this->_qs = "";
			}
		}
		return $this->_qs;
	}
	
	/**
	 * Sets the qs
	 * @var string
	 */
	function setQs($arg0) {
		$this->_qs = $arg0;
		$this->addModifiedColumn("_qs");
		return $this;
	}
	
	/**
	 * Returns the _ref
	 * @return string
	 */
	function getRef() {
		if (is_null($this->_ref)) {
			if (isset($_SERVER['HTTP_REFERER'])) {
				$this->_ref = $_SERVER['HTTP_REFERER'];
			} else {
				$this->_ref = "";
			}
		}
		return $this->_ref;
	}
	
	/**
	 * Sets the _ref
	 * @var string
	 */
	function setRef($arg0) {
		$this->_ref = $arg0;
		$this->parseReferrer();
		$this->addModifiedColumn("_ref");
		return $this;
	}
	
	/**
	 * Parses the ref for the keywords
	 * @return void
	 */
	function parseReferrer($referrer = null) {
		if (is_null($referrer)) {
			$referrer = $this->getRef();
		}
		if (trim($this->getKeywords()) == '' && trim($referrer) != '') {
			$host = parse_url($referrer, PHP_URL_HOST);
			$path = parse_url($referrer, PHP_URL_PATH);
			$query = parse_url($referrer, PHP_URL_QUERY);
			$params = array();
			parse_str($query, $params);
			if ($host == 'googleads.g.doubleclick.net') {
				if (isset($params['url']) && trim($params['url']) != '') {
					$this->parseReferrer(urldecode($params['url']));
				} else if (isset($params['ref']) && trim($params['ref']) != '') {
					$this->parseReferrer(urldecode($params['ref']));
				}
			} else {
				if (isset($params['q']) && trim($params['q']) != '') {
					$this->setKeywords($params['q']);
				} else if (isset($params['query']) && trim($params['query']) != '') {
					$this->setKeywords($params['query']);
				} else if (isset($params['dqi']) && trim($params['dqi']) != '') {
					$this->setKeywords($params['dqi']);
				} else if (isset($params['utm_term']) && trim($params['utm_term']) != '') {
					$this->setKeywords($params['utm_term']);
				} else if (isset($params['kw']) && trim($params['kw']) != '') {
					$this->setKeywords($params['kw']);
				} else if (isset($params['pq']) && trim($params['pq']) != '') {
					$this->setKeywords($params['pq']);
				} else if (isset($params['p']) && trim($params['p']) != '') {
					$this->setKeywords($params['p']);
				} else if (isset($params['term']) && trim($params['term']) != '') {
					$this->setKeywords($params['term']);
				}
				
				if (isset($params['url']) && trim($params['url']) != '') {
					$this->setSourceUrl($params['url']);
				} else if (isset($params['rurl']) && trim($params['rurl']) != '') {
					$this->setSourceUrl($params['rurl']);
				} else {
					$this->setSourceUrl($this->getRef());
				}
			}
		}
	}
	
	/**
	 * Returns the _url
	 * @return string
	 */
	function getUrl() {
		if (is_null($this->_url)) {
			if (isset($_SERVER['REQUEST_URI'])) {
				$this->_url = $_SERVER['REQUEST_URI'];
			} else {
				$this->_url = "";
			}
		}
		return $this->_url;
	}
	
	/**
	 * Sets the _url
	 * @var string
	 */
	function setUrl($arg0) {
		$this->_url = $arg0;
		$this->addModifiedColumn("_url");
		return $this;
	}
	
	/**
	 * Returns the _ua
	 * @return string
	 */
	function getUa() {
		if (is_null($this->_ua)) {
			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$this->_ua = $_SERVER['HTTP_USER_AGENT'];
			} else {
				$this->_ua = "";
			}
		}
		return $this->_ua;
	}
	
	/**
	 * Sets the _ua
	 * @var string
	 */
	function setUa($arg0) {
		$this->_ua = $arg0;
		$this->addModifiedColumn("_ua");
		return $this;
	}
	
	
	
	/**
	 * Returns the _uab
	 * @return string
	 */
	function getUab() {
		if (is_null($this->_uab)) {
			$this->_uab = ''; //self::getUserAgentInfo()->getData()->browser;
		}
		return $this->_uab;
	}
	
	/**
	 * Sets the _uab
	 * @var string
	 */
	function setUab($arg0) {
		$this->_uab = $arg0;
		$this->addModifiedColumn("_uab");
		return $this;
	}
	
	/**
	 * Returns the _uav
	 * @return string
	 */
	function getUav() {
		if (is_null($this->_uav)) {
			$this->_uav = ''; //self::getUserAgentInfo()->getData()->version;
		}
		return $this->_uav;
	}
	
	/**
	 * Sets the _uav
	 * @var string
	 */
	function setUav($arg0) {
		$this->_uav = $arg0;
		$this->addModifiedColumn("_uav");
		return $this;
	}
	
	/**
	 * Returns the _uap
	 * @return string
	 */
	function getUap() {
		if (is_null($this->_uap)) {
			$this->_uap = ''; //self::getUserAgentInfo()->getData()->platform;
		}
		return $this->_uap;
	}
	
	/**
	 * Sets the _uap
	 * @var string
	 */
	function setUap($arg0) {
		$this->_uap = $arg0;
		$this->addModifiedColumn("_uap");
		return $this;
	}
	
	/**
	 * Returns the s1
	 * @return string
	 */
	function getS1() {
		if (is_null($this->s1)) {
			$this->s1 = "";
		}
		return $this->s1;
	}
	
	/**
	 * Sets the s1
	 * @var string
	 */
	function setS1($arg0) {
		$this->s1 = $arg0;
		$this->addModifiedColumn("s1");
		return $this;
	}
	
	/**
	 * Returns the s2
	 * @return string
	 */
	function getS2() {
		if (is_null($this->s2)) {
			$this->s2 = "";
		}
		return $this->s2;
	}
	
	/**
	 * Sets the s2
	 * @var string
	 */
	function setS2($arg0) {
		$this->s2 = $arg0;
		$this->addModifiedColumn("s2");
		return $this;
	}
	
	/**
	 * Returns the s3
	 * @return string
	 */
	function getS3() {
		if (is_null($this->s3)) {
			$this->s3 = "";
		}
		return $this->s3;
	}
	
	/**
	 * Sets the s3
	 * @var string
	 */
	function setS3($arg0) {
		$this->s3 = $arg0;
		$this->addModifiedColumn("s3");
		return $this;
	}
	
	/**
	 * Returns the s4
	 * @return string
	 */
	function getS4() {
		if (is_null($this->s4)) {
			$this->s4 = "";
		}
		return $this->s4;
	}
	
	/**
	 * Sets the s4
	 * @var string
	 */
	function setS4($arg0) {
		$this->s4 = $arg0;
		$this->addModifiedColumn("s4");
		return $this;
	}
	
	/**
	 * Returns the s5
	 * @return string
	 */
	function getS5() {
		if (is_null($this->s5)) {
			$this->s5 = "";
		}
		return $this->s5;
	}
	
	/**
	 * Sets the s5
	 * @var string
	 */
	function setS5($arg0) {
		$this->s5 = $arg0;
		$this->addModifiedColumn("s5");
		return $this;
	}	
	
	/**
	 * Returns the uid
	 * @return string
	 */
	function getUid() {
		if (is_null($this->uid)) {
			$this->uid = "";
		}
		return $this->uid;
	}
	
	/**
	 * Sets the uid
	 * @var string
	 */
	function setUid($arg0) {
		$this->uid = $arg0;
		$this->addModifiedColumn("uid");
		return $this;
	}
	
	/**
	 * Returns the _uab
	 * @return string
	 */
	function getUserAgentBrowser() {
		return $this->getUab();
	}
	
	/**
	 * Returns the _uav
	 * @return string
	 */
	function getUserAgentVersion() {
		return $this->getUav();
	}
	
	/**
	 * Returns the _uap
	 * @return string
	 */
	function getUserAgentPlatform() {
		return $this->getUap();
	}
	
	/**
	 * Returns the User Agent of the user
	 * @return string
	 */
	public static function getUserAgent() {
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			return $_SERVER['HTTP_USER_AGENT'];
		}
		return '';
	}
	
	/**
	 * Returns the user agent information
	 * @param string $user_agent
	 * @return \Crossjoin\Browscap\Browscap
	 */
	public static function getUserAgentInfo() {
		//$bc = new \Crossjoin\Browscap\Browscap();
		// Get information about the current browser's user agent
		//$current_browser = $bc->getBrowser(self::getUserAgent());
		$current_browser = self::getUserAgent();
		return $current_browser;
	}
	
	/**
	 * Sets the _o
	 * $param array
	 */
	function setO($arg0) {
		return $this->setOffer($arg0);
	}
	
	/**
	 * Returns the _o
	 * @return array
	 */
	function getO() {
		return $this->getOffer();
	}
	
	/**
	 * Sets the _c
	 * $param array
	 */
	function setC($arg0) {
		$this->setClient($arg0);
		return $this;
	}
	
	/**
	 * Returns the _c
	 * @return array
	 */
	function getC() {
		return $this->getClient();;
	}
	
	/**
	 * Returns the _ck
	 * @return array
	 */
	function getCk() {
		return $this->getCampaign();
	}
	
	/**
	 * Returns the _ck
	 * @param $arg0 array
	 */
	function setCk($arg0) {
		return $this->setCampaign($arg0);
	}
	
	/**
	 * Returns the this
	 * @return \Flux\Link\Campaign
	 */
	function getCampaign() {
		if (is_null($this->campaign)) {
			$this->campaign = new \Flux\Link\Campaign();
		}
		return $this->campaign;
	}
	
	/**
	 * Sets the this
	 * @var integer|array
	 */
	function setCampaign($arg0) {
		if (is_array($arg0)) {
			$campaign = $this->getCampaign();
			$campaign->populate($arg0);
			if (\MongoId::isValid($campaign->getId()) && $campaign->getName() == "") {
				$campaign->setCampaignName($campaign->getCampaign()->getKey());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->campaign = $campaign;
			if (\MongoId::isValid($campaign->getId())) {
				$this->setOffer($this->getCampaign()->getCampaign()->getOffer()->getId());
				$this->setClient($this->getCampaign()->getCampaign()->getClient()->getId());
			}
		} else if (is_string($arg0) && \MongoId::isValid($arg0)) {
			$campaign = $this->getCampaign();
			$campaign->setCampaignId($arg0);
			if (\MongoId::isValid($campaign->getId()) && $campaign->getName() == "") {
				$campaign->setCampaignName($campaign->getCampaign()->getKey());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->campaign = $campaign;
			if (\MongoId::isValid($campaign->getId())) {
				$this->setOffer($this->getCampaign()->getCampaign()->getOffer()->getId());
				$this->setClient($this->getCampaign()->getCampaign()->getClient()->getId());
			}
		} else if ($arg0 instanceof \MongoId) {
			$campaign = $this->getCampaign();
			$campaign->setCampaignId($arg0);
			if (\MongoId::isValid($campaign->getId()) && $campaign->getName() == "") {
				$campaign->setCampaignName($campaign->getCampaign()->getKey());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->campaign = $campaign;
			if (\MongoId::isValid($campaign->getId())) {
				$this->setOffer($this->getCampaign()->getCampaign()->getOffer()->getId());
				$this->setClient($this->getCampaign()->getCampaign()->getClient()->getId());
			}
		}
		$this->addModifiedColumn('campaign');

		
		return $this;
	}	
	
	/**
	 * Returns the this
	 * @return \Flux\Link\Offer
	 */
	function getOffer() {
		if (is_null($this->offer)) {
			$this->offer = new \Flux\Link\Offer();
		}
		return $this->offer;
	}
	
	/**
	 * Sets the this
	 * @var integer|array
	 */
	function setOffer($arg0) {
		if (is_array($arg0)) {
			$offer = $this->getOffer();
			$offer->populate($arg0);
			if (\MongoId::isValid($offer->getId()) && $offer->getName() == "") {
				$offer->setOfferName($offer->getOffer()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->offer = $offer;
		} else if (is_string($arg0)) {
			$offer = $this->getOffer();
			$offer->setOfferId($arg0);
			if (\MongoId::isValid($offer->getId()) && $offer->getName() == "") {
				$offer->setOfferName($offer->getOffer()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->offer = $offer;
		} else if ($arg0 instanceof \MongoId) {
			$offer = $this->getOffer();
			$offer->setOfferId($arg0);
			if (\MongoId::isValid($offer->getId()) && $offer->getName() == "") {
				$offer->setOfferName($offer->getOffer()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->offer = $offer;
		}
		$this->addModifiedColumn('offer');
		return $this;
	}	
	
	/**
	 * Returns the this
	 * @return \Flux\Link\Client
	 */
	function getClient() {
		if (is_null($this->client)) {
			$this->client = new \Flux\Link\Client();
		}
		return $this->client;
	}
	
	/**
	 * Sets the this
	 * @var integer|array
	 */
	function setClient($arg0) {
		if (is_array($arg0)) {
			$client = $this->getClient();
			$client->populate($arg0);
			if (\MongoId::isValid($client->getId()) && $client->getName() == "") {
				$client->setClientName($client->getClient()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->client = $client;
		} else if (is_string($arg0)) {
			$client = $this->getClient();
			$client->setClientId($arg0);
			if (\MongoId::isValid($client->getId()) && $client->getName() == "") {
				$client->setClientName($client->getClient()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->client = $client;
		} else if ($arg0 instanceof \MongoId) {
			$client = $this->getClient();
			$client->setClientId($arg0);
			if (\MongoId::isValid($client->getId()) && $client->getName() == "") {
				$client->setClientName($client->getClient()->getName());
				\Mojavi\Logging\LoggerManager::debug(__METHOD__ . " :: Populating name from object");
			}
			$this->client = $client;
		}
		$this->addModifiedColumn('client');
		return $this;
	}
}
<?php
/**
 * Pingback.php is a part of the flux project.
 *
 * @link      http://github.com/hiveclick/buxbux for the canonical source repository
 * @copyright Copyright (c) 2010-2020 BuxBux USA Inc. (http://www.bux-bux.com)
 * @license   http://github.com/hiveclick/buxbux/license.txt
 * @author    hobby
 * @created   8/18/16 4:44 PM
 */

namespace Flux\Link;


class Ubot extends BasicLink
{

	private $ubot;

	protected $url;

	/**
	 * Sets the id field
	 */
	function setId($arg0) {
		parent::setId($arg0);
		if (\MongoId::isValid($this->getId()) && $this->getName() == '') {
			$this->setName($this->getUbot()->getName());
		}
		if (\MongoId::isValid($this->getId()) && $this->getUrl() == '') {
			$this->setUrl($this->getUbot()->getUrl());
		}
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		if (is_null($this->url)) {
			$this->url = "";
		}
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		$this->addModifiedColumn("url");
	}

	/**
	 * Returns the ubot
	 * @return \Flux\Ubot
	 */
	function getUbot() {
		if (is_null($this->ubot)) {
			$this->ubot = new \Flux\Ubot();
			$this->ubot->setId($this->getId());
			$this->ubot->query();
		}
		return $this->ubot;
	}
}
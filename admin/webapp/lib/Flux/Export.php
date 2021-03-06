<?php
namespace Flux;

class Export extends Base\Export {
	
	private $export_type_array;
	private $fulfillment_id_array;
	private $split_id_array;
	
	/**
	 * Returns the _status_name
	 * @return string
	 */
	function getStatusName() {
		if ($this->getStatus() == self::EXPORT_STATUS_ACTIVE) {
			return "Active";
		} else if ($this->getStatus() == self::EXPORT_STATUS_INACTIVE) {
			return "Inactive";
		} else if ($this->getStatus() == self::EXPORT_STATUS_DELETED) {
			return "Deleted";
		} else {
			return "Unknown Status";
		}
	}
	
	/**
	 * Returns the _export_type_name
	 * @return string
	 */
	function getExportTypeName() {
		if ($this->getExportType() == self::EXPORT_TYPE_BATCH) {
			return "Batch";
		} else if ($this->getExportType() == self::EXPORT_TYPE_REALTIME) {
			return "Realtime";
		} else if ($this->getExportType() == self::EXPORT_TYPE_EMAIL_BATCH) {
			return "Email Batch";
		} else if ($this->getExportType() == self::EXPORT_TYPE_EMAIL_REALTIME) {
			return "Email Realtime";
		} else if ($this->getExportType() == self::EXPORT_TYPE_TEST) {
			return "Test";
		} else {
			return "Unknown Type";
		}
	}
	
	/**
	 * Returns the export_type_array
	 * @return array
	 */
	function getExportTypeArray() {
		if (is_null($this->export_type_array)) {
			$this->export_type_array = array();
		}
		return $this->export_type_array;
	}
	
	/**
	 * Sets the export_type_array
	 * @var array
	 */
	function setExportTypeArray($arg0) {
		if (is_array($arg0)) {
			$this->export_type_array = $arg0;
			array_walk($this->export_type_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->export_type_array = explode(",", $arg0);
				array_walk($this->export_type_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			} else {
				$this->export_type_array = array((int)$arg0);
			}
		}
		return $this;
	}
	
	/**
	 * Returns the fulfillment_id_array
	 * @return array
	 */
	function getFulfillmentIdArray() {
		if (is_null($this->fulfillment_id_array)) {
			$this->fulfillment_id_array = array();
		}
		return $this->fulfillment_id_array;
	}
	
	/**
	 * Sets the fulfillment_id_array
	 * @var array
	 */
	function setFulfillmentIdArray($arg0) {
		if (is_array($arg0)) {
			$this->fulfillment_id_array = $arg0;
			array_walk($this->fulfillment_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->fulfillment_id_array = explode(",", $arg0);
				array_walk($this->fulfillment_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			} else {
				$this->fulfillment_id_array = array($arg0);
				array_walk($this->fulfillment_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			}
		}
		return $this;
	}
	
	/**
	 * Returns the split_id_array
	 * @return array
	 */
	function getSplitIdArray() {
		if (is_null($this->split_id_array)) {
			$this->split_id_array = array();
		}
		return $this->split_id_array;
	}
	
	/**
	 * Sets the split_id_array
	 * @var array
	 */
	function setSplitIdArray($arg0) {
		if (is_array($arg0)) {
			$this->split_id_array = $arg0;
			array_walk($this->split_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
		} else if (is_string($arg0)) {
			if (strpos($arg0, ',') !== false) {
				$this->split_id_array = explode(",", $arg0);
				array_walk($this->split_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			} else {
				$this->split_id_array = array($arg0);
				array_walk($this->split_id_array, function(&$val) { if (\MongoId::isValid($val) && !($val instanceof \MongoId)) { $val = new \MongoId($val); }});
			}
		}
		return $this;
	}
	
	/**
	 * Updates the log from the file on the file system
	 * @return boolean
	 */
	function updateLog() {
		if (file_exists($this->getLogFilename())) {
			// anything over 1MB only grab a tail of
			if (filesize($this->getLogFilename()) > 1073741824) {
				$cmd = 'tail -n100 ' . $this->getLogFilename();
				$log_file_contents = trim(shell_exec($cmd));
				$log_file_contents = \Mojavi\Util\StringTools::consoleToHtmlColor($log_file_contents);
				$log_file_contents = '<span style="color:white;background-color:black;">-- Only showing the last 100 lines --<br />&nbsp;</span>' . $log_file_contents;
				$this->setProcessingLog(nl2br($log_file_contents));
				$this->update();
				return true;
			} else {
				$log_file_contents = file_get_contents($this->getLogFilename());
				$log_file_contents = \Mojavi\Util\StringTools::consoleToHtmlColor($log_file_contents);
				$this->setProcessingLog($log_file_contents);
				$this->update();
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Queues a lead based on the client export mapping
	 * @return integer
	 */
	function queueLead($lead) {
		$params = array();
		/* @var $mapping \Flux\FulfillmentMap */
		foreach ($this->getFulfillment()->getFulfillment()->getMapping() as $mapping) {
			$value = $mapping->getMappedValue($lead);
			if (trim($mapping->getDataField()->getKeyName()) != '') {
				$params[$mapping->getDataField()->getKeyName()] = $value;
			}
		}
				
		/* @var $export_queue_item \Flux\ExportQueue */
		$export_queue_item = new \Flux\ExportQueue();
		$export_queue_item->setExport($this->getId());
		$export_queue_item->setLead($lead->getId());
		$export_queue_item->setQs($params);
		$export_queue_item->setUrl($this->getFulfillment()->getFulfillment()->getPostUrl());
		$export_queue_item->setIsError(false);
						
		$insert_id = $export_queue_item->insert();
		
		\Mojavi\Logging\LoggerManager::error(__METHOD__ . " :: " . "Export queue id: " . $insert_id);
		
		return $insert_id;
	}

	/**
	 * Queries for a list of exports by client id
	 * @return \Flux\Export
	 */
	function queryAll(array $criteria = array(), array $fields = array(), $hydrate = true, $timeout = 30000) {
		if (count($this->getFulfillmentIdArray()) > 0) {
			$criteria['fulfillment._id'] = array('$in' => $this->getFulfillmentIdArray());
		}
		if (count($this->getSplitIdArray()) > 0) {
			$criteria['split._id'] = array('$in' => $this->getSplitIdArray());
		}
		if (count($this->getExportTypeArray()) > 0) {
			$criteria['export_type'] = array('$in' => $this->getExportTypeArray());
		}
		if (trim($this->getName()) != '') {
			$criteria['name'] = new \MongoRegex("/" . $this->getName() . "/i");
		}
		return parent::queryAll($criteria, $fields, $hydrate, $timeout);
	}
	
	/**
	 * Queries for a list of exports by client id
	 * @return \Flux\Export
	 */
	function queryBySplitAndFulfillment(array $criteria = array()) {
		if (\MongoId::isValid($this->getFulfillment()->getId())) {
			$criteria['fulfillment.fulfillment_id'] = $this->getFulfillment()->getId();
		}
		if (\MongoId::isValid($this->getSplit()->getId())) {
			$criteria['split.split_id'] = $this->getSplit()->getId();
		}
		$criteria['is_complete'] = false;
		$criteria['is_running'] = false;
		return parent::query($criteria, false);
	}
	
	/**
	 * Ensures that the mongo indexes are set (should be called once)
	 * @return boolean
	 */
	public static function ensureIndexes() {
		$export = new self();
		$export->getCollection()->ensureIndex(array('fulfillment._id' => 1), array('background' => true));
		$export->getCollection()->ensureIndex(array('split._id' => 1), array('background' => true));
		$export->getCollection()->ensureIndex(array('is_complete' => 1), array('background' => true));
		return true;
	}
}
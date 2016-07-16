<?php
namespace Flux\Daemon;

class Fulfill extends BaseDaemon
{
	public function action() {
		/* @var $lead_split_item \Flux\LeadSplit */
		$lead_split_item = $this->getNextQueueItem();
		if ($lead_split_item instanceof \Flux\LeadSplit) {
			if (!$lead_split_item->getSplit()->getSplit()->getScheduling()->isValid()) {
				$this->log('Cannot Fulfill Queue item ' . $lead_split_item->getId() . ' because the split schedule is not valid', array($this->pid, $lead_split_item->getId()));
				$lead_split_item->setErrorMessage('Fulfillment Schedule is closed');
				for ($i=0;$i<168;$i++) {
					if ($lead_split_item->getSplit()->getSplit()->getScheduling()->isValid(strtotime('now + ' . $i . ' hours'))) {
						$next_attempt_time = new \MongoDate(strtotime('now + ' . $i . ' hours'));
						$lead_split_item->setNextAttemptTime($next_attempt_time);
						break;
					}
				}
				
				$lead_split_item->setLastAttemptTime(new \MongoDate());
				$lead_split_item->setIsProcessing(false);
				$lead_split_item->update();
				return false;
			}
			if (!$lead_split_item->getSplit()->getSplit()->getFulfillImmediately()) {
				$this->log('Cannot Fulfill Queue item ' . $lead_split_item->getId() . ' because the split is not setup for immediate fulfillment', array($this->pid, $lead_split_item->getId()));
				return false;
			}
			
			if ($lead_split_item->getSplit()->getSplit()->getFulfillImmediately() && \MongoId::isValid($lead_split_item->getSplit()->getSplit()->getFulfillDelay())) {
				if ((strtotime('now') - $lead_split_item->getLead()->getLead()->getModified->sec) > ($lead_split_item->getSplit()->getSplit()->getFulfillDelay() * 60)) {
					$this->log('Cannot Fulfill Queue item ' . $lead_split_item->getId() . ' because the delay has not been reached yet', array($this->pid, $lead_split_item->getId()));
					return false;
				}
			}
			
			if ($lead_split_item->getIsFulfilled()) {
				// Create the fulfillment handler
				/* @var $fulfillment \Flux\Fulfillment */
				$fulfillment = $lead_split_item->getSplit()->getSplit()->getFulfillment()->getFulfillment();
					
				/* @var $lead_split_attempt \Flux\LeadSplitAttempt */
				$lead_split_attempt = new \Flux\LeadSplitAttempt();
				$lead_split_attempt->setLeadSplit($lead_split_item->getId());
				$lead_split_attempt->setFulfillment($fulfillment->getId());
				$lead_split_attempt->setAttemptTime(new \MongoDate());
				
				// The lead has already been fulfilled, so don't allow it to be fulfilled again
				/* @var $lead_split_attempt \Flux\LeadSplitAttempt */
				$lead_split_attempt = new \Flux\LeadSplitAttempt();
				$lead_split_attempt->setLeadSplit($lead_split_item->getId());
				$lead_split_attempt->setFulfillment($fulfillment->getId());
				$lead_split_attempt->setAttemptTime(new \MongoDate());
				$lead_split_attempt->setIsError(false);
				$lead_split_attempt->setResponse('Already Fulfilled');
				$lead_split_item->addAttempt($lead_split_attempt);
				$lead_split_item->setDisposition(\Flux\LeadSplit::DISPOSITION_ALREADY_FULFILLED);
				$lead_split_item->setErrorMessage('Already Fulfilled');
				
				return false;
			}
					
			//
			// We made it past the validations, so attempt to fulfill the lead
			//	
					
			$this->log('Fulfilling Queue item ' . $lead_split_item->getId(), array($this->pid, $lead_split_item->getId()));
					
			// Create the fulfillment handler
			/* @var $fulfillment \Flux\Fulfillment */
			$fulfillment = $lead_split_item->getSplit()->getSplit()->getFulfillment()->getFulfillment();
			 
			/* @var $lead_split_attempt \Flux\LeadSplitAttempt */
			$lead_split_attempt = new \Flux\LeadSplitAttempt();
			$lead_split_attempt->setLeadSplit($lead_split_item->getId());
			$lead_split_attempt->setFulfillment($fulfillment->getId());
			$lead_split_attempt->setAttemptTime(new \MongoDate());
			
			$results = $fulfillment->queueLead($lead_split_attempt);
					 
			/* @var $result \Flux\LeadSplitAttempt */
			foreach ($results as $key => $result) {
				// Save the split queue attempts back to the split queue item
				$lead_split_item->addAttempt($result);
				 
				$lead_split_item->setDebug($result->getRequest());
				$lead_split_item->setLastAttemptTime(new \MongoDate());
				$lead_split_item->setIsProcessing(false);
				 
				if ($result->getIsError()) {
					$lead_split_item->setIsError(true);
					$lead_split_item->setErrorMessage($result->getResponse());
					$lead_split_item->setIsFulfilled(false);
					$lead_split_item->setAttemptCount($lead_split_item->getAttemptCount() + 1);
					$lead_split_item->setNextAttemptTime(new \MongoDate(strtotime('now + 1 hour')));
					$lead_split_item->setDisposition(\Flux\LeadSplit::DISPOSITION_PENDING);
					 
					/* @var $report_lead \Flux\ReportLead */
					$report_lead = new \Flux\ReportLead();
					$lead = $lead_split_item->getLead()->getLead();
					$report_lead->setLead($lead->getId());
					$report_lead->setClient($lead->getTracking()->getClient()->getId());
					$report_lead->setDisposition(\Flux\ReportLead::LEAD_DISPOSITION_DISQUALIFIED);
					$report_lead->setRevenue(0.00);
					$report_lead->setPayout(0.00);
					$report_lead->setReportDate(new \MongoDate());
					$report_lead->insert();
					$this->log('Lead found [' . $lead_split_item->getSplit()->getName() . ']: ' . $lead_split_item->getId() . ', ALREADY FULFILLED', array($this->pid, $lead_split_item->getId()));
				} else {
					$lead_split_item->setIsFulfilled(true);
					$lead_split_item->setIsError(false);
					$lead_split_item->setErrorMessage('');
					$lead_split_item->setDisposition(\Flux\LeadSplit::DISPOSITION_FULFILLED);
					 
					/* @var $lead \Flux\Lead */
					$lead = $lead_split_item->getLead()->getLead();
					
					// Add a fulfilled event to the lead
					if ($fulfillment->getTriggerFulfillmentFlag()) {
						$lead->setValue(\Flux\DataField::DATA_FIELD_EVENT_FULFILLED_NAME, 1);
						$lead->update();
					}
					 
					// Add/Update the lead reporting
					/* @var $report_lead \Flux\ReportLead */
					$report_lead = new \Flux\ReportLead();
					$report_lead->setLead($lead->getId());
					$report_lead->setClient($lead->getTracking()->getClient()->getId());
					$report_lead->setDisposition(\Flux\ReportLead::LEAD_DISPOSITION_ACCEPTED);
					$report_lead->setRevenue($lead_split_attempt->getBounty());
					if ($lead->getTracking()->getCampaign()->getCampaign()->getPayout() > 0) {
						$report_lead->setPayout($lead->getTracking()->getCampaign()->getCampaign()->getPayout());
					} else {
						$report_lead->setPayout($lead->getTracking()->getOffer()->getOffer()->getPayout());
					}
					$report_lead->setReportDate(new \MongoDate());
					$report_lead->setAccepted(true);
					$report_lead->insert();
					$this->log('Lead found [' . $lead_split_item->getSplit()->getName() . ']: ' . $lead_split_item->getId() . ', FULFILLED', array($this->pid, $lead_split_item->getId()));
				}
			}
			 
			$lead_split_item->update();
			
			return true;
		}
		return false;
	}

	/**
	 * Finds the next split to process and returns it
	 * @return \Flux\LeadSplit
	 */
	protected function getNextQueueItem() {
		$lead_split = new \Flux\LeadSplit();
		// Find active splits with no pid, set the pid, and return the split
		$lead_split_item = $lead_split->findAndModify(
			array(
				'next_attempt_time' => array('$lt' => new \MongoDate()),
				'is_processing' => false,
				'is_catch_all' => false,
				'disposition' => \Flux\LeadSplit::DISPOSITION_UNFULFILLED,
				'attempt_count' => array('$lte' => 5)
			),
			array('$set' => array(
				'is_processing' => true
			)),
			null,
			array(
				'new' => true,
				'sort' => array('_id' => 1)
			)
		);
		return $lead_split_item;
	}
}

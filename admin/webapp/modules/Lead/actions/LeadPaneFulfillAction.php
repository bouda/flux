<?php
use Mojavi\Action\BasicAction;
use Mojavi\View\View;
use Mojavi\Request\Request;

use Flux\Offer;
use Flux\Lead;
// +----------------------------------------------------------------------------+
// | This file is part of the Flux package.									  |
// |																			|
// | For the full copyright and license information, please view the LICENSE	|
// | file that was distributed with this source code.						   |
// +----------------------------------------------------------------------------+
class LeadPaneFulfillAction extends BasicAction
{

	// +-----------------------------------------------------------------------+
	// | METHODS															   |
	// +-----------------------------------------------------------------------+

	/**
	 * Execute any application/business logic for this action.
	 *
	 * @return mixed - A string containing the view name associated with this action
	 */
	public function execute ()
	{
		/* @var $offer Flux\Lead */
		$lead = new Lead();
		$lead->populate($_REQUEST);
		$lead->query();
		
		$split = new \Flux\Split();
		$split->setSort('name');
		$split->setSord('ASC');
		$split->setIgnorePagination(true);
		$splits = $split->queryAll();

		$this->getContext()->getRequest()->setAttribute("lead", $lead);
		$this->getContext()->getRequest()->setAttribute("splits", $splits);
		return View::SUCCESS;
	}
}

?>
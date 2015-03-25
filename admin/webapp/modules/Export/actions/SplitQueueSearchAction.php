<?php
use Mojavi\Action\BasicAction;
use Mojavi\View\View;

use Flux\Split;
// +----------------------------------------------------------------------------+
// | This file is part of the Flux package.									  |
// |																			|
// | For the full copyright and license information, please view the LICENSE	|
// | file that was distributed with this source code.						   |
// +----------------------------------------------------------------------------+
class SplitQueueSearchAction extends BasicAction
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
		/* @var $split Flux\Split */
		$split_queue = new \Flux\SplitQueue();
		$split_queue->populate($_REQUEST);
		$split_queue->query();
		
		/* @var $split Flux\Split */
		$split = new \Flux\Split();
		$split->setSort('name');
		$split->setSord('asc');
		$split->setIgnorePagination(true);
		$splits = $split->queryAll();
		
		$offer = new \Flux\Offer();
		$offer->setSort('name');
		$offer->setSord('asc');
		$offer->setIgnorePagination(true);
		$offers = $offer->queryAll();
		
		$this->getContext()->getRequest()->setAttribute("split_queue", $split_queue);
		$this->getContext()->getRequest()->setAttribute("splits", $splits);
		$this->getContext()->getRequest()->setAttribute("offers", $offers);
		 
		return View::SUCCESS;
	}
}

?>
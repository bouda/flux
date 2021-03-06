<?php
use Mojavi\Action\BasicAction;
use Mojavi\View\View;
use Mojavi\Request\Request;

use Flux\FulfillmentMap;
// +----------------------------------------------------------------------------+
// | This file is part of the Flux package.									  |
// |																			|
// | For the full copyright and license information, please view the LICENSE	|
// | file that was distributed with this source code.						   |
// +----------------------------------------------------------------------------+
class FulfillmentPaneMapOptionsModalAction extends BasicAction
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
		/* @var $fulfillment_map Flux\FulfillmentMap */
		$fulfillment_map = new \Flux\FulfillmentMap();
		$fulfillment_map->populate($_REQUEST);
		
		$this->getContext()->getRequest()->setAttribute("fulfillment_map", $fulfillment_map);
		return View::SUCCESS;
	}
}

?>
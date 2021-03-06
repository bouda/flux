<?php
use Mojavi\Action\BasicAction;
use Mojavi\View\View;
use Mojavi\Request\Request;
// +----------------------------------------------------------------------------+
// | This file is part of the Flux package.									  |
// |																			|
// | For the full copyright and license information, please view the LICENSE	|
// | file that was distributed with this source code.						   |
// +----------------------------------------------------------------------------+
class RevenueReportAction extends BasicAction
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
		/* @var $revenue_report Flux\ReportClient */
		$report_client = new \Flux\ReportClient();
		$report_client->setReportDate(new \MongoDate(strtotime(date('m/01/Y'))));
		$report_client->populate($_GET);

		$this->getContext()->getRequest()->setAttribute("revenue_report", $report_client);
		
		return View::SUCCESS;
	}
}

?>
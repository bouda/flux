<?php
use Mojavi\Action\BasicAction;
use Mojavi\View\View;
use Mojavi\Request\Request;

use Flux\Vertical;
use Flux\Offer;
// +----------------------------------------------------------------------------+
// | This file is part of the Flux package.                                      |
// |                                                                            |
// | For the full copyright and license information, please view the LICENSE    |
// | file that was distributed with this source code.                           |
// +----------------------------------------------------------------------------+
class VerticalPaneOffersAction extends BasicAction
{

    // +-----------------------------------------------------------------------+
    // | METHODS                                                               |
    // +-----------------------------------------------------------------------+

    /**
     * Execute any application/business logic for this action.
     *
     * @return mixed - A string containing the view name associated with this action
     */
    public function execute ()
    {
        /* @var $vertical Flux\Vertical */
        $vertical = new Vertical();
        $vertical->populate($_GET);
        $vertical->query();
        
        $offer = new Offer();
        $offer->setVerticals(array($vertical->getId()));
        $offers = $offer->queryAllByVerticals();
        
        $this->getContext()->getRequest()->setAttribute("vertical", $vertical);
        $this->getContext()->getRequest()->setAttribute("offers", $offers);
        return View::SUCCESS;
    }
}

?>
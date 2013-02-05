<?php
/**
 * Sitewards_Giveaway_IndexController
 *
 * Loads and renders the layout for all actions in the Sitewards_Giveaway module
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_IndexController extends Mage_Core_Controller_Front_Action {
	/**
	 * URL: /giveaway/index/index
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}
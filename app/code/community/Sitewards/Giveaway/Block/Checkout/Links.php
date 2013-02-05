<?php
/**
 * Sitewards_Giveaway_Block_Checkout_Links
 *
 * Handling of checkout link in the header
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Checkout_Links extends Mage_Checkout_Block_Links {

	/**
	 * Checks if customer can proceed to checkout and
	 * if so, adds checkout link to the top nav
	 *
	 * @return Sitewards_Giveaway_Block_Checkout_Links
	 */
	public function addCheckoutLink(){
		$oGiveawayHelper = Mage::helper('sitewards_giveaway');
		$bValidCart = $oGiveawayHelper->isCartValidForCheckout();
		if ($bValidCart) {
			return parent::addCheckoutLink();
		} else {
			return $this;
		}
	}
}
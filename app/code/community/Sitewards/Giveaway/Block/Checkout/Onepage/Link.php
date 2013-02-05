<?php
/**
 * Sitewards_Giveaway_Block_Checkout_Onepage_Link
 *
 * Handling of checkout button in the cart
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link {

	/**
	 * Returns if the one page checkout is available
	 *
	 * returns false if the cart is empty, contains only giveaway
	 * products or for other reasons checked in the checkout helper
	 *
	 * @return boolean
	 */
	public function isPossibleOnepageCheckout() {
		$oGiveawayHelper = Mage::helper('sitewards_giveaway');
		$bValidCart = $oGiveawayHelper->isCartValidForCheckout();

		if ( $bValidCart === false ) {
			return false;
		} else {
			return $this->helper('checkout')->canOnepageCheckout();
		}
	}
}
<?php
/**
 * Sitewards_Giveaway_Block_Checkout_Multishipping_Link
 *
 * Handling of "checkout with multiple addresses" link in the cart
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Checkout_Multishipping_Link extends Mage_Checkout_Block_Multishipping_Link {

	/**
	 * Checks if customer can check out with his
	 * cart and if so, displays the checkout link
	 *
	 * @return string
	 */
	public function _toHtml(){
		$oGiveawayHelper = Mage::helper('sitewards_giveaway');
		$bValidCart = $oGiveawayHelper->isCartValidForCheckout();
		if ($bValidCart) {
			return parent::_toHtml();
		} else {
			return '';
		}
	}
}
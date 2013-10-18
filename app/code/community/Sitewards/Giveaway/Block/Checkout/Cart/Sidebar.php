<?php
/**
 * Sitewards_Giveaway_Block_Checkout_Onepage_Cart_Sidebar
 *
 * Handling of checkout button in the cart block on sidebar
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    /**
     * Checks if customer can check out
     * with his cart
     *
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        $oGiveawayHelper = Mage::helper('sitewards_giveaway');
        $bValidCart = $oGiveawayHelper->isCartValidForCheckout();
        if ($bValidCart) {
            return parent::isPossibleOnepageCheckout();
        } else {
            return false;
        }

    }
}

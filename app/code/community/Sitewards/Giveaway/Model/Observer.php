<?php
/**
 * Sitewards_Giveaway_Model_Observer
 *
 * An observer for adding a product to the cart and
 * redirecting to giveaway page if certain criteria are fulfilled
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Model_Observer extends Mage_Core_Model_Observer
{
    /**
     * Checks on adding a product to a cart if certain criteria are fulfilled
     * and redirect to the giveaway select page if so
     *
     * @param Varien_Event_Observer $oObserver
     */
    public function onCheckoutCartAddProductComplete(Varien_Event_Observer $oObserver)
    {
        /** @var $oHelper Sitewards_Giveaway_Helper_Data */
        $oHelper = Mage::helper('sitewards_giveaway');

        if ($oHelper->isExtensionEnabled()
            && $oHelper->isForwardToGiveawaysPageEnabled()
            && $oHelper->canAddGiveawaysToCart() !== false
            && !$oHelper->getGiveawayIdentifierValue($oObserver->getEvent()->getProduct()->getId())
        ) {
            $oObserver->getRequest()->setParam('return_url', Mage::getUrl($oHelper->getGiveawaysPage()));
        }
    }
}

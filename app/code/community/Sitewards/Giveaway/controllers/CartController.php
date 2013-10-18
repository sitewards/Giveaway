<?php
/**
 * Sitewards_Giveaway_CartController
 *
 * Extends the main cart functionality for handling of giveaway products
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class Sitewards_Giveaway_CartController extends Mage_Checkout_CartController
{
    /**
     * Update shopping cart action
     *
     * @type string
     */
    const S_CART_ACTION_UPDATE = 'update_qty';

    /**
     * Key to retrieve shopping cart action
     *
     * @type string
     */
    const S_CART_ACTION_KEY = 'update_cart_action';

    /**
     * Adds products to cart
     * cancels if the product is giveaway and the max amount
     * of giveaway products in cart is already reached
     *
     * @return void
     */
    public function addAction()
    {
        /** @var Sitewards_Giveaway_Helper_Data $oHelper */
        $oHelper = Mage::helper('sitewards_giveaway');

        $oProductQuantityCollection = $oHelper->getAddCartProductInfo($this->getRequest());
        $bCanAddProducts = $oHelper->canAddProducts(
            $oProductQuantityCollection,
            Sitewards_Giveaway_Helper_Data::I_CART_ACTION_ADD
        );
        if ($oHelper->getGiveawayIdentifierValue($oProductQuantityCollection->getFirstItem()->getId())
            && $bCanAddProducts == false
        ) {
            $this->_goBack();
        } else {
            parent::addAction();
        }
    }

    /**
     * Updates info in the cart. Adds a notice if trying to increase
     * the amount of giveaway product over the allowed limit
     */
    public function updatePostAction()
    {
        $aParams = $this->getRequest()->getParams();
        /** @var Sitewards_Giveaway_Helper_Data $oHelper */
        $oHelper = Mage::helper('sitewards_giveaway');
        $aProductInformation = $oHelper->getUpdateCartProductInfo($this->getRequest());
        $bIsCartActionUpdate = ($aParams[self::S_CART_ACTION_KEY] == self::S_CART_ACTION_UPDATE);
        if ($bIsCartActionUpdate && $oHelper->canAddProducts($aProductInformation) == false) {
            $this->_goBack();
        } else {
            parent::updatePostAction();
        }
    }
}

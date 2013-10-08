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
class Sitewards_Giveaway_CartController extends Mage_Checkout_CartController {

	/**
	 * Adds products to cart
	 * cancels if the product is giveaway and the max amount
	 * of giveaway products in cart is already reached
	 *
	 * @return void
	 */
	public function addAction() {

		/** @var Sitewards_Giveaway_Helper_Data $oHelper */
		$oHelper = Mage::helper('sitewards_giveaway');

		$aParams = $this->getRequest()->getParams();

		if (
			$oHelper->isProductGiveaway(Mage::getModel('catalog/product')->load($aParams['product']))
			AND
			$oHelper->canAddGiveawaysToCart() == false
		) {
			$this->_goBack();
		} else {
			parent::addAction();
		}
	}

	/**
	 * Returns an array of all giveaways in the cart after updating the quantities
	 *
	 * @return array keys - product-ids, values - new amount in the cart
	 */
	protected function getGiveawaysInCart(){
		/** @var Sitewards_Giveaway_Helper_Data $oHelper */
		$oHelper = Mage::helper('sitewards_giveaway');

		$oCartItems = $this->_getCart()->getItems();

		$aGiveawaysInCart = $oHelper->getCartGiveawayProductsAmounts();

		foreach ($oCartItems as $iItemIndex => $oItem) {
			/** @var Mage_Sales_Model_Quote_Item $oItem */
			if (isset($aParams['cart'][$iItemIndex])) {
				if ($oHelper->isProductGiveaway(Mage::getModel('catalog/product')->load($oItem->getProductId()))){
					$aGiveawaysInCart[$oItem->getProductId()] = $aParams['cart'][$iItemIndex]['qty'];
				}
			}
		}

		return $aGiveawaysInCart;
	}

	/**
	 * Updates info in the cart. Adds a notice if trying to increase
	 * the amount of giveaway product over the allowed limit
	 */
	public function updatePostAction() {
		$aParams    = $this->getRequest()->getParams();

		/** @var Sitewards_Giveaway_Helper_Data $oHelper */
		$oHelper = Mage::helper('sitewards_giveaway');

		$aGiveawaysInCart = $this->getGiveawaysInCart();

		if (array_sum($aGiveawaysInCart) > $oHelper->getGiveawaysPerCart()){
			$oHelper->setMaximumGiveawayAmountReachedMessage();
			$this->_goBack();
		} else {
			parent::updatePostAction();
		}
	}
}
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

		$aProductInformation = $oHelper->getAddCartProductInfo($this->getRequest());

		if ($oHelper->isProductGiveaway($aProductInformation[0]['id'])
			&& $oHelper->canAddProducts($aProductInformation, Sitewards_Giveaway_Helper_Data::S_CART_ACTION_ADD) == false
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
	public function updatePostAction() {
		$aParams = $this->getRequest()->getParams();
		/** @var Sitewards_Giveaway_Helper_Data $oHelper */
		$oHelper = Mage::helper('sitewards_giveaway');
		$aProductInformation = $oHelper->getUpdateCartProductInfo($this->getRequest());
		if ($aParams['update_cart_action'] == 'update_qty' && $oHelper->canAddProducts($aProductInformation) == false) {
			$this->_goBack();
		} else {
			parent::updatePostAction();
		}
	}
}
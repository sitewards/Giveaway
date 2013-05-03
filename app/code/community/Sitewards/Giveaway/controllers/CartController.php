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
		$aParams		= $this->getRequest()->getParams();
		$aProductInformation[] = array(
			'id'	=> $aParams['product'],
			'qty'	=> (isset($aParams['qty']) ? (int)$aParams['qty'] : Mage::helper('sitewards_giveaway')->getDefaultOrderQtyForProductId($aParams['product']))
		);

		if (Mage::helper('sitewards_giveaway')->canAddGiveawaysToCart() == false) {
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
		$aParams	= $this->getRequest()->getParams();
		$oCart   	= $this->_getCart();
		$oCartItems	= $oCart->getItems();

		$aProductInformation = array();
		foreach ($oCartItems as $iItemIndex => $oItem) {
			if (isset($aParams['cart'][$iItemIndex])) {
				if ($oItem->getQty() != $aParams['cart'][$iItemIndex]['qty']) {
					$aProductInformation[] = array(
							'id'	=> $oItem->getProductId(),
							'qty'	=> $aParams['cart'][$iItemIndex]['qty']
							);
				}
			}
		}

		if ($this->_canAddGiveaway($aProductInformation) == false) {
			$this->_getSession()->addNotice($this->__('Cannot add the item to shopping cart. You have already reached your limit of giveaway products.'));
			$this->_goBack();
		} else {
			parent::updatePostAction();
		}
	}
}
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

		if ($this->_canAddGiveaway($aProductInformation) == false) {
			$this->_getSession()->addNotice($this->__('Cannot add the item to shopping cart. You have already reached your limit of giveaway products.'));
			$this->_goBack();
		} else {
			parent::addAction();
		}
	}

	/**
	 * Updades info in the cart. Adds a motice if trying to increase
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

	/**
	 * Get all items in the cart,
	 * Check if any have the is_giveaway flag
	 * If not call the parent action
	 * Otherwise throw an error
	 *
	 * @param array $aProductInformation - an array with each element containing product id and quantity
	 * @return boolean
	 */
	protected function _canAddGiveaway($aProductInformation) {
		$oCart   = $this->_getCart();

		$oSitewardsGiveawayHelper	= Mage::helper('sitewards_giveaway');
		$sGiveawayAttributeCode		= $oSitewardsGiveawayHelper->getGiveawayIdentifierName();
		$iGiveawayMaxCount			= (int)$oSitewardsGiveawayHelper->getGiveawaysPerCart();

		/*
		 * First check to see if the user has requested more than the allowed number of giveaway items
		 */
		$iTotalUpdateQty = 0;
		$bUpdateIsGiveaway = false;
		foreach($aProductInformation as $aProduct) {
			$oProduct = Mage::getModel('catalog/product');
			$oProduct->load($aProduct['id']);
			if ($oProduct->getData($sGiveawayAttributeCode) == true) {
				$bUpdateIsGiveaway = true;
				$iTotalUpdateQty += $aProduct['qty'];
				if ($aProduct['qty'] > $iGiveawayMaxCount) {
					return false;
				}
			}
		}

		/*
		 * Then check to see if the total giveaway items in the cart is already greater than the limit 
		 */
		if ($bUpdateIsGiveaway == true) {
			/*
			 * Check to see if the new total would be greater than the limit
			 * taking in count that we could just increase the amount of giveaway
			 * products which are already in the cart
			 */
			$aGiveawayProductsCount = $oSitewardsGiveawayHelper->getCartGiveawayProductsAmounts();
			foreach ($aProductInformation as $aProductInfo){
				$aGiveawayProductsCount[$aProductInfo['id']] = $aProductInfo['qty'];
			}
			if(array_sum($aGiveawayProductsCount) > $iGiveawayMaxCount) {
				return false;
			}
		}
		return true;
	}
}
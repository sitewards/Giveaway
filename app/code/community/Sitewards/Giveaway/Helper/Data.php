<?php
/**
 * Sitewards_Giveaway_Helper_Data
 *
 * A helper class to read all the extension setting variables
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Sitewards_Giveaway_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Add cart action
	 *
	 * @type string
	 */
	const S_CART_ACTION_ADD    = 'add';

	/**
	 * Update cart action
	 *
	 * @type string
	 */
	const S_CART_ACTION_UPDATE = 'update';

	/**
	 * Returns an attribute code to identify a give away product by
	 *
	 * @return string
	 */
	public function getGiveawayIdentifierName() {
		if ( $this->isExtensionEnabled() == true ) {
			return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/giveaway_identifier_name');
		}
	}

	/**
	 * Returns the number of giveaway items allowed per cart instance
	 *
	 * @return integer
	 */
	public function getGiveawaysPerCart() {
		if ( $this->isExtensionEnabled() == true ) {
			return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/giveaways_per_cart');
		}
	}

	/**
	 * Returns min total to add giveaways
	 *
	 * @return integer
	 */
	protected function getMinTotal() {
		if ( $this->isExtensionEnabled() == true ) {
			return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/min_total');
		}
	}

	/**
	 * Returns the number of product in cart needed to add giveaways
	 *
	 * @return integer
	 */
	protected function getNonGiveawaysPerCart() {
		if ( $this->isExtensionEnabled() == true ) {
			return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/min_products');
		}
	}

	/**
	 * Returns true is extension is active
	 *
	 * @return boolean
	 */
	public function isExtensionEnabled() {
		return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/enable_ext');
	}

	/**
	 * @return boolean
	 */
	public function isForwardToGiveawaysPageEnabled(){
		return Mage::getStoreConfig('sitewards_giveaway_config/sitewards_giveaway_general/giveaways_forward_to_list');
	}

	/**
	 * Returns the page for giveaway products
	 *
	 * @return string
	 */
	public function getGiveawaysPage(){
		return 'giveaway';
	}

	/**
	 * Returns if the product is a giveaway
	 *
	 * @param integer $iProductId
	 *
	 * @return boolean
	 */
	public function isProductGiveaway($iProductId){
		$oProduct= Mage::getModel('catalog/product')->load($iProductId);
		return (boolean)$oProduct->getData($this->getGiveawayIdentifierName());
	}

	/**
	 * Returns an array of giveaway product ids and their amount in the cart
	 *
	 * @return array
	 */
	public function getCartGiveawayProductsAmounts(){
		return $this->getCartProductsAmounts(true);
	}

	/**
	 * Returns an array of non giveaway product ids and their amount in the cart
	 *
	 * @return array
	 */
	public function getCartNonGiveawayProductsAmounts(){
		return $this->getCartProductsAmounts(false);
	}

	/**
	 * Returns if user can add giveaway products to his cart
	 *
	 * @return boolean|integer
	 */
	public function canAddGiveawaysToCart(){
		return ($this->canHaveMoreGiveaways()
			&& $this->hasEnoughNonGiveaways()
			&& $this->hasEnoughTotal()
			&& $this->siteHasGiveaways()
		);
	}

	/**
	 * returns if sessions grand total is at least the defined min_total
	 *
	 * @return bool
	 */
	private function hasEnoughTotal() {
		$oCheckoutSession = Mage::getSingleton('checkout/session');
		if ($oCheckoutSession->getQuote()->getBaseGrandTotal() < $this->getMinTotal()) {
			return false;
		}
		return true;
	}

	/**
	 * returns if user has enough non giveaways in cart
	 */
	private function hasEnoughNonGiveaways() {
		$aNonGiveawayProductsInCart = $this->getCartNonGiveawayProductsAmounts();
		if (array_sum($aNonGiveawayProductsInCart) < $this->getNonGiveawaysPerCart()) {
			return false;
		}
		return true;
	}

	/**
	 * returns if user has not already all possible giveaways in cart
	 *
	 * @return bool
	 */
	private function canHaveMoreGiveaways() {
		$aGiveawayProductsInCart = $this->getCartGiveawayProductsAmounts();
		if (array_sum($aGiveawayProductsInCart) >= $this->getGiveawaysPerCart()) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the default order qty for a product by provided product id
	 *
	 * @param integer|boolean $iProductId
	 * @return integer|float
	 * @throws Exception
	 * 	if the product id is not an integer greater than zero
	 */
	public function getDefaultOrderQtyForProductId($iProductId = false){

		if (intval($iProductId) != $iProductId || intval($iProductId) <= 0){
			throw new Exception("ProductId must be an integer greater than 0.");
		}

		$oProductViewBlock = Mage::app()->getLayout()->createBlock('catalog/product_view');
		$oProduct = Mage::getModel('catalog/product')->load($iProductId);
		$mDefaultQty = $oProductViewBlock->getProductDefaultQty($oProduct);

		return $mDefaultQty;
	}

	/**
	 * Checks if customer can proceed to checkout.
	 * If his cart contains only giveaway products,
	 * or giveaway products are more than allowed,
	 * there is no checkout possible
	 *
	 * @return bool
	 */
	public function isCartValidForCheckout(){
		/* @var $oCartHelper Mage_Checkout_Helper_Cart */
		$oCartHelper		= Mage::helper('checkout/cart');
		$oCart				= $oCartHelper->getCart();
		$oCartItems			= $oCart->getItems();
		$bValidCart			= false;
		$oProduct = Mage::getModel('catalog/product');

		// loop through all products in the cart and set $bValidCart
		// to true if we have at least one non-giveaway product
		foreach ($oCartItems as $oItem) {
			$iProductId = $oItem->getProductId();
			$oProduct->load($iProductId);
			if ( $oProduct->getData($this->getGiveawayIdentifierName()) != true ) {
				$bValidCart = true;
			}
			$oProduct->clearInstance();
		}

		$aGiveawayProductsInCart = $this->getCartGiveawayProductsAmounts();
		if (array_sum($aGiveawayProductsInCart) > $this->getGiveawaysPerCart()) {
			$bValidCart = false;
		}

		// if the cart is empty, it's valid too
		if (count($oCartItems) == 0) {
			$bValidCart = true;
		}

		return $bValidCart;
	}

	/**
	 * Returns an array of non giveaway product ids and their amount in the cart
	 *
	 * @param bool $bIsGiveaway
	 * @return array
	 */
	private function getCartProductsAmounts($bIsGiveaway = false) {
		$oCart = Mage::getSingleton('checkout/cart');
		$aProductsInCart = array();
		foreach ($oCart->getItems() as $oItem) {
			$iProductId = $oItem->getProductId();
			$oProduct = Mage::getModel('catalog/product')->load($iProductId);
			if ($oProduct->getData($this->getGiveawayIdentifierName()) == $bIsGiveaway) {
				$aProductsInCart[$iProductId] = $oItem->getQty();
			}
		}
		return $aProductsInCart;
	}

	/**
	 * Check to see if there are giveaway products available on the site
	 *
	 * @return bool
	 */
	public function siteHasGiveaways() {
		/* @var $oProduct Mage_Catalog_Model_Product */
		$oProduct = Mage::getModel('catalog/product');
		/* @var $oCollection Mage_Catalog_Model_Resource_Product_Collection */
		$oCollection = $oProduct->getResourceCollection()
			->addStoreFilter()
			->addAttributeToFilter($this->getGiveawayIdentifierName(), true);

		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($oCollection);
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($oCollection);

		if($oCollection->getSize() > 0) {
			return true;
		}
	}

	/**
	 * Get all items in the cart
	 * Get all items adding to the cart
	 * Calculate cart params
	 * Compare cart params with config limits and add notices
	 *
	 * @param array $aProductInformation - an array with each element containing product id and quantity
	 * @param string $sCartAction - cart action
	 * @return boolean
	 */
	public function canAddProducts($aProductInformation, $sCartAction = self::S_CART_ACTION_UPDATE) {
		/** @var $oSitewardsGiveawayHelper Sitewards_Giveaway_Helper_Data */
		$oSitewardsGiveawayHelper	= Mage::helper('sitewards_giveaway');
		if ($sCartAction == self::S_CART_ACTION_ADD
			&& $oSitewardsGiveawayHelper->isProductGiveaway($aProductInformation[0]['id']) == false
		) {
			return true;
		}
		/** @var $oCheckoutSession Mage_Checkout_Model_Session */
		$oCheckoutSession 			= Mage::getSingleton('checkout/session');
		$sGiveawayAttributeCode		= $oSitewardsGiveawayHelper->getGiveawayIdentifierName();

		/*
		 * Config limits
		 */
		$iGiveawayMaxCount			= (int)$oSitewardsGiveawayHelper->getGiveawaysPerCart();
		$iNonGiveawayMinCount		= (int)$oSitewardsGiveawayHelper->getNonGiveawaysPerCart();
		$iMinTotal					= (float)$oSitewardsGiveawayHelper->getMinTotal();

		/*
		 * Calculate cart params according to the cart action
		 */
		$iTotalGiveawayQty		= 0;
		$iTotalNonGiveawayQty	= 0;
		$iBaseGrandTotal		= 0;
		if ($sCartAction == self::S_CART_ACTION_ADD) {
			$iTotalGiveawayQty		= array_sum($oSitewardsGiveawayHelper->getCartGiveawayProductsAmounts());
			$iTotalNonGiveawayQty	= array_sum($oSitewardsGiveawayHelper->getCartNonGiveawayProductsAmounts());
			$iBaseGrandTotal		= (float)$oCheckoutSession->getQuote()->getBaseGrandTotal();
		}
		$bUpdateIsGiveaway = false;
		foreach($aProductInformation as $aProduct) {
			$oProduct = Mage::getModel('catalog/product');
			$oProduct->load($aProduct['id']);
			if ($oProduct->getData($sGiveawayAttributeCode) == true) {
				$bUpdateIsGiveaway = true;
				$iTotalGiveawayQty += $aProduct['qty'];
			} else {
				$iTotalNonGiveawayQty += $aProduct['qty'];
			}
			$iBaseGrandTotal += $aProduct['qty'] * $oProduct->getPrice();
		}

		/*
		 * Compare cart params with config limits
		 */
		if ($bUpdateIsGiveaway == true) {
			if($iTotalGiveawayQty > $iGiveawayMaxCount) {
				$oCheckoutSession->addNotice($this->__('Cannot add the item to shopping cart. You have already reached your limit of giveaway products.'));
				return false;
			} elseif ($iTotalNonGiveawayQty < $iNonGiveawayMinCount) {
				$oCheckoutSession->addNotice($this->__('Cannot add the item to shopping cart. You need more products in cart.'));
				return false;
			} elseif ($iBaseGrandTotal < $iMinTotal) {
				$oCheckoutSession->addNotice($this->__('Cannot add the item to shopping cart. You need more total.'));
				return false;
			}
		}
		return true;
	}

	/**
	 * Return update cart product data
	 *
	 * @param Mage_Core_Controller_Request_Http $oRequest
	 * @return array
	 */
	public function getUpdateCartProductInfo(Mage_Core_Controller_Request_Http $oRequest) {
		$aParams = $oRequest->getParams();
		$aProductInformation = array();
		foreach (Mage::getSingleton('checkout/cart')->getItems() as $iItemIndex => $oItem) {
			if (isset($aParams['cart'][$iItemIndex])) {
				$aProductInformation[] = array(
					'id'	=> $oItem->getProductId(),
					'qty'	=> $aParams['cart'][$iItemIndex]['qty']
				);
			}
		}
		return $aProductInformation;
	}

	/**
	 * Return add cart product data
	 *
	 * @param Mage_Core_Controller_Request_Http $oRequest
	 * @return array
	 */
	public function getAddCartProductInfo(Mage_Core_Controller_Request_Http $oRequest) {
		$aParams = $oRequest->getParams();
		$aProductInformation[] = array(
			'id'	=> $aParams['product'],
			'qty'	=> (isset($aParams['qty']) ? (int)$aParams['qty'] : $this->getDefaultOrderQtyForProductId($aParams['product']))
		);
		return $aProductInformation;
	}
}
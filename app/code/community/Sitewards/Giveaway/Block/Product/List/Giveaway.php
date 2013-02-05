<?php
/**
 * Sitewards_Giveaway_Block_Product_List_Giveaway
 * 
 * Builds a block to display all giveaway products
 * Sets up a collection and checks for true against the giveaway identifier
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Product_List_Giveaway extends Mage_Catalog_Block_Product_List {
	/**
	 * Giveaway Product Collection
	 *
	 * @var Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected $_productCollection;

	/**
	 * Set the Giveaway Product Collection then call the parent function
	 * 
	 * (non-PHPdoc)
	 * @see Mage_Core_Block_Abstract::_prepareLayout()
	 */
	public function _prepareLayout() {
		$this->_getGiveawayCollection();

		// add Home breadcrumb
		$oBreadcrumbs = $this->getLayout()->getBlock('breadcrumbs');

		// if we don't have breadcrumbs in layout, just continue
		if (!$oBreadcrumbs){
			return parent::_prepareLayout();
		}

		$sTitle = $this->__('Free Allowances');
		if ($oBreadcrumbs) {
			$oBreadcrumbs->addCrumb('home', array(
					'label' => $this->__('Home'),
					'title' => $this->__('Go to Home Page'),
					'link'  => Mage::getBaseUrl()
			))->addCrumb('search', array(
					'label' => $sTitle,
					'title' => $sTitle
			));
		}

		// modify page title
		$oHeadBlock = $this->getLayout()->getBlock('head');
		if ($oHeadBlock){
			$oHeadBlock->setTitle($sTitle);
		}

		// add page title
		$oTitleBlock = $this->getLayout()->createBlock('sitewards_giveaway/product_listtitle')
			->setTemplate('sitewards/giveaway/catalog/product/list_title.phtml')
			->setPageTitle($sTitle);
		$this->getLayout()->getBlock('content')->insert($oTitleBlock);

		return parent::_prepareLayout();
	}

	/**
	 * Retrieve loaded Giveaway Product Collection
	 *
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected function _getGiveawayCollection() {
		if (is_null($this->_productCollection)) {
			$oProduct = Mage::getModel('catalog/product');
			/* @var $oCollection Mage_Catalog_Model_Resource_Product_Collection */
			$this->_productCollection = $oProduct->getResourceCollection();

			$oSitewardsGiveawayHelper = Mage::helper('sitewards_giveaway');
			$sGiveawayAttributeCode = $oSitewardsGiveawayHelper->getGiveawayIdentifierName();
			$this->_productCollection->addAttributeToFilter($sGiveawayAttributeCode, true);
			$this->_productCollection->addAttributeToSelect('*');
		}

		return $this->_productCollection;
	}

	/**
	 * Retrieve loaded Giveaway Product Collection
	 *
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	public function getLoadedProductCollection() {
		return $this->_getGiveawayCollection();
	}

	/**
	 * Set the Giveaway Product Collection
	 * 
	 * @param Mage_Eav_Model_Entity_Collection_Abstract $oCollection
	 */
	public function setCollection($oCollection) {
		$this->_productCollection = $oCollection;
		return $this;
	}
}
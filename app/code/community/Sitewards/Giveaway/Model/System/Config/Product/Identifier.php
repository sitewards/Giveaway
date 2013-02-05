<?php
/**
 * Sitewards_Giveaway_Model_System_Config_Product_Identifier
 * 
 * Return an array of all current product attributes
 * This is to be used with the main extension config to allow a user to select against which attribute the extension will check
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Model_System_Config_Product_Identifier {
	/**
	 * Returns an array of the value and attribute code for each product attribute
	 *  
	 * @return array
	 */
	public function toOptionArray() {
		$oEavEntityAttributeCollection = Mage::getResourceModel('eav/entity_attribute_collection');
		$oEavEntity                    = Mage::getModel('eav/entity');
		$iProductEntityTypeId          = $oEavEntity->setType('catalog_product')->getTypeId();

		$aAttributeInfo = $oEavEntityAttributeCollection->setEntityTypeFilter($iProductEntityTypeId)->addSetInfo()->getData();

		$aReturnAttributes = array();

		if ( !empty ( $aAttributeInfo ) ) {
			foreach ( $aAttributeInfo as $aAttribute ) {
				$aReturnAttributes[] = array(
						'value' => $aAttribute['attribute_code'],
						'label' => $aAttribute['attribute_code']
						);
			}
		}
		
		return $aReturnAttributes;
	}
}
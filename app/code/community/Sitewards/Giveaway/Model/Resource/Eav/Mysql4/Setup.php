<?php
/**
 * Sitewards_Giveaway_Model_Resource_Eav_Mysql4_Setup
 *
 * New attribute for giveaway products
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
	/**
	 * adds the giveaway attribute to products
	 * @return array
	 */
	public function getDefaultEntities()
	{
		return array(
			'catalog_product' => array(
				'entity_model'      => 'catalog/product',
				'attribute_model'   => 'catalog/resource_eav_attribute',
				'table'             => 'catalog/product',
				'additional_attribute_table' => 'catalog/eav_attribute',
				'entity_attribute_collection' => 'catalog/product_attribute_collection',
				'attributes'        => array(
					'is_giveaway' => array(
						'group'             => 'Sitewards Giveaway',
						'label'             => 'Is Giveaway',
						'type'              => 'int',
						'input'             => 'boolean',
						'default'           => '0',
						'class'             => '',
						'backend'           => '',
						'frontend'          => '',
						'source'            => '',
						'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
						'visible'           => true,
						'required'          => false,
						'user_defined'      => false,
						'searchable'        => false,
						'filterable'        => false,
						'comparable'        => false,
						'visible_on_front'  => false,
						'visible_in_advanced_search' => false,
						'unique'            => false
					),
				),
			),
		);
	}
}

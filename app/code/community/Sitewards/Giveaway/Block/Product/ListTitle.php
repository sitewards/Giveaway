<?php
/**
 * Sitewards_Giveaway_Block_Product_ListTitle
 *
 * Basic block for rendering of page title
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2012 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Block_Product_ListTitle extends Mage_Core_Block_Template
{
    public function _toHtml()
    {
        $this->setPageTitle(Mage::helper('sitewards_giveaway')->__('Free Allowances'));
        return parent::_toHtml();
    }
}

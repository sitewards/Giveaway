<?php
/**
 * Sitewards_Giveaway_Model_Product_Quantity
 *
 * Contain add to cart product information
 *
 * @category    Sitewards
 * @package     Sitewards_Giveaway
 * @copyright   Copyright (c) 2013 Sitewards GmbH (http://www.sitewards.com/de/)
 */
class Sitewards_Giveaway_Model_Product_Quantity extends Varien_Object
{
    /**
     * Product Id
     *
     * @var integer
     */
    protected $iId;

    /**
     * Product quantity
     *
     * @var integer
     */
    protected $iQty;

    /**
     * Setter for $iId
     *
     * @param integer $iId
     * @return Sitewards_Giveaway_Model_Product_Quantity
     */
    public function setId($iId)
    {
        $this->iId = $iId;
        return $this;
    }

    /**
     * Setter for $iQty
     *
     * @param integer $iQty
     * @return Sitewards_Giveaway_Model_Product_Quantity
     */
    public function setQty($iQty)
    {
        $this->iQty = $iQty;
        return $this;
    }

    /**
     * Getter for $iId
     *
     * @return int
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * Getter for $iQty
     *
     * @return int
     */
    public function getQty()
    {
        return $this->iQty;
    }
}

<?php
class Ccc_Vendor_Model_Product extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('product/product');
    }
    public function formatUrlKey($str)
    {
        return $this->getUrlModel()->formatUrlKey($str);
    }
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('catalog/factory')->getProductUrlInstance();
        }
        return $this->_urlModel;
    }
}

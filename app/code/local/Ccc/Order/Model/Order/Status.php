<?php
class Ccc_Order_Model_Order_Status extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 2;
    protected function _construct()
    {
        $this->_init('order/order_status');
    }
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('order')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('order')->__('Disabled')
        );
    }
}

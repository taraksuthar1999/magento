<?php
class Ccc_Order_Model_Resource_Order_Address extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('order/order_address', 'order_address_id');
    }
}

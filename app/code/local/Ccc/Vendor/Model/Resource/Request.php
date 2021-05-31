<?php
class Ccc_Vendor_Model_Resource_Request extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('request/request', 'request_id');
    }
}

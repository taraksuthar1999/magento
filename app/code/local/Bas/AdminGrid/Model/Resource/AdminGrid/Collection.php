<?php 

class Bas_AdminGrid_Model_Resource_AdminGrid_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('admingrid/admingrid');
    }
}
?>
<?php
class Bas_AdminGrid_Block_Adminhtml_AdminGrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_admingrid';
        $this->_blockGroup = 'admingrid';
        $this->_headerText = Mage::helper('admingrid')->__('View Data');
        $this->_addButtonLabel = Mage::helper('admingrid')->__('Edit Status');
        $this->_addButtonLabel = Mage::helper('admingrid')->__('Add Record');

        parent::__construct();
    }
}
?>
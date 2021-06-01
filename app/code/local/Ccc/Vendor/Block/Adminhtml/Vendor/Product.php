<?php
class Ccc_Vendor_Block_Adminhtml_Vendor_Product extends Mage_Adminhtml_Block_widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_Vendor_product';
        $this->_headerText = $this->__('Product Request Grid');
        parent::__construct();
        $this->_removeButton('add');
    }
}

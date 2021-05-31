<?php
class Ccc_Order_Block_Adminhtml_Order_Create extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_order_create';
        $this->_blockGroup = "order";
        $this->_headerText = Mage::helper('order')->__('Customer List');

        parent::__construct();
    }
}

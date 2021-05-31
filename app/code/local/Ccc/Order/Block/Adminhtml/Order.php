<?php
class Ccc_Order_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = "order";
        $this->_headerText = Mage::helper('order')->__('View Data');
        // $this->_addButtonLabel = Mage::helper('order')->__('Create New Order');
        $this->_addButton('create_order', array(
            'label'     => Mage::helper('order')->__('Create New Order'),
            'onclick'   => "location.href='" . $this->getUrl('*/Adminhtml_Order/orderCreate') . "'",
            'class'     => '',
        ));
        parent::__construct();
        $this->_removeButton('add');
    }
    public function getCreateUrl()
    {
        $this->getUrl('*/Adminhtml_Order/newOrder');
    }
}

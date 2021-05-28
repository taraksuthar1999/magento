<?php
class Ccc_Order_Block_Adminhtml_Order_Cart_Shipping_Method extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_shipping_method');
    }

    public function getHeaderText()
    {
        return Mage::helper('order')->__('Shipping Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-method';
    }

    public function getShippingMethods()
    {
        $shippingMethods = Mage::getModel('shipping/config')->getActiveCarriers();
        return $shippingMethods;
    }
}

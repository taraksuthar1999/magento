<?php
class Ccc_Order_Block_Adminhtml_Order_Cart_Shipping_Method extends Mage_Adminhtml_Block_Widget_Form
{
    protected $cart = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_shipping_method');
    }
    public function setCart(Ccc_Order_Model_Cart $cart)
    {
        $this->cart = $cart;
        return $this;
    }
    public function getCart()
    {
        if (!$this->cart) {
            // $cart = Mage::getModel('order/cart')->load((int)$this->getCustomerId(), 'customer_id');
            // return $cart;
            return false;
        }
        return $this->cart;
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
    public function fetchShippingMethod()
    {
        return $this->getCart()->getShippingMethodCode();
    }
}

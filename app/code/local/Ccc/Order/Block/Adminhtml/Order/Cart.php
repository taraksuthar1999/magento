<?php
class Ccc_Order_Block_Adminhtml_Order_Cart extends Mage_Adminhtml_Block_Template
{
    protected $cart = null;
    public function __construct()
    {
        
        parent::__construct();
        $this->setTemplate('order/cart2.phtml');
    }
    
    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }
    public function getCart()
    {
        if (!$this->cart) {
            return false;
        }
        return $this->cart;
    }
}

<?php
class Ccc_Order_Block_Adminhtml_Order_Cart_Shipping_Address
extends Mage_Adminhtml_Block_Template
{
    protected $cart = null;
    public function getHeaderText()
    {
        return Mage::helper('order')->__('Shipping Address');
    }
    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }
    public function getCart()
    {
        if (!$this->cart) {
            $cart = Mage::getModel('order/cart')->load((int)$this->getCustomerId(), 'customer_id');
            return $cart;
        }
        return $this->cart;
    }
    public function getCustomerId()
    {
        $session = Mage::getSingleton('core/session');
        return $session->getCustomerId();
    }
    public function getCustomerShippingAddress()
    {
        $customerCollection = Mage::getModel('customer/address')->getCollection();
        $customerCollection->addAttributeToSelect(['city', 'firstname', 'lastname', 'country_id', 'postcode', 'region', 'street'], 'inner');
        $customerCollection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['e.entity_id', 'city' => 'at_city.value', 'firstname' => 'at_firstname.value', 'lastname' => 'at_lastname.value', 'country_id' => 'at_country_id.value', 'postcode' => 'at_postcode.value', 'street' => 'at_street.value', 'region' => 'at_region.value']);
        $customerCollection->addFieldToFilter('entity_id', $this->getCustomerId());
        return $customerCollection->getResource()->getReadConnection()->fetchRow($customerCollection->getSelect());
    }
    public function getShippingAddress()
    {
        $cartAddress = Mage::getModel('order/cart_address')->getCollection();
        $cartAddress->addFieldToFilter('cart_id', (int)$this->getCart()->getId());
        $cartAddress->addFieldToFilter('address_type', 'shipping');
        $cartAddress->getSelect();
        $address = $cartAddress->getResource()->getReadConnection()->fetchRow($cartAddress->getSelect());
        $cartAddress = Mage::getModel('order/cart_address');
        $cartAddress->setData($address);
        if (!$address) {
            $cartAddress = Mage::getModel('order/cart_address');

            $address = $this->getCustomerShippingAddress();

            unset($address['entity_id']);
            $address['cart_id'] = (int)$this->getCart()->getId();
            $address['address_type'] = 'shipping';
            $cartAddress->setData($address);
            $cartAddress->save();
        }
        return $cartAddress;
    }
}

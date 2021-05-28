<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('order/cart');
    }
    public function getCustomerBillingAddress()
    {
        $customerCollection = Mage::getModel('customer/address')->getCollection();
        $customerCollection->addAttributeToSelect(['city', 'firstname', 'lastname', 'country_id', 'postcode', 'region', 'street'], 'inner');
        $customerCollection->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['e.entity_id', 'city' => 'at_city.value', 'firstname' => 'at_firstname.value', 'lastname' => 'at_lastname.value', 'country_id' => 'at_country_id.value', 'postcode' => 'at_postcode.value', 'street' => 'at_street.value', 'region' => 'at_region.value']);
        $customerCollection->addFieldToFilter('entity_id', $this->getCustomerId());
        return $customerCollection->getResource()->getReadConnection()->fetchRow($customerCollection->getSelect());
    }
    public function getBillingAddress()
    {
        $cartAddress = Mage::getModel('order/cart_address');

        $cartAddress = Mage::getModel('order/cart_address')->getCollection();
        $cartAddress->addFieldToFilter('cart_id', $this->getId());
        $cartAddress->addFieldToFilter('address_type', 'billing');
        $cartAddress->getSelect();
        $address = $cartAddress->getResource()->getReadConnection()->fetchRow($cartAddress->getSelect());
        $cartAddress = Mage::getModel('order/cart_address');

        $cartAddress->setData($address);
        if (!$address) {
            $address = $this->getCustomerBillingAddress();
            unset($address['entity_id']);
            $address['cart_id'] = (int)$this->getId();
            $address['address_type'] = 'billing';

            $cartAddress->setData($address);
            $cartAddress->save();
        }
        return $cartAddress;
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
        $cartAddress->addFieldToFilter('cart_id', (int)$this->getId());
        $cartAddress->addFieldToFilter('address_type', 'shipping');
        $cartAddress->getSelect();
        $address = $cartAddress->getResource()->getReadConnection()->fetchRow($cartAddress->getSelect());
        $cartAddress = Mage::getModel('order/cart_address');
        $cartAddress->setData($address);
        if (!$address) {
            $cartAddress = Mage::getModel('order/cart_address');

            $address = $this->getCustomerShippingAddress();

            unset($address['entity_id']);
            $address['cart_id'] = (int)$this->getId();
            $address['address_type'] = 'shipping';
            $cartAddress->setData($address);
            $cartAddress->save();
        }
        return $cartAddress;
    }
    public function getCartItems()
    {
        $items = Mage::getModel('order/cart_item')->getCollection();
        return $items->getData();
    }
}

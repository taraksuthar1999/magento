<?php
class Ccc_Order_Model_Order extends Mage_Core_Model_Abstract
{
    protected $billingAddress = null;
    protected $shippingAddress = null;
    protected $items = null;
    protected $customer = null;

    protected function _construct()
    {
        $this->_init('order/order');
    }



    public function setBillingAddress(Ccc_Order_Model_Order_Address $billingAddress)
    {
        $this->$billingAddress = $billingAddress;
        return $this;
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }
    public function getCustomer()
    {
        if ($this->customer) {
            return $this->customer;
        }
        if (!$this->getCustomerId()) {
            return false;
        }
        $customer = Mage::getModel('Customer/Customer')->load($this->getCustomerId());
        $this->setCustomer($customer);
        return $this->customer;
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
        if (!$this->billingAddress) {

            $cartAddress = Mage::getModel('order/cart_address')->getCollection();
            $cartAddress->addFieldToFilter('cart_id', $this->getId());
            $cartAddress->addFieldToFilter('address_type', 'billing');
            $this->billingAddress = $cartAddress->getFirstItem();
        }
        return $this->billingAddress;
        // return $cartAddress;
        // echo '<pre>';
        // print_r($cartAddress->getFirstItem());
        // die;
        // // $cartAddress->getSelect();
        // //$address = $cartAddress->getResource()->getReadConnection()->fetchRow($cartAddress->getSelect());
        // // $cartAddress = Mage::getModel('order/cart_address');

        // $cartAddress->setData($address);
        // if (!$address) {
        //     $address = $this->getCustomerBillingAddress();
        //     unset($address['entity_id']);
        //     $address['cart_id'] = (int)$this->getId();
        //     $address['address_type'] = 'billing';

        //     $cartAddress->setData($address);
        //     $cartAddress->save();
        // }
        // return $cartAddress;
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
        if (!$this->shippingAddress) {
            $cartAddress = Mage::getModel('order/cart_address')->getCollection();
            $cartAddress->addFieldToFilter('cart_id', (int)$this->getId());
            $cartAddress->addFieldToFilter('address_type', 'shipping');
            $this->shippingAddress = $cartAddress->getFirstItem();
        }
        return $this->shippingAddress;
        // $cartAddress->getSelect();
        // $address = $cartAddress->getResource()->getReadConnection()->fetchRow($cartAddress->getSelect());
        // $cartAddress = Mage::getModel('order/cart_address');
        // $cartAddress->setData($address);
        // if (!$address) {
        //     $cartAddress = Mage::getModel('order/cart_address');

        //     $address = $this->getCustomerShippingAddress();

        //     unset($address['entity_id']);
        //     $address['cart_id'] = (int)$this->getId();
        //     $address['address_type'] = 'shipping';
        //     $cartAddress->setData($address);
        //     $cartAddress->save();
        // }
        // return $cartAddress;
    }
    public function setItems(Ccc_Order_Model_Resource_Cart_Item_Collection $items)
    {
        $this->items = $items;
        return $this;
    }
    public function getItems()
    {
        if (!$this->items) {
            $collection = Mage::getModel('order/cart_item')->getCollection()
                ->addFieldToFilter('cart_id', ['eq' => $this->getId()]);
            $this->items = $collection;
        }
        return $this->items;
    }
    public function getCartItems()
    {
        $items = Mage::getModel('order/cart_item')->getCollection();
        return $items->getData();
    }
    public function getSubTotal()
    {
        $items = $this->getItems();
        $sum = 0;
        foreach ($items as $key => $item) {

            $sum = $sum + ($item->getPrice() * $item->getQuantity());
        }
        return $sum;
    }
    public function getDiscountAmount()
    {

        $items = $this->getItems();
        $sum = 0;
        foreach ($items as $key => $item) {

            $sum = $sum + $item->getDiscount();
        }
        return $sum;
    }

    public function getSubtotalWithDiscount()
    {
        return ($this->getSubTotal() - $this->getDiscountAmount());
    }

    public function countItems()
    {
        print_r($this->getCartItems());
    }
}

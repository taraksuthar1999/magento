<?php
class Ccc_Order_Adminhtml_Order_CartController extends Mage_Adminhtml_Controller_Action
{
    protected $customerId = null;
    protected function setCustomerId()
    {
        $this->customerId =  Mage::getSingleton('core/session')->getCustomerId();
        return $this->customerId;
    }
    protected function getCustomerId()
    {
        if (!$this->customerId) {
            $this->setCustomerId();
        }
        return $this->customerId;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $cart = $this->newCartAction();
        //print_r($cart);
        $this->getLayout()->getBlock('order_cart')->setCart($cart);
        // $this->_addContent($cartBlock->setCart($cart));
        // print_r($cartBlock->getCart($cart));
        $this->renderLayout();
    }
    public function newCartAction()
    {
        // print_r(Mage::getModel('order/cart')->getCollection()->getTable('order/cart'));
        // die;
        // echo '<pre>';
        // $Collection = Mage::getModel('customer/address')->getCollection();
        // $select = $Collection->getSelect()
        //     ->reset(Zend_Db_Select::COLUMNS);
        // echo $select;
        // die;
        // Mage::getSingleton('core/session')->setCustomerId($this->getRequest()->getParam('id'));
        // echo Mage::getSingleton('core/session')->getCustomerId();
        try {
            $customerId = $this->getRequest()->getParam('id');
            if (!$customerId) {
                throw new Exception('customer not selected');
            }
            $cart = Mage::getModel('order/cart');

            $session = Mage::getSingleton('core/session');


            // echo $session->customerId;

            if (!$session->getCustomerId()) {
                $session->setCustomerId($customerId);
            }

            // $customerId = $session->customerId;


            $cart = $cart->load($customerId, 'customer_id');

            if (!$cart->getdata()) {
                $cart = Mage::getModel('order/cart');

                $cart->setCustomerId($customerId);
                $cart->setCreatedDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                $cart->save();
            }


            return $cart;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
    public function saveAction()
    {
        // echo '<pre>';
        // print_r($this->getRequest()->getPost());
        /*$attribute_value = $product->getResource()->getAttribute($attribute_code)->getFrontend()->getValue($product);*/
        $order = $this->getRequest()->getPost('order');

        foreach ($order as $key => $data) {
            $cart_adddress_id = $key;
            foreach ($data as $key => $address) {

                if ($key == 'billing_address') {
                    print_r($address);
                    $cartAddress = Mage::getModel('order/cart_address')->load($cart_adddress_id);
                    $cartAddress->addData($address);
                    $cartAddress->addData($address);
                }
                if ($key == 'save_in_address_book') {
                    $customerAddress = Mage::getModel('customer/address');
                }
            }
        }
    }
    public function addProductAction()
    {
        $products = $this->getRequest()->getPost('massaction');
        $customerId = Mage::getSingleton('core/session')->getCustomerId();
        $cartId = Mage::getModel('order/cart')->load($customerId, 'customer_id')->getId();
        foreach ($products as $key => $productId) {
            $product = Mage::getModel('catalog/product')->getCollection();

            $product->addFieldToFilter('entity_id', $productId);
            $product->addAttributeToSelect(['price'], 'inner');
            $product->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(['e.entity_id', 'price' => 'at_price.value']);
            $product = $product->getResource()->getReadConnection()->fetchRow($product->getSelect());

            $cartItem = Mage::getModel('order/cart_item')->load((int)$product['entity_id'], 'product_id');


            if ($cartItem->getData()) {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);
            } else {
                $cartItem->setCartId($cartId);
                $cartItem->setproductId((int)$product['entity_id']);
                $cartItem->setPrice($product['price']);
                $cartItem->setQuantity(1);
            }

            $cartItem->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item Added to cart'));
            $this->_redirect('*/*/', ['id' => $customerId]);
        }
    }
    public function updateQuantityAction()
    {
    }
}

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

        $this->getLayout()->getBlock('order_cart')->setCart($cart);

        $this->renderLayout();
    }
    public function newCartAction()
    {

        try {
            $customerId = $this->getRequest()->getParam('id');
            $session = Mage::getSingleton('core/session');
            $cart = Mage::getModel('order/cart');
            if (!$customerId && !$session->getCustomerId()) {
                return $cart;
            }

            if (!$customerId) {
                $customerId = $session->getCustomerId();
            }

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

        try {
            $order = $this->getRequest()->getPost('order');

            if ($order) {


                $cart = $this->newCartAction();
                foreach ($order as $key => $address) {

                    if ($key == 'billing_address') {

                        if (!($address['firstname'] && $address['lastname'] && $address['country_id'] && $address['street'] && $address['city'] && $address['postcode'])) {
                            throw new Exception("Enter required field in billing address.");
                        }


                        $billing = $cart->getBillingAddress();
                        $billing->addData($address);
                        $billing->setCartId($cart->getId());
                        $billing->setAddressType('billing');
                        $billing->save();
                        // Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Address Saved to Cart'));
                        // $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
                    }
                    if ($key == 'save_in_address_book') {
                        $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
                        $customerAddress = Mage::getModel('customer/address')->load($cart->getCustomerId(), 'entity_id');

                        $address = $cart->getBillingAddress()->getData();
                        unset($address['cart_id']);
                        unset($address['cart_address_id']);

                        print_r($customerAddress);
                    }
                    if ($key == 'shipping_as_billing') {
                        $billing = $cart->getBillingAddress();
                        $data = $billing->getData();
                        unset($data['cart_item_id)']);
                        unset($data['address_type']);
                        $data['address_type'] = 'shipping';
                        $shipping = Mage::getModel('order/cart_address')->getCollection();
                        $shipping->addFieldToFilter('cart_id', $cart->getId());
                        $shipping->addFieldToFilter('address_type', 'shipping');
                        $shipping = $shipping->getFirstItem();
                        if (!$shipping) {
                            $shipping = Mage::getModel('order/cart_adddress');
                        }
                        $shipping->addData($data);
                        //$shipping->save();
                    }
                    if ($key == 'shipping_address') {

                        if (!($address['firstname'] && $address['lastname'] && $address['country_id'] && $address['street'] && $address['city'] && $address['postcode'])) {
                            throw new Exception("Enter required field in shipping address.");
                        }

                        $billing = $cart->getShippingAddress();
                        $billing->addData($address);
                        $billing->setCartId($cart->getId());
                        $billing->setAddressType('shipping');
                        $billing->save();
                        // Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Address Saved to Cart'));
                        // $this->_redirect('*/*/index', ['id' => $cart->getCustomerId()]);
                    }
                    if ($key == 'save_in_address_book') {
                        $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
                        $customerAddress = Mage::getModel('customer/address')->load($cart->getCustomerId(), 'entity_id');

                        $address = $cart->getBillingAddress()->getData();
                        unset($address['cart_id']);
                        unset($address['cart_address_id']);

                        print_r($customerAddress);
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Address Saved to Cart'));
                    $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index', ['id' => $cart->getCustomerId()]);
        }
    }
    public function addProductAction()
    {
        $products = $this->getRequest()->getPost('massaction');
        $customerId = Mage::getSingleton('core/session')->getCustomerId();
        $cartId = $this->newCartAction()->getId();
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
    public function paymentMethodAction()
    {
        $data = $this->getRequest()->getPost('billingMethod');

        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError('Please Select Payment Method');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }
        $cart = $this->newCartAction();
        $cart->setPaymentMethodCode($data);
        $cart->save();
        Mage::getSingleton('adminhtml/session')->addSuccess('Paymnet Method Saved');
        $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
    }
    public function shippingMethodAction()
    {
        $data = $this->getRequest()->getPost('shippingMethod');
        if (!$data) {
            Mage::getSingleton('adminhtml/session')->addError('Please Select Shipping Method');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }
        $data = explode('_', $data);
        $cart = $this->newCartAction();

        $cart->setShippingMethodCode($data[0]);
        $cart->setShippingAmount($data[1]);
        $cart->save();
        Mage::getSingleton('adminhtml/session')->addSuccess('Shipping Method Saved');
        $this->_redirect('*/*/', ['id' => $cart->getCustomerId()]);
    }

    public function placeOrderAction()
    {
        $cart = $this->newCartAction();
        $cartItems = $cart->getItems();
        $billingAddress = $cart->getBillingAddress();
        $shippingAddress = $cart->getShippingAddress();

        if (count($cartItems) <= 0) {
            Mage::getSingleton('adminhtml/session')->addError('Please Add At Least One Item');
            $this->_redirect('*/adminhtml_order/index');
            return;
        }
        if (!$billingAddress->getId()) {
            Mage::getSingleton('adminhtml/session')->addError('Please Fill The Billing Address');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }
        if (!$shippingAddress->getId()) {
            Mage::getSingleton('adminhtml/session')->addError('Please Fill The Shipping Address');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }

        if (!$cart->getShippingMethodCode()) {
            Mage::getSingleton('adminhtml/session')->addError('Please Select Shipping Method');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }
        if (!$cart->getPaymentMethodCode()) {
            Mage::getSingleton('adminhtml/session')->addError('Please Select Payment Method');
            $this->_redirect('*/adminhtml_order/grid');
            return;
        }

        $cart->setTotal($cart->getSubtotalWithDiscount() + $cart->getShippingAmount());
        $cart->save();

        $orderModel = Mage::getModel('order/order');
        $orderModel->setData($cart->getData());
        unset($orderModel['cart_id']);
        $orderModel->setCreatedDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        unset($orderModel['payment_method_code']);
        unset($orderModel['shipping_method_code']);

        $orderModel->payment_name = $cart->getPaymentMethodCode();
        $orderModel->shipping_name = $cart->getshippingMethodCode();

        $orderModel->setCustomerName($cart->getBillingAddress()->getFirstname() . " " . $cart->getBillingAddress()->getLastname());
        $orderModel->save();


        foreach ($cartItems as $key => $item) {
            $orderItemModel = Mage::getModel('order/order_item')
                ->setData($item->getData());

            unset($orderItemModel['cart_item_id']);
            unset($orderItemModel['cart_id']);
            $orderItemModel->setOrderId($orderModel->getId());
            $orderItemModel->setCreatedDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
            $orderItemModel->save();
            $item->delete();
        }

        $orderAddress = Mage::getModel('order/order_address');
        $orderAddress->setData($billingAddress->getData());
        unset($orderAddress['cart_id']);
        unset($orderAddress['cart_address_id']);
        $orderAddress->setOrderId($orderModel->getId());
        $orderAddress->setCreatedDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        $orderAddress->save();
        Mage::getModel('order/cart_address')->load($billingAddress->getCartAddressId())->delete();



        $orderAddress = Mage::getModel('order/order_address');
        $orderAddress->setData($shippingAddress->getData());
        unset($orderAddress['cart_id']);
        unset($orderAddress['cart_address_id']);
        $orderAddress->setOrderId($orderModel->getId());
        $orderAddress->setCreatedDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        $orderAddress->save();
        $addressModel = Mage::getModel('order/cart_address')->load($shippingAddress->getCartAddressId())->delete();

        $cart->delete();
        Mage::getSingleton('adminhtml/session')->addSuccess("Your Order Is Placed");
        $this->_redirect('*/adminhtml_order/index');
    }
    public function deleteItemAction()
    {
        $id = (int)$this->getRequest()->getPost('delete');
        try {
            $model = Mage::getModel('order/cart_item');
            if (!$model->load($id)) {
                throw new Exception("Product Not Found!!");
            }
            $model->delete();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index');
            return;
        }
        Mage::getSingleton('adminhtml/session')->addSuccess('Product is Deleted Successfully');
        $this->_redirect('*/*/index');
    }
    public function updateQuantityAction()
    {
        try {

            if (!$quantity = $this->getRequest()->getPost('update')) {
                throw new Exception('invalid request');
            }



            $cartItemId = $quantity['cart_item_id'];
            $quantity = $quantity['quantity'];
            $cartItem = Mage::getModel('order/cart_item')->load($cartItemId);
            $cartItem->setQuantity($quantity);
            $cartItem->save();
            Mage::getSingleton('adminhtml/session')->addSuccess('Product is Deleted Successfully');
            $this->_redirect('*/*/index');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
}

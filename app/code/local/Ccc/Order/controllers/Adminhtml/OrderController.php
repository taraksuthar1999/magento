<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected $_publicActions = array('view', 'index');

    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Ccc_Order');
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Mage_Adminhtml_Sales_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    /**
     * Orders grid
     */
    public function indexAction()
    {
        $this->loadLayout();
        // print_r(get_class(Mage::getBlockSingleton('order/adminhtml_order')));
        // die;
        $this->renderLayout();
        // $this->_addContent($this->getLayout()->createBlock('order/adminhtml_order'));
        // $this->_title($this->__('Sales'))->_title($this->__('Orders'));

        // $this->_initAction()
        //     ->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('newOrderAction');
    }
    public function newOrderAction()
    {
        $this->loadLayout();
        $cart = $this->newCartAction();
        $this->_addContent($this->getLayout()->createBlock('order/adminhtml_order_cart')->setCart($cart));
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
            }
            $cart = Mage::getModel('order/cart');

            $session = Mage::getSingleton('core/session');



            $session->setCustomerId($customerId);



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
    public function orderCreateAction()
    {
        // print_r($this->getLayout()->createBlock('order/adminhtml_order_create'));
        // die;
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('order/adminhtml_order_create_grid'));
        $this->renderLayout();
    }
    public function viewAction()
    {
        $this->loadLayout();
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $order = Mage::getModel('order/order')->load($orderId);
        

        $this->renderLayout();
    }
}

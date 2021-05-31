<?php

class Ccc_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        // echo '<pre>';
        // $mod = Mage::getResourceModel('product/product');
        // $attributeSetId = $mod->getEntityType()->getDefaultAttributeSetId();

        // $groups = Mage::getResourceModel('eav/entity_attribute_group_collection')
        //     ->addFieldToFilter('attribute_set_id', array('like' => '%' . $attributeSetId . '%'))->getData();
        // print_r($groups);
        // $attributeModel = Mage::getResourceModel('product/product_attribute_collection');
        // print_r($eav = Mage::getModel('eav/entity_attribute'));
        // foreach ($groups as $id => $group) {
        //     print_r($attributeModel->getData());
        // }
    }
    protected function _initProduct()
    {

        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('product/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);

        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->_initProduct();

        $this->loadLayout();
        $this->renderLayout();
    }
    public function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }
    public function saveAction()
    {
        try {

            $requestModel = Mage::getModel('vendor/request');

            $productData = $this->getRequest()->getPost('product');

            $product = Mage::getModel('product/product');

            if ($productId = $this->getRequest()->getParam('id')) {

                if (!$product->load($productId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            }

            $product->addData($productData);

            $product->save();
            $requestModel = Mage::getModel('vendor/request');



            $vendorId = $this->_getSession()->getVendor()->getId();


            $requestModel->setVendorId($vendorId);
            $requestModel->setProductId($product->getId());
            if ($productId) {
                $requestModel->setRequestType('edit');
            } else {
                $requestModel->setRequestType('save');
            }
            $requestModel->setRequestDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));

            $requestModel->save();
            Mage::getSingleton('core/session')->addSuccess("Product data added.");
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }
}

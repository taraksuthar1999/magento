<?php
class Ccc_Vendor_Adminhtml_Vendor_ProductController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vendor/product');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('vendor');

        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor_product'));

        $this->renderLayout();
    }
    // protected function _initProduct()
    // {
    //     $this->_title($this->__('Product'))
    //         ->_title($this->__('Manage products'));

    //     $productId = (int) $this->getRequest()->getParam('id');
    //     $product   = Mage::getModel('product/product')
    //         ->setStoreId($this->getRequest()->getParam('store', 0))
    //         ->load($productId);

    //     Mage::register('current_product', $product);
    //     Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
    //     return $product;
    // }
    public function approvedAction()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $requestModel = Mage::getModel('vendor/request')->load($this->getRequest()->getParam('id'), 'product_id');

        $vendorId = $requestModel->getVendorId();

        $productData = Mage::getModel('vendor/product')->load($productId)->getData();

        $catalogProduct = Mage::getModel('catalog/product');
        $attributeSetId = $catalogProduct->getResource()->getEntityType()->getDefaultAttributeSetId();
        $entityTypeId = $catalogProduct->getResource()->getEntityType()->getEntityTypeId();


        $catalogProduct->addData($productData);
        $catalogProduct->setAttributeSetId($attributeSetId);
        $catalogProduct->setEntityTypeId($entityTypeId);
        $catalogProduct->setVendorId($vendorId);
        $catalogProduct->save();
        print_r($catalogProduct->getData());
        die();


        $productRequest = Mage::getModel('vendor/request');
        $productRequest->setRequestId($requestModel->getId());
        $productRequest->setCatalogProductId($catalogProduct->getId());

        $productRequest->setRequestApproveDate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        $productRequest->save();

        $productRequest->load($productRequest->getRequestId());

        if ($productRequest->getRequestType() == 'delete') {
            $this->_forward('vendorDelete');
        }

        Mage::getSingleton('core/session')->addSuccess($this->__('The Product Approved Successfully...'));
        $this->_redirect('*/*/');
    }
    public function vendorDeleteAction()
    {
        $product = Mage::getModel('vendor/product');

        echo $productId = $this->getRequest()->getParam('id');

        $product->load($productId);

        $product->delete();
    }

    // public function newAction()
    // {
    //     $this->_forward('edit');
    // }

    // public function editAction()
    // {
    //     $productId = (int) $this->getRequest()->getParam('id');
    //     $product   = $this->_initProduct();

    //     if ($productId && !$product->getId()) {
    //         $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
    //         $this->_redirect('*/*/');
    //         return;
    //     }

    //     $this->_title($product->getName());

    //     $this->loadLayout();

    //     $this->_setActiveMenu('vendor/vendor');

    //     $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

    //     $this->renderLayout();
    // }
}

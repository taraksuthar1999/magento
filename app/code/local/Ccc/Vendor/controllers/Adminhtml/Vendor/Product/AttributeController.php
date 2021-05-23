<?php
class Ccc_Vendor_Adminhtml_Vendor_Product_AttributeController extends Mage_Adminhtml_Controller_Action
{
    protected $_entityTypeId;

    const XML_PATH_ALLOWED_TAGS = 'system/catalog/frontend/allowed_html_tags_list';

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('product/session');
    }

    protected function _getAllowedTags()
    {
        return explode(',', Mage::getStoreConfig(self::XML_PATH_ALLOWED_TAGS));
    }

    public function preDispatch()
    {
        //$this->_setForcedFormKeyActions('delete');
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Ccc_Vendor_Model_Resource_Product::ENTITY)->getTypeId();
    }
    protected function _initAction()
    {
        $this->_title($this->__('product'))
            ->_title($this->__('Attributes'))
            ->_title($this->__('Manage Attributes'));
        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('attribute')
                ->_addBreadcrumb(mage::helper('vendor')->__('Product'), Mage::helper('vendor')->__('Product'))
                ->_addBreadcrumb(
                    Mage::helper('vendor')->__('Manage Attribute'),
                    Mage::helper('vendor')->__('Manage Attribute')
                );
        }
        return $this;
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('vendor');
        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor_product_attribute'));
        $this->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');


        $model = Mage::getModel('vendor/resource_product_eav_attribute')
            ->setEntityTypeId($this->_entityTypeId);

        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                Mage::getSingleton('vendor/session')->addError(
                    Mage::helper('vendor')->__('This attribute no longer exists')
                );
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('vendor/session')->addError(
                    Mage::helper('vendor')->__('This attribute cannot be edited.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        $data = Mage::getSingleton('vendor/session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('entity_attribute', $model);

        $this->_initAction();

        $this->_title($id ? $model->getName() : $this->__('New Attribute'));

        $item = $id ? Mage::helper('vendor')->__('Edit Vendor Attribute')
            : Mage::helper('vendor')->__('New Vendor Attribute');

        $this->_addBreadcrumb($item, $item);

        $this->_setActiveMenu('vendor');

        $this->renderLayout();
    }
    public function saveAction()
    {

        $data = $this->getRequest()->getPost();


        if ($data) {

            $session = Mage::getSingleton('vendor/session');

            $redirectBack   = $this->getRequest()->getParam('back', false);

            $model = Mage::getModel('vendor/resource_product_eav_attribute');


            $helper = Mage::helper('vendor/vendor');

            $id = $this->getRequest()->getParam('attribute_id');

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^(?!event$)[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        Mage::helper('vendor')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter. Do not use "event" for an attribute code.')
                    );
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('vendor')->__('This Attribute no longer exists')
                    );
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        Mage::helper('vendor')->__('This attribute cannot be updated.')
                    );
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['backend_model'] = $model->getBackendModel();
                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }


            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }

            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }
            try {
                $model->save();

                $session->addSuccess(
                    Mage::helper('vendor')->__('The Vendor attribute has been saved.')
                );

                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);

                $this->_redirect('*/*/', array());

                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    protected function _filterPostData($data)
    {
        if ($data) {

            $helperCatalog = Mage::helper('vendor');

            $data['frontend_label'] = (array) $data['frontend_label'];
            foreach ($data['frontend_label'] as &$value) {
                if ($value) {
                    $value = $helperCatalog->stripTags($value);
                }
            }

            if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
                $allowableTags = isset($data['is_html_allowed_on_front']) && $data['is_html_allowed_on_front']
                    ? sprintf('<%s>', implode('><', $this->_getAllowedTags())) : null;
                foreach ($data['option']['value'] as $key => $values) {
                    foreach ($values as $storeId => $storeLabel) {
                        $data['option']['value'][$key][$storeId]
                            = $helperCatalog->stripTags($storeLabel, $allowableTags);
                    }
                }
            }
        }
        return $data;
    }
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('vendor/resource_eav_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('vendor/session')->addError(
                Mage::helper('vendor')->__('Attribute with the same code already exists')
            );
            $this->_initLayoutMessages('vendor/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }
}

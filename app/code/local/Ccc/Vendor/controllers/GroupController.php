<?php
class Ccc_Vendor_GroupController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();

        $this->renderLayout();
    }
    public function newAction()
    {
        $this->_forward('edit');
    }
    public function editAction()
    {
        $this->loadLayout();
        $id = $this->getRequest()->getParam('attribute_group_id');
        $model = Mage::getModel('eav/entity_attribute_group');
        if ($id) {

            $model->load($id);
        }
        Mage::register('entity_attribute_group', $model);

        $this->renderLayout();
    }
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        $model = Mage::getModel('eav/entity_attribute_group');

        $model->setAttributeGroupName($data['attribute_group_name'])
            ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'));

        if ($model->itemExists()) {
            Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('A group with the same name already exists.'));
        } else {
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('An error occurred while saving this group.'));
            }
        }
        $this->_redirect('*/*/');
    }
}

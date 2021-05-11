<?php

class Bas_AdminGrid_Block_Adminhtml_AdminGrid_Edit_Tabs_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('admingrid_form', array('legend' => Mage::helper('admingrid')->__('Student information')));

        $fieldset->addField('stdname', 'text', array(
            'label' => Mage::helper('admingrid')->__('Name'),
            'class' => 'required-entry',
 //'required' => true,
 //'readonly' => true,
            'name' => 'stdname',
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('admingrid')->__('Email'),
            'class' => 'required-entry',
 //'required' => true,
            'name' => 'email',
 //'readonly' => true,
        ));

        $fieldset->addField('rollno', 'text', array(
            'label' => Mage::helper('admingrid')->__('Telephone'),
            'class' => 'required-entry',
 //'required' => true,
            'name' => 'rollno',
 //'readonly' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('admingrid')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('admingrid')->__('Active'),
                ),

                array(
                    'value' => 0,
                    'label' => Mage::helper('admingrid')->__('Inactive'),
                ),
            ),
        ));
        if (Mage::getSingleton('adminhtml/session')->getAdminGridData()) {

            $form->setValues(Mage::getSingleton('adminhtml/session')->getProData());
            Mage::getSingleton('adminhtml/session')->setProData(null);
        } elseif (Mage::registry('admingrid_data')) {

            $form->setValues(Mage::registry('admingrid_data')->getData());
        }
        return parent::_prepareForm();
    }
}
?>
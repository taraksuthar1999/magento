<?php
class Bas_AdminGrid_Block_Adminhtml_AdminGrid_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('admingrid_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('admingrid')->__('Student Information'));
    }

    protected function _beforeToHtml()
    {
       // print_r($this->getLayout()->createBlock('admingrid/adminhtml_admingrid_edit_tabs_form')->toHtml());
        $this->addTab('form_section', array(
            'label' => Mage::helper('admingrid')->__('Student Data'),
            'stdname' => Mage::helper('admingrid')->__('Student Name'),
            'email' => Mage::helper('admingrid')->__('Student Email'),
            'rollno' => Mage::helper('admingrid')->__('Student Roll No'),
            'content' => $this->getLayout()->createBlock('admingrid/adminhtml_admingrid_edit_tabs_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
?>
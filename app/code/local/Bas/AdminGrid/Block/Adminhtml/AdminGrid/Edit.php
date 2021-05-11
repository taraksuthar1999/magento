<?php
class Bas_AdminGrid_Block_Adminhtml_AdminGrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'admingrid';
        $this->_controller = 'adminhtml_admingrid';

        $this->_updateButton('save', 'label', Mage::helper('admingrid')->__('Save Data'));
        $this->_updateButton('delete', 'label', Mage::helper('admingrid')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('admingrid_data') && Mage::registry('admingrid_data')->getId()) {
            return Mage::helper('admingrid')->__("View Student Data '%s'", $this->htmlEscape(Mage::registry('admingrid_data')->getTitle()));
        } else {
            return Mage::helper('admingrid')->__('Student Information');
        }
    }
}
?>
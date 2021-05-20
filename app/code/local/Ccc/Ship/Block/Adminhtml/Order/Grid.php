<?php
class Ccc_Ship_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareColumns()
    {

        $this->addColumn('contact', array(
            'header' => Mage::helper('sales')->__('Contact Number'),
            'width' => '80px',
            'type'  => 'text',
        ));
        $this->addColumn('email', array(
            'header' => Mage::helper('sales')->__('email'),
            'width' => '80px',
            'type'  => 'text',
        ));



        return parent::_prepareColumns();
    }
}

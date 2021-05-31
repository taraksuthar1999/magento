<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {

        parent::__construct();
        $this->setId('customer_id');
        $this->setDefaultSort('');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_regione', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinField('store_name', 'core/store', 'name', 'store_id=store_id', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'align'     => 'right',
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('sales')->__('Email'),
            'width'     => '150px',
            'index'     => 'email'
        ));
        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('sales')->__('Telephone'),
            'width'     => '100px',
            'index'     => 'billing_telephone'
        ));
        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('sales')->__('ZIP/Post Code'),
            'width'     => '120px',
            'index'     => 'billing_postcode',
        ));
        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('sales')->__('Country'),
            'width'     => '100px',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));
        $this->addColumn('billing_regione', array(
            'header'    => Mage::helper('sales')->__('State/Province'),
            'width'     => '100px',
            'index'     => 'billing_regione',
        ));

        $this->addColumn('store_name', array(
            'header'    => Mage::helper('sales')->__('Signed Up From'),
            'align'     => 'center',
            'index'     => 'store_name',
            'width'     => '130px',
        ));

        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('order/adminhtml_order_cart/index', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}

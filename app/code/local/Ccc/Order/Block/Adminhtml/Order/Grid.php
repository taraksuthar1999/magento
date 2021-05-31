<?php
class Ccc_Order_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_id');
        $this->setDefaultSort('order_id'); // This is the primary key of the database
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('order/order_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header' => Mage::helper('order')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'order_id',
        ));


        $this->addColumn('customer_name', array(
            'header' => Mage::helper('order')->__('Customer Name'),
            'index' => 'customer_name',
        ));
        $this->addColumn('total', array(
            'header' => Mage::helper('order')->__('Total'),
            'index' => 'total',
        ));
        $this->addColumn('created_Date', array(
            'header' => Mage::helper('order')->__('Purchased On'),
            'index' => 'created_date',
            'type' => 'datetime',
            'width' => '100px',
        ));


        // $this->addRssList('rss/order/new', Mage::helper('order')->__('New Order RSS'));

        // $this->addExportType('*/*/exportCsv', Mage::helper('order')->__('CSV'));
        // $this->addExportType('*/*/exportExcel', Mage::helper('order')->__('Excel XML'));

        return parent::_prepareColumns();
    }
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order');

        $statuses = Mage::getSingleton('order/order_status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('order')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('order')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        return $this;
    }
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}

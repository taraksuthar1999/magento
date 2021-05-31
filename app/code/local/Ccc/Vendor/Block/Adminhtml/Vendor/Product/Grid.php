<?php

class Ccc_Vendor_Block_Adminhtml_Vendor_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }



    protected function _prepareCollection()
    {
        $store = $this->_getStore();

        $collection = Mage::getModel('vendor/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('weight')
            ->addAttributeToSelect('price');

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute(
            'name',
            'product/name',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'sku',
            'product/sku',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'weight',
            'product/weight',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'price',
            'product/price',
            'entity_id',
            null,
            'inner',
            $adminStore
        );


        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('vendor')->__('id'),
                'width'  => '50px',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('vendor')->__('Name'),
                'width'  => '50px',
                'index'  => 'name',
            )
        );

        $this->addColumn(
            'sku',
            array(
                'header' => Mage::helper('vendor')->__('SKU'),
                'width'  => '50px',
                'index'  => 'sku',
            )
        );

        $this->addColumn(
            'weight',
            array(
                'header' => Mage::helper('vendor')->__('Weight'),
                'width'  => '50px',
                'index'  => 'weight',
            )
        );

        $this->addColumn(
            'price',
            array(
                'header' => Mage::helper('vendor')->__('Price'),
                'width'  => '50px',
                'index'  => 'price',
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'   => Mage::helper('vendor')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getEntityId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('Approved'),
                        'url'     => array(
                            'base' => '*/*/approved',
                            'params' => ['store' => $this->getRequest()->getParam('store')],
                        ),
                        'field'   => 'id',
                    ), array(
                        'caption' => Mage::helper('vendor')->__('Rejected'),
                        'url'     => array(
                            'base' => '*/*/delete',
                            'params' => ['store' => $this->getRequest()->getParam('store')],
                        ),
                        'field'   => 'id',
                    ),
                ),
                'filter'   => false,
                'sortable' => false,
            )
        );

        parent::_prepareColumns();
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            array(
                'store' => $this->getRequest()->getParam('store'),
                'id'    => $row->getId()
            )
        );
    }
}

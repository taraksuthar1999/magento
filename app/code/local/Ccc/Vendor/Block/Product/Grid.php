<?php
class Ccc_Vendor_Block_Product_Grid extends Mage_Core_Block_Template
{
    public function getCollection()
    {

        $collection = Mage::getModel('product/product')->getCollection()
            ->addAttributeToSelect('firstname');


        $collection->joinAttribute(
            'name',
            'product/name',
            'entity_id',
            null,
            'inner',
        );

        $collection->joinAttribute(
            'sku',
            'product/sku',
            'entity_id',
            null,
            'inner',
        );
        $collection->joinAttribute(
            'weight',
            'product/weight',
            'entity_id',
            null,
            'inner',
        );
        $collection->joinAttribute(
            'price',
            'product/price',
            'entity_id',
            null,
            'inner',
        );


        return $collection->getData();
    }
}

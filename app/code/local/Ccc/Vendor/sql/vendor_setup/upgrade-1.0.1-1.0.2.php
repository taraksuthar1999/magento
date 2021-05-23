<?php

$this->startSetup();

$this->addEntityType(Ccc_Vendor_Model_Resource_Product::ENTITY, [
    'entity_model'                => 'vendor/product',
    'attribute_model'             => 'vendor/attribute',
    'table'                       => 'product/product',
    'increment_per_store'         => '0',
    'additional_attribute_table'  => null,
    'entity_attribute_collection' => 'vendor/product_attribute_collection',
]);

$this->createEntityTables('product');
$this->installEntities();

$default_attribute_set_id = Mage::getModel('eav/entity_setup', 'core_setup')
    ->getAttributeSetId('product', 'Default');

$this->run("UPDATE `eav_entity_type` SET `default_attribute_set_id` = {$default_attribute_set_id} WHERE `entity_type_code` = 'product'");

$this->endSetup();

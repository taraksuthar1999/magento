<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_address'))
    ->addColumn('cart_address_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Cart Id')
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Firstname')
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Lastname')
    ->addColumn('address_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Address Type')
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Street')
    ->addColumn('region', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Region')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'city')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'postcode')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Country Id')
    ->addForeignKey(
        $installer->getFkName('order/cart_address', 'cart_id', 'order/cart', 'cart_id'),
        'cart_id',
        $installer->getTable('order/cart'),
        'cart_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->CreateTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_item'))
    ->addColumn('cart_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Cart Item Id')
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Cart Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Product Id')
    ->addColumn('quantity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'quantity')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ), 'Price')
    ->addColumn('discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ), 'Discount')
    ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Created Date')
    ->addForeignKey(
        $installer->getFkName('order/cart_item', 'cart_id', 'order/cart', 'cart_id'),
        'cart_id',
        $installer->getTable('order/cart'),
        'cart_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
$installer->getConnection()->CreateTable($table);
$installer->endSetup();

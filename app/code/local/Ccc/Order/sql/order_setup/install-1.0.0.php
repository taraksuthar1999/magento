<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart'))
    ->addColumn('cart_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Customer Id')
    ->addColumn('total', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ), 'Total')
    ->addColumn('discount', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ), 'Discount')
    ->addColumn('payment_method_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Payment Method Code')
    ->addColumn('shipping_method_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable' => false,
    ), 'Shipping Method Code')
    ->addColumn('shipping_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, null, array(
        'nullable' => false,
    ), 'shipping_amount')
    ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Created Date');
$installer->getConnection()->CreateTable($table);
$installer->endSetup();

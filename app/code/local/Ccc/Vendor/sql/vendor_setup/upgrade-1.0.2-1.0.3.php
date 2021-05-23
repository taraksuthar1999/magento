<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()->newTable($installer->getTable('request/request'))
    ->addColumn('request_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'unsigned' => true,
        'primary' => true,

    ), 'Request Id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(

        'nullable' => false,
    ), 'Product Id')
    ->addColumn('catalog_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Catalog Product Id')
    ->addColumn('request_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(

        'nullable' => false,
    ), 'Request Type')
    ->addColumn('request_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(

        'nullable' => false,
    ), 'Request Date')
    ->addColumn('request_approve_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(

        'nullable' => false,
    ), 'Request Approve Date')
    ->addColumn('request_reject_date', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(

        'nullable' => false,
    ), 'Request reject date')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(

        'nullable' => false,
    ), 'Vendor Id');
$installer->getConnection()->createTable($table);
$installer->endSetup();

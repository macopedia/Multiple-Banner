<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table     = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'stores', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'   => 255,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Stores'
    ));
/** @todo remove column previous column store when not needed */

$installer->startSetup();
$installer->endSetup();

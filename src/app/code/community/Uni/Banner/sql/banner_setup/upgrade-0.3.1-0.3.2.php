<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'subtitle', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Subtitle'
    ));


$installer->startSetup();
$installer->endSetup();

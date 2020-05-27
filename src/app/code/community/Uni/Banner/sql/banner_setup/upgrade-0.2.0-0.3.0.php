<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()->addColumn(
    $table, 'mobile', ' smallint(6) NOT NULL default 0'
);
$installer->startSetup();
$installer->endSetup();

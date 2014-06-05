<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()->addColumn(
        $table, 'banner_name', ' varchar(255) NOT NULL default ""'
    );

$installer->getConnection()->addColumn(
    $table, 'banner_width', ' SMALLINT( 4 ) NOT NULL DEFAULT 0'
);

$installer->getConnection()->addColumn(
    $table, 'banner_height', ' SMALLINT( 4 ) NOT NULL DEFAULT 0'
);

$installer->startSetup();
$installer->endSetup();

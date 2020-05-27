<?php
/**
 * Created by Macopedia
 * Developer: Dragan Atanasov <d.atanasov@macopedia.pl>
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'product_title', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Product title'
    ));

$installer->getConnection()
    ->addColumn($table, 'badge_label', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Badge label'
    ));

$installer->getConnection()
    ->addColumn($table, 'badge_color', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Badge color'
    ));


$installer->startSetup();
$installer->endSetup();

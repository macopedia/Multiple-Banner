<?php
/**
 * Created by Macopedia
 * Developer: Dragan Atanasov <d.atanasov@macopedia.pl>
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'schedule_enabled', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'default'  => '0',
        'comment'  => 'Schedule enabled'
    ));

$installer->getConnection()
    ->addColumn($table, 'from_date', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'nullable' => true,
        'comment'  => 'Schedule from'
    ));

$installer->getConnection()
    ->addColumn($table, 'to_date', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_DATETIME,
        'nullable' => true,
        'comment'  => 'Schedule to'
    ));

$installer->startSetup();
$installer->endSetup();

<?php
/**
 * Created by Macopedia
 * Developer: Dragan Atanasov <d.atanasov@macopedia.pl>
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'product_position', array(
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Product position'
    ));

$installer->startSetup();
$installer->endSetup();

<?php
/**
 * Created by Macopedia
 * Developer: Dragan Atanasov <d.atanasov@macopedia.pl>
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$table = $installer->getTable('banner/banner');

$installer->getConnection()
    ->addColumn($table, 'image_mobile', array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => false,
        'default'  => '',
        'comment'  => 'Mobile banner image'
    ));

$installer->startSetup();
$installer->endSetup();

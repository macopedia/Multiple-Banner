<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Model_Mysql4_Bannergroup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('banner/bannergroup');
    }

    public function getDuplicateGroupCode($groupCode) {
        $this->setConnection($this->getResource()->getReadConnection());
        $table = $this->getTable('banner/bannergroup');
        $select = $this->getSelect();
        $select->from(array('main_table' => $table), 'group_id')
                ->where('group_code = ?', $groupCode);
        return $this;
    }
}
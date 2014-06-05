<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Model_Mysql4_Bannergroup extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('banner/bannergroup', 'group_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object) {
        $isDataValid = true;
        $id = $object->getId();
        $groupCode = $object->getGroupCode();
        $groupCollection = Mage::getModel('banner/bannergroup')->getCollection();
        if ($id == '' || $id == 0) {
            if ($groupCode == '') {
                throw new Exception('Banner Group code can\'t be blank !!');
            } else {
                $groupInfo = $groupCollection->getDuplicateGroupCode($groupCode);
                if ($groupInfo->count() > 0) {
                    $isDataValid = false;
                }
                if ($isDataValid === false) {
                    throw new Exception("Banner Group Code already exists !!");
                }
            }
        }
    }

}
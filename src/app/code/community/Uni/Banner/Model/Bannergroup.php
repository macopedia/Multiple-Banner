<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Model_Bannergroup extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('banner/bannergroup');
    }

    public function getDataByGroupCode($groupCode) {        
        $groupData = array();
        $bannerData = array();
        $result = array('group_data'=>$groupData,'banner_data'=>$bannerData);
        $collection = Mage::getResourceModel('banner/bannergroup_collection');
        $collection->getSelect()->where('group_code = ?', $groupCode)->where('status = 1');
        foreach ($collection as $record) {
            $bannerIds = $record->getBannerIds();
            $bannerModel = Mage::getModel('banner/banner');
            $bannerData = $bannerModel->getDataByBannerIds($bannerIds);
            $result = array('group_data' => $record, 'banner_data' => $bannerData);
        }
        return $result;
    }

}
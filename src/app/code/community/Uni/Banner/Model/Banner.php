<?php

/**
 * Unicode Systems
 *
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Model_Banner extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'banner';

    const VARNISH_TAG = 'banner-';

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag        = self::CACHE_TAG;

    public function _construct()
    {
        parent::_construct();
        $this->_init('banner/banner');
    }

    public function getAllAvailBannerIds()
    {
        $collection = Mage::getResourceModel('banner/banner_collection')
            ->getAllIds();
        return $collection;
    }

    public function getAllBanners()
    {
        $collection = Mage::getResourceModel('banner/banner_collection');
        $collection->getSelect()->where('status = ?', 1);
        $data = array();
        foreach ($collection as $record) {
            $data[$record->getId()] = array('value' => $record->getId(), 'label' => $record->getfilename());
        }
        return $data;
    }

    public function getDataByBannerIds($bannerIds)
    {
        $data = array();
        if ($bannerIds != '') {

            $collection = $this->getCollection()
                ->addIdFilter($bannerIds)
                ->addStoreFilter($this->_getCurrentStoreId())
                ->setOrder('sort_order');

            foreach ($collection as $record) {
                $status = $record->getStatus();
                if ($status == 1) {
                    $data[] = $record;
                }
            }
        }
        return $data;
    }

    /**
     * Get current store id
     *
     * @return int
     */
    protected function _getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getStores()
    {
        return explode(',', $this->getData('stores'));
    }

    public function save()
    {
        $this->setStores(join(',', $this->getData('stores')));
        parent::save();
        if(Mage::helper('core')->isModuleEnabled('Aoe_Static')) {
            Mage::helper('aoestatic')->purgeTags(self::VARNISH_TAG.$this->getId());
        }
    }

    public function delete()
    {
        parent::delete();
        if(Mage::helper('core')->isModuleEnabled('Aoe_Static')) {
            Mage::helper('aoestatic')->purgeTags(self::VARNISH_TAG.$this->getId());
        }
    }
}

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

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 2;

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag = self::CACHE_TAG;

    protected $_product;

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
        if (is_array($this->getData('stores'))) {
            $this->setStores(implode(',', $this->getData('stores')));
        }
        parent::save();
        Mage::helper('banner')->purgeCacheTag(self::VARNISH_TAG . $this->getId());
    }

    public function delete()
    {
        parent::delete();
        Mage::helper('banner')->purgeCacheTag(self::VARNISH_TAG . $this->getId());
    }

    /**
     * Before save. Validation of data
     *
     * @return Uni_Banner_Model_Banner
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getScheduleEnabled()) {
            $fromDate = $this->_getResource()->mktime($this->getFromDate());
            $toDate = $this->_getResource()->mktime($this->getToDate());
            if (!$fromDate) {
                Mage::throwException(Mage::helper('banner')->__('From Date is required.'));
            }
            if (!$toDate) {
                Mage::throwException(Mage::helper('banner')->__('To Date is required.'));
            }
            if ($fromDate > $toDate) {
                Mage::throwException(Mage::helper('banner')->__('To Date must be after From Date'));
            }
            $timeNow = Mage::getModel('core/date')->timestamp();
            if ($timeNow > $fromDate && $timeNow < $toDate) {
                $this->setStatus(self::STATUS_ENABLED);
            } else {
                $this->setStatus(self::STATUS_DISABLED);
            }
        } else {
            $this->setFromDate(null);
            $this->setToDate(null);
        }

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product | null
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $sku = $this->getProductId();
            $this->_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
        }
        return $this->_product;
    }
}

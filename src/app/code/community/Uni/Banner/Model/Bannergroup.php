<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Model_Bannergroup extends Mage_Core_Model_Abstract
{
    protected $_banners = array();

    public function _construct()
    {
        parent::_construct();
        $this->_init('banner/bannergroup');
    }

    public function getDataByGroupCode($groupCode)
    {
        $result = array('group_data' => $this, 'banner_data' => $this->getBannersByGroupCode($groupCode));
        return $result;
    }

    /**
     * @param string $groupCode
     *
     * @return array
     */
    public function getBannersByGroupCode($groupCode, $addCacheTags = false)
    {
        if (!$this->_banners) {
            $bannerGroupCollection = Mage::getModel('banner/bannergroup')->getCollection()
                ->addFieldToFilter('group_code', $groupCode)
                ->addFieldToFilter('status', Uni_Banner_Model_Banner::STATUS_ENABLED);
            $bannerGroup = $bannerGroupCollection->getFirstItem();
            if ($bannerGroup && $bannerGroup->getId()) {
                $this->setData($bannerGroup->getData());
                $bannerCollection = Mage::getModel('banner/banner')->getCollection()
                    ->addIdFilter($bannerGroup->getBannerIds())
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('status', Uni_Banner_Model_Banner::STATUS_ENABLED)
                    ->setOrder('sort_order');
                $this->_banners = array();
                Mage::getSingleton('core/resource_iterator')->walk($bannerCollection->getSelect(),
                    array(
                        array($this, 'callbackValidateBanner')
                    ),
                    array(
                        'banner'         => Mage::getModel('banner/banner'),
                        'add_cache_tags' => $addCacheTags
                    )
                );
            }
        }
        return $this->_banners;
    }

    /**
     * Callback function for checking product availability
     *
     * @param array $args
     */
    public function callbackValidateBanner($args)
    {
        /** @var Uni_Banner_Model_Banner $banner */
        $banner = clone $args['banner'];
        $banner->setData($args['row']);
        $addCacheTags = $args['add_cache_tags'];
        if ($banner->getProduct()) {
            $product = $banner->getProduct();
            if ($product->isSalable()) {
                $this->_banners[] = $banner;
                if ($addCacheTags) {
                    Mage::helper('banner')->addCacheTag(Uni_Banner_Model_Banner::VARNISH_TAG . $banner->getId());
                    Mage::helper('banner')->addCacheTag('product-' . $product->getId());
                }
            }
        } else {
            $this->_banners[] = $banner;
            Mage::helper('banner')->addCacheTag(Uni_Banner_Model_Banner::VARNISH_TAG . $banner->getId());
        }
    }
}

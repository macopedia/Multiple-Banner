<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Banner extends Mage_Core_Block_Template
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getBanner()
    {
        if (!$this->hasData('banner')) {
            $this->setData('banner', Mage::registry('banner'));
        }
        return $this->getData('banner');
    }

    public function getDataByGroupCode($groupCode)
    {
        return Mage::getModel('banner/bannergroup')->getDataByGroupCode($groupCode);
    }

    protected function checkDir($directory)
    {
        if (!is_dir($directory)) {
            umask(0);
            mkdir($directory, 0777, true);
            return true;
        }
    }
}

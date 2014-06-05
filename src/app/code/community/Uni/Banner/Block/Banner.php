<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Banner extends Mage_Core_Block_Template {

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getBanner() {
        if (!$this->hasData('banner')) {
            $this->setData('banner', Mage::registry('banner'));
        }
        return $this->getData('banner');
    }

    public function getDataByGroupCode($groupCode){
        return Mage::getModel('banner/bannergroup')->getDataByGroupCode($groupCode);
    }

    public function getResizeImage($bannerPath, $groupName, $w = 0, $h = 0) {
        $name = '';
        $_helper = Mage::helper('banner');
        $bannerDirPath = $_helper->updateDirSepereator($bannerPath);
        $baseDir = Mage::getBaseDir();
        $mediaDir = Mage::getBaseDir('media');
        $mediaUrl = Mage::getBaseUrl('media');
        $resizeDir = $mediaDir . DS . 'custom' . DS . 'banners' . DS . 'resize' . DS;
        $resizeUrl = $mediaUrl.'custom/banners/resize/';
        $imageName = basename($bannerDirPath);

        if (@file_exists($mediaDir . DS . $bannerDirPath)) {
            $name = $mediaDir . DS . $bannerPath;
            $this->checkDir($resizeDir . $groupName);
            $smallImgPath = $resizeDir . $groupName . DS . $imageName;
            $smallImg = $resizeUrl . $groupName .'/'. $imageName;
        }

        if ($name != '') {
            $resizeObject = Mage::getModel('banner/bannerresize');
            $resizeObject->setImage($name);
            if ($resizeObject->resizeLimitwh($w, $h, $smallImgPath) === false) {
                return $resizeObject->error();
            } else {                
                return $mediaUrl.'custom/banners/'.$imageName;                
            }
        } else {
            return '';
        }
    }

    protected function checkDir($directory) {
        if (!is_dir($directory)) {
            umask(0);
            mkdir($directory, 0777,true);
            return true;
        }
    }
}
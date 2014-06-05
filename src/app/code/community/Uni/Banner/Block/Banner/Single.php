<?php

/**
 * Created by Macopedia.
 * Developer: Agata Firlejczyk <a.firlejczyk@macopedia.pl>
 */
class Uni_Banner_Block_Banner_Single extends Mage_Core_Block_Template
{
    /**
     * @param $id
     *
     * @return Mage_Core_Model_Abstract|mixed
     */
    public function getBanner($id)
    {
        return Mage::getModel('banner/banner')->getCollection()
            ->addFieldToFilter('banner_name', $id)->getFirstItem();
    }

    /**
     * @param $bannerPath
     *
     * @return string
     */
    public function getBannerImg($bannerPath)
    {
        $bannerDirPath = $this->_helper()->updateDirSepereator($bannerPath);
        $mediaUrl = Mage::getBaseUrl('media');
        $imageName = basename($bannerDirPath);
        return $mediaUrl . 'custom/banners/' . $imageName;
    }

    /**
     * @return string
     */
    public function getMediaDir()
    {
        return Mage::getBaseDir('media');
    }

    /**
     * @return Uni_Banner_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('banner');
    }

    /**
     * @param $directory
     *
     * @return bool
     */
    protected function checkDir($directory)
    {
        if (!is_dir($directory)) {
            umask(0);
            mkdir($directory, 0777, true);
            return true;
        }
    }
}
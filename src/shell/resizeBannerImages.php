<?php

require_once 'abstract.php';

class ResizeBannerImages extends Mage_Shell_Abstract
{
    public function run()
    {
        /** @var Neuca_MultipleBanner_Helper_Data $helper */
        $helper = Mage::helper('banner');
        $banners = Mage::getModel('banner/banner')->getCollection();
        $bannersQuantity = count($banners);
        foreach ($banners as $key => $banner) {
            echo 'Banner ' . $key . ' from ' . $bannersQuantity . " \r";

            if ($imageMobile = $banner->getImageMobile()) {
                $helper->resizeImageInAllSizes($imageMobile, 'image_mobile');
            }
            if ($image = $banner->getFilename()) {
                $helper->resizeImageInAllSizes($image, 'image');
            }
        }
    }
}

$shell = new ResizeBannerImages();
$shell->run();

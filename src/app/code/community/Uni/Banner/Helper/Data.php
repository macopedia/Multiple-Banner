<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected static $egridImgDir = null;
    protected static $egridImgURL = null;
    protected static $egridImgThumb = null;
    protected static $egridImgThumbWidth = null;
    protected $_allowedExtensions = array();

    public function __construct()
    {
        self::$egridImgDir = Mage::getBaseDir('media') . DS;
        self::$egridImgURL = Mage::getBaseUrl('media');
        self::$egridImgThumb = "thumb/";
        self::$egridImgThumbWidth = 100;
    }

    public function updateDirSepereator($path)
    {
        return str_replace('\\', DS, $path);
    }

    public function getImageUrl($image_file)
    {
        $url = false;
        if (file_exists(self::$egridImgDir . self::$egridImgThumb . $this->updateDirSepereator($image_file))) {
            $url = self::$egridImgURL . self::$egridImgThumb . $image_file;
        } else {
            $url = self::$egridImgURL . $image_file;
        }
        return $url;
    }

    public function getFileExists($image_file)
    {
	    $path = self::$egridImgDir . $this->updateDirSepereator($image_file);
        return is_file($path);
    }

    public function getImageThumbSize($image_file)
    {
        $img_file = $this->updateDirSepereator(self::$egridImgDir . $image_file);
        if ($image_file == '' || !file_exists($img_file)) {
            return false;
        }
        list($width, $height, $type, $attr) = getimagesize($img_file);

        if ($width == 0 || $height == 0) {
            return false;
        }
        $a_height = (int)((self::$egridImgThumbWidth / $width) * $height);
        return array('width' => self::$egridImgThumbWidth, 'height' => $a_height);
    }

    public function deleteFiles($image_file)
    {
        $pass = true;
        if (!unlink(self::$egridImgDir . $image_file)) {
            $pass = false;
        }
        if (!unlink(self::$egridImgDir . self::$egridImgThumb . $image_file)) {
            $pass = false;
        }
        return $pass;
    }

    public function getBannerUrl()
    {
        return $this->_getUrl('banner');
    }

    public function purgeCacheTag($tag)
    {
        if (Mage::helper('core')->isModuleEnabled('Aoe_Static')) {
            Mage::helper('aoestatic')->purgeTags($tag);
        }
    }

    public function addCacheTag($tag)
    {
        if (Mage::helper('core')->isModuleEnabled('Aoe_Static')) {
            Mage::getSingleton('aoestatic/cache_control')->addTag($tag);
        }

    }

    /**
     * @param string $imagePath
     * @param string $bannerGroupName
     * @param int|null $w
     * @param int|null $h
     * @return string|void
     */
    public function getResizedImage($imagePath, $bannerGroupName, $w = null, $h = null)
    {
        if (!$imagePath) {
            return false;
        }
        $resizedImagePath = $this->getResizedImagePath($imagePath, $bannerGroupName, $w, $h);

        $mediaDir = Mage::getBaseDir('media');

        if (!$this->getFileExists($resizedImagePath)) {
            if (!$this->getFileExists($imagePath)) {
                return false;
            }
            $fullImagePath = $mediaDir . DS . $imagePath;
            $fullResizedImagePath = $mediaDir . DS . $resizedImagePath;
            if (!$this->resizeImage($fullImagePath, $w, $h, $fullResizedImagePath) || !$this->getFileExists($resizedImagePath)) {
                return false;
            }
        }

        return Mage::getBaseUrl('media') . DS . $resizedImagePath;
    }

    /**
     * @param string $imagePath
     * @param string $bannerGroupName
     * @param int|null $w
     * @param int|null $h
     * @return string
     */
    public function getResizedImagePath($imagePath, $bannerGroupName, $w, $h) {
        return implode(DS, [
            'custom',
            'banners',
            'resize',
            $bannerGroupName,
            implode('x', [$w, $h]),
            basename($imagePath)
        ]);
    }

    /**
     * @param string $fullImagePath
     * @param int|null $w
     * @param int|null $h
     * @param string $resizedImagePath
     * @return bool|String
     */
    protected function resizeImage($fullImagePath, $w, $h, $resizedImagePath)
    {
        $this->checkDir(dirname($resizedImagePath));
        $imagePathInfo = pathinfo($fullImagePath);
        /** @var Uni_Banner_Model_Bannerresize $resizeObject */
        $resizeObject = Mage::getModel('banner/bannerresize');
        $resizeObject->setImage($fullImagePath);

        if ($resizeObject->resizeLimitwh($w, $h, $resizedImagePath) === false) {
            return $resizeObject->error();
        }
        return true;
    }

    /**
     * @param string $directory
     * @return bool
     */
    protected function checkDir($directory) {
        if (!is_dir($directory)) {
            umask(0);
            mkdir($directory, 0777,true);
            return true;
        }
        return false;
    }
}

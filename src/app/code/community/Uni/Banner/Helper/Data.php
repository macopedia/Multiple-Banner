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
        try {
            if (!$imagePath) {
                $this->log($imagePath, $bannerGroupName, Mage::helper('banner')->__('Image path is empty.'));
                return false;
            }

            $imagePathToShow = $imagePath;
            if ($this->shouldConvertPngToJpg() && pathinfo($imagePathToShow)['extension'] === 'png') {
                $imagePathToShow = substr($imagePathToShow, 0, -3) . 'jpg';
            }

            $resizedImagePath = $this->getResizedImagePath($imagePathToShow, $bannerGroupName, $w, $h);

            $mediaDir = Mage::getBaseDir('media');

            if (!$this->getFileExists($resizedImagePath)) {
                if (!$this->getFileExists($imagePath)) {
                    $this->log($imagePath, $bannerGroupName, sprintf(Mage::helper('banner')->__('Image %s doesn\'t exists.'), $imagePath));
                    return false;
                }
                $fullImagePath = $mediaDir . DS . $imagePath;
                $fullResizedImagePath = $mediaDir . DS . $resizedImagePath;
                $isResizedImage = $this->resizeImage($fullImagePath, $w, $h, $fullResizedImagePath);
                if ($isResizedImage !== true) {
                    $this->log($imagePath, $bannerGroupName, $isResizedImage);
                    return false;
                }
                if (!$this->getFileExists($resizedImagePath)) {
                    $this->log($imagePath, $bannerGroupName, sprintf(Mage::helper('banner')->__('Can\'t generate %s image.'), $resizedImagePath));
                    return false;
                }
            }
            return Mage::getBaseUrl('media') . DS . $resizedImagePath;
        } catch (Exception $e) {
            $this->log($imagePath, $bannerGroupName, $e->getMessage());
            throw $e;
        }
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
     * @param string $fullResizedImagePath
     * @return bool|String
     */
    protected function resizeImage($fullImagePath, $w, $h, $fullResizedImagePath)
    {
        $this->checkDir(dirname($fullResizedImagePath));
        $imagePathInfo = pathinfo($fullImagePath);
        /** @var Uni_Banner_Model_Bannerresize $resizeObject */
        $resizeObject = Mage::getModel('banner/bannerresize');
        $resizeObject->setImage($fullImagePath);
        $resizeObject->setQuality($this->getQuality());

        if ($this->shouldConvertPngToJpg() && $imagePathInfo['extension'] === 'png') {
            $convertedFullImagePath = sprintf('%s/%s.jpg', $imagePathInfo['dirname'], $imagePathInfo['filename']);
            $this->convertPngToJpg($resizeObject, $convertedFullImagePath);
        }

        if ($resizeObject->resizeLimitwh($w, $h, $fullResizedImagePath) === false) {
            return $resizeObject->error();
        }
        return true;
    }

    /**
     * @param string $imagePath
     * @param string $imageType
     * @throws Exception
     */
    public function resizeImageInAllSizes($imagePath, $imageType) {
        $bannerGroups = Mage::getModel('banner/bannergroup')->getCollection();
        foreach ($bannerGroups as $bannerGroup) {
            $groupCode = $bannerGroup->getGroupCode();
            $sizes = $this->getImageSizesListByType($imageType, $bannerGroup);
            foreach ($sizes as $size) {
                $this->getResizedImage($imagePath, $groupCode, $size['w'], $size['h']);
            }
        }
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

    /**
     * @TODO move to admin panel
     */
    protected function shouldConvertPngToJpg() {
        return false;
    }

    /**
     * @param Uni_Banner_Model_Bannerresize $bannerresize
     * @param string $newImagePath
     */
    private function convertPngToJpg(Uni_Banner_Model_Bannerresize $bannerresize, $newImagePath)
    {
        $image = $bannerresize->_img;
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, true);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        imagejpeg($bg, $newImagePath, $this->getQuality());
        imagedestroy($bg);

        $bannerresize->setImage($newImagePath);
    }

    /**
     * @TODO move to admin panel
     * @return int
     */
    protected function getQuality()
    {
        return 90;
    }

    /**
     * @param string $type
     * @param Uni_Banner_Model_Bannergroup $bannerGroup
     * @return int[]
     * @throws Exception
     */
    public function getImageSizesListByType($type, Uni_Banner_Model_Bannergroup $bannerGroup)
    {
        switch ($type) {
            case 'image':
                return $this->getImageNormalSizesList($bannerGroup);
            case 'image_mobile':
                return $this->getImageMobileSizesList($bannerGroup);
            default:
                throw new Exception('Unknown image type');
        }
    }

    /**
     * @TODO Add in admin panel functionality to set more possible sizes
     * @param Uni_Banner_Model_Bannergroup $bannerGroup
     * @return array
     */
    protected function getImageNormalSizesList(Uni_Banner_Model_Bannergroup $bannerGroup)
    {
        return [
            [
                'w' => $bannerGroup->getBannerWidth(),
                'h' => $bannerGroup->getBannerHeight()
            ]
        ];
    }

    /**
     * @TODO Add in admin panel functionality to set more possible sizes
     * @param Uni_Banner_Model_Bannergroup $bannerGroup
     * @return array
     */
    protected function getImageMobileSizesList(Uni_Banner_Model_Bannergroup $bannerGroup)
    {
        return [
            [
                'w' => $bannerGroup->getBannerWidth(),
                'h' => $bannerGroup->getBannerHeight()
            ]
        ];
    }

    /**
     * @param string $imagePath
     * @param string $bannerGroupName
     * @param string $message
     */
    protected function log($imagePath, $bannerGroupName, $message)
    {
        $text = [
            'imagePath' => $imagePath,
            'bannerGroupName' => $bannerGroupName,
            'message' => $message
        ];
        Mage::log(print_r($text, true), Zend_Log::DEBUG, 'multiple-banner.log');
    }
}

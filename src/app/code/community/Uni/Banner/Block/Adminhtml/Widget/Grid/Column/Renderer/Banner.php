<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
//require_once 'Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Action.php';
class Uni_Banner_Block_Adminhtml_Widget_Grid_Column_Renderer_Banner extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function __construct() {

    }

    public function render(Varien_Object $row) {
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row) {
        $dored = false;
        $out = '';
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $row->getData($this->getColumn()->getIndex());
        $url = Mage::helper('banner')->getImageUrl($val);
        $size = Mage::helper('banner')->getImageThumbSize($val);
        $file_extis = Mage::helper('banner')->getFileExists($val);
        $popLink = "popWin('$url','image','width=800,height=600,resizable=yes,scrollbars=yes')";
        if (is_array($size) && $file_extis)
            $out = '<a href="javascript:;" onclick="'.$popLink.'"><img src="'.$url.'" width="'.$size['width'].'" height="'.$size['height'].'" style="border: 2px solid #CCCCCC;"/></a>';
        return $out;
    }
}
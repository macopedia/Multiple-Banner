<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_bannergroup';
        $this->_blockGroup = 'banner';
        $this->_headerText = Mage::helper('banner')->__('Banner Group Manager');
        $this->_addButtonLabel = Mage::helper('banner')->__('Add Banner Group');
        parent::__construct();
    }

}
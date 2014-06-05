<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup_Edit_Tab_Gridbanner extends Mage_Adminhtml_Block_Widget_Container {

    /**
     * Set template
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('unibanner/banners.phtml');
    }

    public function getTabsHtml() {
        return $this->getChildHtml('tabs');
    }

    /**
     * Prepare button and grid
     *
     */
    protected function _prepareLayout() {
        $this->setChild('tabs', $this->getLayout()->createBlock('banner/adminhtml_bannergroup_edit_tab_banner', 'bannergroup.grid.banner'));
        return parent::_prepareLayout();
    }

    public function getBannergroupData() {
        return Mage::registry('bannergroup_data');
    }

    public function getBannersJson() {
        $banners = explode(',', $this->getBannergroupData()->getBannerIds());
        if (!empty($banners) && isset($banners[0]) && $banners[0] != '') {
            $data = array();
            foreach ($banners as $element) {
                $data[$element] = $element;
            }
            return Zend_Json::encode($data);
        }
        return '{}';
    }

}

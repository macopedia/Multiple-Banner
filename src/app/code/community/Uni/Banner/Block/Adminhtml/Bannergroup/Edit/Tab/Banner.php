<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup_Edit_Tab_Banner extends Uni_Banner_Block_Adminhtml_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('bannerLeftGrid');
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);
    }

    public function getBannergroupData() {
        return Mage::registry('bannergroup_data');
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('banner/banner')->getCollection();
        $collection->getSelect()->order('banner_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'banner_triggers') {
                $bannerIds = $this->_getSelectedBanners();
                if (empty($bannerIds)) {
                    $bannerIds = 0;
                }
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('banner_id', array('in' => $bannerIds));
                } else {
                    if ($bannerIds) {
                        $this->getCollection()->addFieldToFilter('banner_id', array('nin' => $bannerIds));
                    }
                }
            } else {
                parent::_addColumnFilterToCollection($column);
            }
        }
        return $this;;
    }

    protected function _prepareColumns() {
        $this->addColumn('banner_triggers', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'values' => $this->_getSelectedBanners(),
            'align' => 'center',
            'index' => 'banner_id'
        ));
        $this->addColumn('banner_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '50',
            'align' => 'center',
            'index' => 'banner_id'
        ));

        $this->addColumn('filename', array(
            'header' => Mage::helper('banner')->__('Image'),
            'align' => 'center',
            'index' => 'filename',
            'type' => 'banner',
            'escape' => true,
            'sortable' => false,
            'width' => '150px',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('catalog')->__('Title'),
            'index' => 'title',
            'align' => 'left',
        ));

        $this->addColumn('link', array(
            'header' => Mage::helper('banner')->__('Link'),
            'width' => '200px',
            'index' => 'link',
        ));

        $this->addColumn('banner_type', array(
            'header' => Mage::helper('banner')->__('Type'),
            'width' => '100px',
            'index' => 'banner_type',
            'type' => 'options',
            'options' => array(
                0 => 'Image',
                1 => 'Html',
            ),
        ));
        
        $this->addColumn('sort_order', array(
            'header' => Mage::helper('banner')->__('Sort Order'),
            'width' => '80px',
            'index' => 'sort_order',
            'align' => 'center',
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/bannergrid', array('_current' => true));
    }

    protected function _getSelectedBanners() {
        $banners = $this->getRequest()->getPost('selected_banners');
        if (is_null($banners)) {
            $banners = explode(',', $this->getBannergroupData()->getBannerIds());
            return (sizeof($banners) > 0 ? $banners : 0);
        }
        return $banners;
    }

}

?>
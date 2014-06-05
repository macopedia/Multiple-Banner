<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('bannergroupGrid');
        $this->setDefaultSort('group_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('banner/bannergroup')->getCollection();
        $collection->getSelect()->columns(array('banner_effect' => 'if((animation_type=0),pre_banner_effects,banner_effects)'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('group_id', array(
            'header' => Mage::helper('banner')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'group_id',
        ));

        $this->addColumn('group_name', array(
            'header' => Mage::helper('banner')->__('Banner Group name'),
            'index' => 'group_name',
        ));
        
        $this->addColumn('group_code', array(
            'header' => Mage::helper('banner')->__('Group code'),
            'width' => '100px',
            'index' => 'group_code',
        ));

        $this->addColumn('banner_width', array(
            'header' => Mage::helper('banner')->__('Banner Width'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'banner_width',
        ));

        $this->addColumn('banner_height', array(
            'header' => Mage::helper('banner')->__('Banner Height'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'banner_height',
        ));

        $this->addColumn('animation_type', array(
            'header' => Mage::helper('banner')->__('Banner Animation'),
            'width' => '100px',
            'align' => 'center',
            'index' => 'animation_type',
            'type' => 'options',
            'options' => array(
                0 => 'Pre-defined',
                1 => 'Custom',
            ),
        ));

        $this->addColumn('banner_effect', array(
            'header' => Mage::helper('banner')->__('Banner Effect'),
            'width' => '150px',
            'align' => 'left',
            'index' => 'banner_effect',
        ));

        $this->addColumn('banner_ids', array(
            'header' => Mage::helper('banner')->__('Banners'),
            'width' => '50px',
            'index' => 'banner_ids',
        ));
        
        $this->addColumn('show_title', array(
            'header' => Mage::helper('banner')->__('Show Title'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'show_title',
            'type' => 'options',
            'options' => array(
                1 => 'Yes',
                0 => 'No',
            ),
        ));
        $this->addColumn('show_content', array(
            'header' => Mage::helper('banner')->__('Show Content'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'show_content',
            'type' => 'options',
            'options' => array(
                1 => 'Yes',
                0 => 'No',
            ),
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('banner')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
                array(
                    'header' => Mage::helper('banner')->__('Action'),
                    'width' => '50',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('banner')->__('Edit'),
                            'url' => array('base' => '*/*/edit'),
                            'field' => 'id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('banner')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('banner')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('group_id');
        $this->getMassactionBlock()->setFormFieldName('banner');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('banner')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('banner')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('banner/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('banner')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('banner')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
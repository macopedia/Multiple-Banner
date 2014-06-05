<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Banner_Grid extends Uni_Banner_Block_Adminhtml_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('bannerGrid');
        $this->setDefaultSort('banner_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('banner/banner')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('banner_id', array(
            'header' => Mage::helper('banner')->__('ID'),
            'align' => 'center',
            'width' => '30px',
            'index' => 'banner_id',
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
            'header' => Mage::helper('banner')->__('Title'),
            'index' => 'title',
        ));

        $this->addColumn('link', array(
            'header' => Mage::helper('banner')->__('Web Url'),
            'width' => '150px',
            'index' => 'link',
        ));        

        $this->addColumn('banner_type', array(
            'header' => Mage::helper('banner')->__('Type'),
            'width' => '80px',
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
                    'width' => '80',
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
        $this->setMassactionIdField('banner_id');
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
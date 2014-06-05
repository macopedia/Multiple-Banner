<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('banner_form', array('legend' => Mage::helper('banner')->__('Item information')));
        $version = substr(Mage::getVersion(), 0, 3);
        //$config = (($version == '1.4' || $version == '1.5') ? "'config' => Mage::getSingleton('banner/wysiwyg_config')->getConfig()" : "'class'=>''");

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('banner')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('link', 'text', array(
            'label' => Mage::helper('banner')->__('Web Url'),
            'name' => 'link',
        ));

        $fieldset->addField('banner_type', 'select', array(
            'label' => Mage::helper('banner')->__('Type'),
            'name' => 'banner_type',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('banner')->__('Image'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Html'),
                ),
            ),
        ));

        $fieldset->addField('filename', 'image', array(
            'label' => Mage::helper('banner')->__('Image'),
            'required' => false,
            'name' => 'filename',
        ));

        if (in_array($version, array('1.4','1.5','1.6','1.7'))) {
            $fieldset->addField('banner_content', 'editor', array(
                'name' => 'banner_content',
                'label' => Mage::helper('banner')->__('Content'),
                'title' => Mage::helper('banner')->__('Content'),
                'style' => 'width:600px; height:250px;',
                'config' => Mage::getSingleton('banner/wysiwyg_config')->getConfig(),
                'wysiwyg' => true,
                'required' => false,
            ));
        } else {
            $fieldset->addField('banner_content', 'editor', array(
                'name' => 'banner_content',
                'label' => Mage::helper('cms')->__('Content'),
                'title' => Mage::helper('cms')->__('Content'),
                'style' => 'width:600px; height:250px;',                
                'wysiwyg' => false,
                'required' => false,
            ));
        }

        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('banner')->__('Sort Order'),
            'name' => 'sort_order',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('banner')->__('Status'),
            'class' => 'required-entry',
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('banner')->__('Disabled'),
                ),
            ),
        ));

        if (Mage::getSingleton('adminhtml/session')->getBannerData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerData());
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        } elseif (Mage::registry('banner_data')) {
            $form->setValues(Mage::registry('banner_data')->getData());
        }
        return parent::_prepareForm();
    }

}
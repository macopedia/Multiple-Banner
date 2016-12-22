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

        $fieldset->addField('subtitle', 'text', array(
            'label' => Mage::helper('banner')->__('Subtitle'),
            'name' => 'subtitle'
        ));

        $fieldset->addField('identifier', 'text', array(
                'label' => Mage::helper('banner')->__('Identifier'),
                'class' => 'validate-code',
                'required' => true,
                'name' => 'identifier',
            ));

        $field = $fieldset->addField('stores', 'multiselect', array(
            'label'    => Mage::helper('banner')->__('Store View'),
            'title'    => Mage::helper('banner')->__('Store View'),
            'name'     => 'stores',
            'required' => true,
            'values'   => $this->_getStoreValuesForForm(),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);


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

        $fieldset->addField('banner_width', 'text', array(
                'label' => Mage::helper('banner')->__('Banner Width [in px]'),
                'required' => false,
                'name' => 'banner_width',
            ));

        $fieldset->addField('banner_height', 'text', array(
                'label' => Mage::helper('banner')->__('Banner Height [in px]'),
                'required' => false,
                'name' => 'banner_height',
            ));

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


        $fieldset->addField(
            'mobile', 'select', array('name'   => 'mobile', 'label' => Mage::helper('banner')->__('Mobile'),
                                      'title'  => Mage::helper('banner')->__('Mobile'), 'disabled' => false,

                                      'values' => array(array('value' => 1,
                                                              'label' => Mage::helper('banner')->__('Yes'),),
                                                        array('value' => 0,
                                                              'label' => Mage::helper('banner')->__('No'),),),)
        );

        $fieldset->addField('product_id', 'text', array(
            'label' => Mage::helper('banner')->__('Product ID'),
            'class' => 'required-entry',
            'required' => false,
            'name' => 'product_id',
        ));

        $fieldset->addField('product_title', 'text', array(
            'label' => Mage::helper('banner')->__('Product title'),
            'name' => 'product_title'
        ));

        $fieldset->addField('product_position', 'text', array(
            'label' => Mage::helper('banner')->__('Product position'),
            'name'  => 'product_position',
            'note' => 'eg. <strong> top: 20px; right: 50px; </strong> <br/> remember about semicolon!'
        ));

        $fieldset->addField('badge_label', 'text', array(
            'label' => Mage::helper('banner')->__('Badge label'),
            'name' => 'badge_label'
        ));

        $fieldset->addField('badge_color', 'text', array(
            'label' => Mage::helper('banner')->__('Badge color'),
            'name' => 'badge_color'
        ));


        $fieldset->addField('product_image_path', 'image', array(
            'label' => Mage::helper('banner')->__('Product image'),
            'required' => false,
            'name' => 'product_image_path',
        ));

        if (Mage::getSingleton('adminhtml/session')->getBannerData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBannerData());
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        } elseif (Mage::registry('banner_data')) {
            $form->setValues(Mage::registry('banner_data')->getData());
        }
        return parent::_prepareForm();
    }


    /**
     * Get store values for form
     *
     * @return array
     */
    protected function _getStoreValuesForForm()
    {
        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        return $storeModel->getStoreValuesForForm(false, false);
    }
}

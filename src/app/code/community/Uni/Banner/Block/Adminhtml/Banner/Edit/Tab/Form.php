<?php

/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $data = null;
        if (Mage::getSingleton('adminhtml/session')->getBannerData()) {
            $data = Mage::getSingleton('adminhtml/session')->getBannerData();
            Mage::getSingleton('adminhtml/session')->setBannerData(null);
        } elseif (Mage::registry('banner_data')) {
            $data = Mage::registry('banner_data')->getData();
        }
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('banner_form',
            array('legend' => Mage::helper('banner')->__('Item information')));
        $version = substr(Mage::getVersion(), 0, 3);
        //$config = (($version == '1.4' || $version == '1.5') ? "'config' => Mage::getSingleton('banner/wysiwyg_config')->getConfig()" : "'class'=>''");

        $fieldset->addField('title', 'text', array(
            'label'    => Mage::helper('banner')->__('Title'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'title',
        ));

        $fieldset->addField('subtitle', 'text', array(
            'label' => Mage::helper('banner')->__('Subtitle'),
            'name'  => 'subtitle',
            'class' => 'is-html-field'
        ));

        $fieldset->addField('identifier', 'text', array(
            'label'    => Mage::helper('banner')->__('Identifier'),
            'class'    => 'validate-code',
            'required' => true,
            'name'     => 'identifier',
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
            'name'  => 'link',
        ));

        $fieldset->addField('banner_type', 'select', array(
            'label'  => Mage::helper('banner')->__('Type'),
            'name'   => 'banner_type',
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
            'label'    => Mage::helper('banner')->__('Desktop image'),
            'required' => false,
            'name'     => 'filename',
        ));

        $fieldset->addField('image_mobile', 'image', array(
            'label' => Mage::helper('banner')->__('Mobile image'),
            'required' => false,
            'name' => 'image_mobile',
        ));

        if (in_array($version, array('1.4', '1.5', '1.6', '1.7'))) {
            $fieldset->addField('banner_content', 'editor', array(
                'name'     => 'banner_content',
                'label'    => Mage::helper('banner')->__('Content'),
                'title'    => Mage::helper('banner')->__('Content'),
                'style'    => 'width:600px; height:250px;',
                'config'   => Mage::getSingleton('banner/wysiwyg_config')->getConfig(),
                'wysiwyg'  => true,
                'required' => false,
            ));
        } else {
            $fieldset->addField('banner_content', 'editor', array(
                'name'     => 'banner_content',
                'label'    => Mage::helper('cms')->__('Content'),
                'title'    => Mage::helper('cms')->__('Content'),
                'style'    => 'width:600px; height:250px;',
                'wysiwyg'  => false,
                'class'    => 'is-html-field',
                'required' => false,
            ));
        }

//        $fieldset->addField('banner_width', 'text', array(
//            'label'    => Mage::helper('banner')->__('Banner Width [in px]'),
//            'required' => false,
//            'name'     => 'banner_width',
//        ));
//
//        $fieldset->addField('banner_height', 'text', array(
//            'label'    => Mage::helper('banner')->__('Banner Height [in px]'),
//            'required' => false,
//            'name'     => 'banner_height',
//        ));

        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('banner')->__('Sort Order'),
            'name'  => 'sort_order',
        ));

        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('banner')->__('Status'),
            'class'  => 'required-entry',
            'name'   => 'status',
            'values' => array(
                array(
                    'value' => Uni_Banner_Model_Banner::STATUS_ENABLED,
                    'label' => Mage::helper('banner')->__('Enabled'),
                ),
                array(
                    'value' => Uni_Banner_Model_Banner::STATUS_DISABLED,
                    'label' => Mage::helper('banner')->__('Disabled'),
                ),
            ),
        ));

//
//        $fieldset->addField(
//            'mobile', 'select', array(
//                'name'     => 'mobile',
//                'label'    => Mage::helper('banner')->__('Mobile'),
//                'title'    => Mage::helper('banner')->__('Mobile'),
//                'disabled' => false,
//
//                'values' => array(
//                    array(
//                        'value' => 1,
//                        'label' => Mage::helper('banner')->__('Yes'),
//                    ),
//                    array(
//                        'value' => 0,
//                        'label' => Mage::helper('banner')->__('No'),
//                    ),
//                ),
//            )
//        );

        $fieldset->addField('product_id', 'text', array(
            'label'    => Mage::helper('banner')->__('Product SKU'),
            'required' => false,
            'name'     => 'product_id',
        ));

        $fieldset->addField('product_title', 'text', array(
            'label' => Mage::helper('banner')->__('Product title'),
            'name'  => 'product_title',
            'class' => 'is-html-field'
        ));

        $fieldset->addField('product_position', 'text', array(
            'label' => Mage::helper('banner')->__('Product position'),
            'name'  => 'product_position',
            'note' => 'eg. <strong> top: 20px; right: 50px; </strong> <br/> remember about semicolon!',
            'class' => 'is-html-field'
        ));

        $fieldset->addField('badge_label', 'text', array(
            'label' => Mage::helper('banner')->__('Badge label'),
            'name'  => 'badge_label',
            'class' => 'is-html-field'
        ));

        $fieldset->addField('badge_color', 'text', array(
            'label' => Mage::helper('banner')->__('Badge color'),
            'name'  => 'badge_color',
            'class' => 'is-html-field'
        ));


        $fieldset->addField('product_image_path', 'image', array(
            'label'    => Mage::helper('banner')->__('Product image'),
            'required' => false,
            'name'     => 'product_image_path'
        ));

        $fieldset->addField('schedule_enabled', 'checkbox', array(
            'label'   => Mage::helper('banner')->__('Enable schedule'),
            'name'    => 'schedule_enabled',
            'value'   => 1,
            'checked' => $data && $data['schedule_enabled'] ? 1 : 0,
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'time'   => true,
            'label'  => Mage::helper('banner')->__('From Date'),
            'title'  => Mage::helper('banner')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $dateFormatIso
        ));

        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'time'   => true,
            'label'  => Mage::helper('banner')->__('To Date'),
            'title'  => Mage::helper('banner')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $dateFormatIso
        ));

        $form->setValues($data);
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

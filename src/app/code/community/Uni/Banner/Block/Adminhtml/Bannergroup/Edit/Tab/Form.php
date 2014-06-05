<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('bannergroup_form', array('legend' => Mage::helper('banner')->__('Item information')));
        $animations = Mage::getSingleton('banner/status')->getAnimationArray();
        $preAnimations = Mage::getSingleton('banner/status')->getPreAnimationArray();


        $fieldset->addField('group_name', 'text', array(
            'label' => Mage::helper('banner')->__('Banner Group Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'group_name',
        ));

        if (Mage::registry('bannergroup_data')->getId() == null) {
            $fieldset->addField('group_code', 'text', array(
                'label' => Mage::helper('banner')->__('Banner Group Code'),
                'class' => 'required-entry',
                'name' => 'group_code',
                'required' => true,
            ));
        }

        $fieldset->addField('banner_width', 'text', array(
            'label' => Mage::helper('banner')->__('Banner Width [in px]'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'banner_width',
        ));

        $fieldset->addField('banner_height', 'text', array(
            'label' => Mage::helper('banner')->__('Banner Height [in px]'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'banner_height',
        ));

        $fieldset->addField('animation_type', 'select', array(
            'label' => Mage::helper('banner')->__('Banner Animation'),
            'name' => 'animation_type',
            'required' => false,
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('banner')->__('Pre-defined Animation'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Custom Animation'),
                ),
            ),
        ));

        $fieldset->addField('pre_banner_effects', 'select', array(
            'label' => Mage::helper('banner')->__('Pre-Defined Banner Effects'),
            'name' => 'pre_banner_effects',
            'required' => true,
            'values' => $preAnimations
        ));

        $fieldset->addField('banner_effects', 'select', array(
            'label' => Mage::helper('banner')->__('Custom Banner Effects'),
            'name' => 'banner_effects',
            'required' => true,
            'values' => $animations
        ));       
        

        $fieldset->addField('show_title', 'select', array(
            'label' => Mage::helper('banner')->__('Display Title'),
            'class' => 'required-entry',
            'name' => 'show_title',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('banner')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('show_content', 'select', array(
            'label' => Mage::helper('banner')->__('Display Content'),
            'class' => 'required-entry',
            'name' => 'show_content',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('banner')->__('No'),
                ),
            ),
        ));

        $fieldset->addField('link_target', 'select', array(
            'label' => Mage::helper('banner')->__('Target'),
            'name' => 'link_target',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('banner')->__('New Window'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('banner')->__('Same Window'),
                ),
            ),
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

//        $fieldset->addField('banner_ids', 'multiselect', array(
//            'label' => Mage::helper('banner')->__('Banner Images'),
//            'class' => 'required-entry',
//            'required' => true,
//            'name' => 'banner_ids[]',
//            'values' => $bannerData,
//        ));


        if (Mage::getSingleton('adminhtml/session')->getBannergroupData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBannergroupData());
            Mage::getSingleton('adminhtml/session')->setBannergroupData(null);
        } elseif (Mage::registry('bannergroup_data')) {
            $form->setValues(Mage::registry('bannergroup_data')->getData());
        }
        return parent::_prepareForm();
    }

}
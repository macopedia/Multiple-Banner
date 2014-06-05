<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Bannergroup_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'banner';
        $this->_controller = 'adminhtml_bannergroup';

        $this->_updateButton('save', 'label', Mage::helper('banner')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('banner')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('banner_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'banner_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'banner_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            function showBannerEfects(){
                var typeId=$('animation_type').value;
                var reqClass = ((typeId==1)?'required-entry':'');
                var preReqClass = ((typeId==1)?'':'required-entry');
                $('banner_effects').disabled = ((typeId==1)?false:true);
                $('pre_banner_effects').disabled = ((typeId==1)?true:false);
                $('banner_effects').addClassName(reqClass);
                $('pre_banner_effects').addClassName(preReqClass);
            }

            Event.observe('animation_type', 'change', function(){
                    showBannerEfects();
                });
            Event.observe(window, 'load', function(){
                    showBannerEfects();
                });
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('bannergroup_data') && Mage::registry('bannergroup_data')->getId()) {
            return Mage::helper('banner')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('bannergroup_data')->getGroupName()));
        } else {
            return Mage::helper('banner')->__('Add Item');
        }
    }

}
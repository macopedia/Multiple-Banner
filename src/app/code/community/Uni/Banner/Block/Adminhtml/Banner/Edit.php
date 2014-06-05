<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'banner';
        $this->_controller = 'adminhtml_banner';

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

            function showTypeContents(){
                var typeId=$('banner_type').value;
                var show = ((typeId==0)?'block':'none');
                var hide = ((typeId==0)?'none':'block');
                $('filename').setStyle({display:show});
                $('filename_delete').setStyle({display:show});
                $('banner_content').setStyle({display:hide});
                setTimeout('bannerContentType()',1000);
                alert($('filename').getStyle('display'))
            }
         
            function bannerContentType(){
                var typeId=$('banner_type').value;
                var hide = ((typeId==0)?'none':'block');
                $('buttonsbanner_content').setStyle({display:hide});
                $('banner_content_parent').setStyle({display:hide});
            }


            /* Event.observe('banner_type', 'change', function(){
                    showTypeContents();
                });
            Event.observe(window, 'load', function(){
                    showTypeContents();
                }); */
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('banner_data') && Mage::registry('banner_data')->getId()) {
            return Mage::helper('banner')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('banner_data')->getTitle()));
        } else {
            return Mage::helper('banner')->__('Add Item');
        }
    }

}
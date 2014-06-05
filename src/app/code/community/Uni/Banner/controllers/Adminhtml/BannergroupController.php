<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Banner
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Banner_Adminhtml_BannergroupController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('banner/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('banner/bannergroup')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
        }
        Mage::register('bannergroup_data', $model);
        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function bannergridAction() {
        $this->_initAction();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('banner/adminhtml_bannergroup_edit_tab_banner')->toHtml()
        );
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('banner/bannergroup')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('bannergroup_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('banner/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('banner/adminhtml_bannergroup_edit'))
                    ->_addLeft($this->getLayout()->createBlock('banner/adminhtml_bannergroup_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $banners = array();
            $availBannerIds = Mage::getModel('banner/banner')->getAllAvailBannerIds();
            parse_str($data['bannergroup_banners'], $banners);
            foreach ($banners as $k => $v) {
                if (preg_match('/[^0-9]+/', $k) || preg_match('/[^0-9]+/', $v)) {
                    unset($banners[$k]);
                }
            }
            $bannerIds = array_intersect($availBannerIds, $banners);
            $data['banner_ids'] = implode(',', $bannerIds);
            $data['banner_effects'] = (($data['animation_type'] == 0) ? '' : $data['banner_effects']);
            $data['pre_banner_effects'] = (($data['animation_type'] == 0) ? $data['pre_banner_effects'] : '');
            $model = Mage::getModel('banner/bannergroup');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('banner')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('banner')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('banner/bannergroup')->load($this->getRequest()->getParam('id'));
                $filePath = Mage::getBaseDir('media') . DS . 'custom' . DS . 'banners' . DS . 'resize' . DS . $model->getGroupCode();              
                $model->delete();
                $this->removeFile($filePath);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getModel('banner/bannergroup')->load($bannerId);
                    $filePath = Mage::getBaseDir('media') . DS . 'custom' . DS . 'banners' . DS . 'resize' . DS . $banner->getGroupCode();
                    $banner->delete();
                    $this->removeFile($filePath);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($bannerIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $bannerIds = $this->getRequest()->getParam('banner');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $banner = Mage::getSingleton('banner/bannergroup')
                                    ->load($bannerId)
                                    ->setStatus($this->getRequest()->getParam('status'))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($bannerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'banner.csv';
        $content = $this->getLayout()->createBlock('banner/adminhtml_banner_grid')
                        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'banner.xml';
        $content = $this->getLayout()->createBlock('banner/adminhtml_banner_grid')
                        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function removeFile($file) {
        try {
            $io = new Varien_Io_File();
            if(!is_dir($file) && file_exists($file)){
                $result = $io->rmdir($file, true);
            }
        } catch (Exception $e) {

        }
    }

}
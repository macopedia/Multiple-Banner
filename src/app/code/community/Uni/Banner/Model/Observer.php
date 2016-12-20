<?php

/**
 * Created by Macopedia
 * Developer: Dragan Atanasov <d.atanasov@macopedia.pl>
 */
class Uni_Banner_Model_Observer
{
    /**
     * Add cache tags for Aoe_Static
     *
     * @param Varien_Event_Observer $observer
     */
    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('core')->isModuleEnabled('Aoe_Static')) {
            /** @var Uni_Banner_Block_Banner_Single $block */
            $block = $observer->getBlock();
            if ($block instanceof Uni_Banner_Block_Banner_Single) {
                Mage::getSingleton('aoestatic/cache_control')->addTag(Uni_Banner_Model_Banner::VARNISH_TAG
                    . $block->getBanner($block->getIdentifier())
                        ->getId());
            }
        }
    }

    public function scheduleCheck()
    {
        $timeNow = gmdate('U');
        /** @var Uni_Banner_Model_Mysql4_Banner_Collection $bannersToEnable */
        $bannersToEnable = Mage::getModel('banner/banner')->getCollection()
            ->addFieldToFilter('schedule_enabled', 1)
            ->addFieldToFilter('status', Uni_Banner_Model_Banner::STATUS_DISABLED)
            ->addFieldToFilter('from_date', array('to' => $timeNow, 'date' => true))
            ->addFieldToFilter('to_date', array('from' => $timeNow, 'date' => true));
        foreach ($bannersToEnable as $banner) {
            try {
                $banner->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        /** @var Uni_Banner_Model_Mysql4_Banner_Collection $bannersToDisable */
        $bannersToDisable = Mage::getModel('banner/banner')->getCollection()
            ->addFieldToFilter('schedule_enabled', 1)
            ->addFieldToFilter('status', Uni_Banner_Model_Banner::STATUS_ENABLED)
            ->addFieldToFilter('to_date', array('to' => $timeNow, 'date' => true));
        foreach ($bannersToDisable as $banner) {
            try {
                $banner->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}

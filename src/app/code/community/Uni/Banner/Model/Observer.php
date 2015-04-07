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

}

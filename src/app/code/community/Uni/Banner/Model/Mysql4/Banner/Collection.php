<?php

class Uni_Banner_Model_Mysql4_Banner_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('banner/banner');
    }

    protected function _afterLoad()
    {
        foreach ($this as $item) {
            $item->setStores($item->getStores());
        }
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Resource_Block_Collection
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }

        if (is_array($store)) {
            $store = array_pop($store);
        }

        $this->addFieldToFilter('stores', array('finset' => $store));

        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|int[] $ids
     * @return Mage_Cms_Model_Resource_Block_Collection
     */
    public function addIdFilter($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        $this->addFieldToFilter('banner_id', array('in' => $ids));
        return $this;
    }
}

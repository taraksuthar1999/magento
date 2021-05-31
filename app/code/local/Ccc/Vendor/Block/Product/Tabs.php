<?php
class Ccc_Vendor_Block_Product_Tabs extends Mage_Core_Block_Template
{
    protected $tabs = [];
    public function __construct()
    {
        // parent::__construct();
        $this->prepareTab();
    }
    public function prepareTab()
    {
        $mod = Mage::getResourceModel('vendor/product');
        $attributeSetId = $mod->getEntityType()->getDefaultAttributeSetId();

        $groups = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->addFieldToFilter('attribute_set_id', array('like' => '%' . $attributeSetId . '%'))->getData();
        // echo '<pre>';
        // print_r($groups);
        foreach ($groups as $key => $group) {
            $this->addTab($group['attribute_group_name'], ['label' => $group['attribute_group_name'], 'attribute_group_id' => $group['attribute_group_id'], 'block' => 'Vendor/Product_Tabs_Form']);
        }

        $this->setDefaultTab('General');
        return $this;
    }
    public function setDefaultTab($defaultTab)
    {
        $this->defaultTab = $defaultTab;
        return $this;
    }
    public function getDefaultTab()
    {
        return $this->defaultTab;
    }
    public function setTabs(array $tabs)
    {
        $this->tabs = $tabs;
        return $this;
    }
    public function getTabs()
    {
        return $this->tabs;
    }
    public function addTab($key, $tab = [])
    {
        $this->tabs[$key] = $tab;
        return $this;
    }
    public function getTab($key)
    {
        if (!array_key_exists($key, $this->tabs)) {
            return null;
        }
        return $this->tabs[$key];
    }

    public function removeTab($key)
    {
        if (!array_key_exists($key, $this->tabs)) {
            return null;
        }
        unset($this->tabs[$key]);
    }
}

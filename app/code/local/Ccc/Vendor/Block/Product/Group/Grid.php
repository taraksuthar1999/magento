
<?php
class Ccc_Vendor_Block_Product_Group_Grid extends Ccc_Vendor_Block_Account_Dashboard
{
    public function getAttributeGroups()
    {
        $mod = Mage::getResourceModel('vendor/product');
        $attributeSetId = $mod->getEntityType()->getDefaultAttributeSetId();

        return Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->addFieldToFilter('attribute_set_id', array('like' => '%' . $attributeSetId . '%'))->getData();
    }
    public function getAddUrl()
    {
        return Mage::getUrl('vendor/group/new');
    }
}

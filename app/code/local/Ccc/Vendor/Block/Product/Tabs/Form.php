<?php
class Ccc_Vendor_Block_Product_Tabs_Form extends Mage_Core_Block_Template
{
    protected $attributeObject = null;
    public function getGroups()
    {
        $mod = Mage::getResourceModel('vendor/product');
        $attributeSetId = $mod->getEntityType()->getDefaultAttributeSetId();

        $groups = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->addFieldToFilter('attribute_set_id', array('like' => '%' . $attributeSetId . '%'))->getData();
        return $groups;
    }
    public function getAttributes()
    {
        $attributes1 = [];
        $attributes = Mage::getResourceModel('vendor/product_attribute_Collection');
        $groups = $this->getGroups();
        foreach ($groups as $group) {
            foreach ($attributes as $attribute) {
                if (Mage::getModel('vendor/vendor')->checkInGroup($attribute['attribute_id'], $group['attribute_set_id'], $group['attribute_group_id'])) {
                    $attributes1[$group['attribute_group_id']][] = $attribute->getData();
                }
            }
        }
        return $attributes1;
    }
    public function getOptionValues($attribute_id)
    {
        return Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute_id)
            ->setPositionOrder('desc', true)
            ->load();
    }
    public function setAttributeObject($attribute_id)
    {
        $this->attributeObject = Mage::getModel('eav/entity_attribute')->load($attribute_id);
    }
    public function getAttributeObject()
    {
        return $this->attributeObject;
    }
    // public function getOptionValues($attribute_id)
    // {
    //     $this->setAttributeObject($attribute_id);
    //     $attributeType = $this->getAttributeObject()->getFrontendInput();
    //     $defaultValues = $this->getAttributeObject()->getDefaultValue();
    //     if ($attributeType == 'select' || $attributeType == 'multiselect') {
    //         $defaultValues = explode(',', $defaultValues);
    //     } else {
    //         $defaultValues = array();
    //     }

    //     switch ($attributeType) {
    //         case 'select':
    //             $inputType = 'radio';
    //             break;
    //         case 'multiselect':
    //             $inputType = 'checkbox';
    //             break;
    //         default:
    //             $inputType = '';
    //             break;
    //     }

    //     $values = $this->getData('option_values');
    //     if (is_null($values)) {
    //         $values = array();
    //         $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
    //             ->setAttributeFilter($this->getAttributeObject()->getId())
    //             ->setPositionOrder('desc', true)
    //             ->load();

    //         $helper = Mage::helper('core');
    //         foreach ($optionCollection as $option) {
    //             $value = array();
    //             if (in_array($option->getId(), $defaultValues)) {
    //                 $value['checked'] = 'checked="checked"';
    //             } else {
    //                 $value['checked'] = '';
    //             }

    //             $value['intype'] = $inputType;
    //             $value['id'] = $option->getId();
    //             $value['sort_order'] = $option->getSortOrder();
    //             foreach ($this->getStores() as $store) {
    //                 $storeValues = $this->getStoreOptionValues($store->getId());
    //                 $value['store' . $store->getId()] = isset($storeValues[$option->getId()])
    //                     ? $helper->escapeHtml($storeValues[$option->getId()]) : '';
    //             }
    //             $values[] = new Varien_Object($value);
    //         }
    //         $this->setData('option_values', $values);
    //     }

    //     return $values;
    // }
}

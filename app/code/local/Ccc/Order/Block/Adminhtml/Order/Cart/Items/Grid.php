<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create items grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Order_Block_Adminhtml_Order_Cart_Items_Grid extends Mage_Adminhtml_Block_Template
{
    protected $cart = null;
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search_grid');
    }
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => Mage::helper('sales')->__('update Quantity'),
            'onclick' => 'mage.formSubmit(this) ',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }
    public function getProductName($id = null)
    {
        if (!$id) {
            return null;
        }
        $product = Mage::getModel('catalog/product')->getCollection();
        $product->addAttributeToSelect(['name'], 'inner');
        $product->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(['name' => 'at_name.value']);
        $product->addFieldToFilter('entity_id', $id);
        $product = $product->getResource()->getReadConnection()->fetchRow($product->getSelect());
        return $product['name'];
    }
    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }
    public function getCart()
    {
        if (!$this->cart) {
            $cart = Mage::getModel('order/cart')->load((int)$this->getCustomerId(), 'customer_id');
            return $cart;
        }
        return $this->cart;
    }

    public function getHeaderText()
    {
        return Mage::helper('order')->__('Cart Items');
    }
}

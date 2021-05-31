<?php
class Mage_Vendor_Block_Account extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vendor/account.phtml');
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('vendor')->__('My Account'));
    }
}

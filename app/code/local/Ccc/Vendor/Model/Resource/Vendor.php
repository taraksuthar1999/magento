<?php
class Ccc_Vendor_Model_Resource_Vendor extends Mage_Eav_Model_Entity_Abstract
{

	const ENTITY = 'vendor';

	public function __construct()
	{

		$this->setType(self::ENTITY)
			->setConnection('core_read', 'core_write');

		parent::__construct();
	}

	public function loadByEmail(Ccc_Vendor_Model_Vendor $customer, $email, $testOnly = false)
	{
		$adapter = $this->_getReadAdapter();
		$bind    = array('vendor_email' => $email);
		$select  = $adapter->select()
			->from($this->getEntityTable() . '_varchar', array($this->getEntityIdField()))
			->where('value = :vendor_email');
		// echo '<pre>';
		// print_r($select); die;
		// if ($customer->getSharingConfig()->isWebsiteScope()) {
		// 	if (!$customer->hasData('website_id')) {
		// 		Mage::throwException(
		// 			Mage::helper('customer')->__('Vendor website ID must be specified when using the website scope')
		// 		);
		// 	}
		// 	$bind['website_id'] = (int)$customer->getWebsiteId();
		// 	$select->where('website_id = :website_id');
		// }
		$customerId = $adapter->fetchOne($select, $bind);
		if ($customerId) {
			$this->load($customer, $customerId);
		} else {
			$customer->setData(array());
		}
		return $this;
	}
	public function changePassword(Ccc_Vendor_Model_Vendor $customer, $newPassword)
	{
		$customer->setPassword($newPassword);
		//$this->saveAttribute($customer, 'password_hash');
		return $this;
	}

	// public function loadByEmail(Ccc_Vendor_Model_Vendor $vendor, $email, $testOnly = false)
	// {
	// 	$adapter = $this->_getReadAdapter();
	// 	$bind    = array('vendor_email' => $email);
	// 	$select  = $adapter->select()
	// 		->from($this->getEntityTable() . '_varchar', array($this->getEntityIdField()))
	// 		->where('email = :vendor_email');

	// 	// if ($vendor->getSharingConfig()->isWebsiteScope()) {
	// 	// 	if (!$vendor->hasData('website_id')) {
	// 	// 		Mage::throwException(
	// 	// 			Mage::helper('vendor')->__('Vendor website ID must be specified when using the website scope')
	// 	// 		);
	// 	// 	}
	// 	// 	$bind['website_id'] = (int)$vendor->getWebsiteId();
	// 	// 	$select->where('website_id = :website_id');
	// 	// }
	// 	echo '<pre>';
	// 	$vendorId = $adapter->fetchOne($select, $bind);
	// 	print_r($vendorId);
	// 	die();
	// 	if ($vendorId) {
	// 		$this->load($vendor, $vendorId);
	// 	} else {
	// 		$vendor->setData(array());
	// 	}

	// 	return $this;
	// }
}

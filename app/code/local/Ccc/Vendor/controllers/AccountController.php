<?php
class Ccc_Vendor_AccountController extends Mage_Core_Controller_Front_Action
{

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('loginPost', 'createpost');

    /**
     * Retrieve vendor session model object
     *
     */
    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    /**
     * Action predispatch
     *
     * Check vendor authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = strtolower($this->getRequest()->getActionName());
        $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'changeforgotten',
            'resetpassword',
            'resetpasswordpost',
            'confirm',
            'confirmation'
        );
        $pattern = '/^(' . implode('|', $openActions) . ')/i';

        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

    /**
     * Action postdispatch
     *
     * Remove No-referer flag from vendor session after each action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $this->_getSession()->unsNoReferer(false);
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('vendor/account_dashboard')
        );
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Account'));
        $this->renderLayout();
    }
    public function loginAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function createAction()
    {

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }
    public function createPostAction()
    {
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));

        if (!$this->_validateFormKey()) {
            $this->_redirectError($errUrl);
            return;
        }

        /** @var $session Mage_Vendor_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->_redirectError($errUrl);
            return;
        }

        $vendor = $this->_getVendor();


        try {

            //$errors = $this->_getVendorErrors($vendor);
            $data = $this->getRequest()->getPost();
            unset($data['form_key']);
            unset($data['error_url']);
            unset($data['success_url']);
            $vendor->cleanPasswordsValidationData();
            $vendor->setData($data);
            // echo 1;
            // die();
            // print_r($email = $vendor->getEmail());
            // $vendorModel = Mage::getModel('vendor/vendor')->getCollection();
            // $vendorModel->addFieldToFilter('email', $email);
            // $vendorModel->addFieldToFilter('entity_id', $vendor->getID());
            // $vendorModel = $vendorModel->getSelect();

            // print_r($vendorModel->getData());
            // die;


            $vendor->save();



            $this->_dispatchRegisterSuccess($vendor);
            $this->_successProcessRegistration($vendor);
            return;
        } catch (Mage_Core_Exception $e) {

            $session->setVendorFormData($this->getRequest()->getPost());
            if ($e->getCode() === Ccc_Vendor_Model_Vendor::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('vendor/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
            } else {
                $message = $this->_escapeHtml($e->getMessage());
            }
            // $session->addError($message);
        } catch (Exception $e) {
            $session->setVendorFormData($this->getRequest()->getPost());
            $session->addException($e, $this->__('Cannot save the vendor.'));
        }

        $this->_redirectError($errUrl);
    }
    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }
    protected function _getVendor()
    {
        $vendor = $this->_getFromRegistry('current_vendor');
        if (!$vendor) {
            $vendor = $this->_getModel('vendor/vendor')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $vendor->setIsSubscribed(1);
        }
        /**
         * Initialize vendor group id
         */
        $vendor->getGroupId();

        return $vendor;
    }
    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }
    protected function _escapeHtml($text)
    {
        return Mage::helper('core')->escapeHtml($text);
    }
    protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }
    protected function _getVendorErrors($vendor)
    {
        $errors = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnVendorAddress($vendor);
        }
        print_r($request);
        $vendorForm = $this->_getVendorForm($vendor);

        $vendorData = $vendorForm->extractData($request);

        $vendorErrors = $vendorForm->validateData($vendorData);

        if ($vendorErrors !== true) {
            $errors = array_merge($vendorErrors, $errors);
        } else {
            $vendorForm->compactData($vendorData);
            $vendor->setPassword($request->getPost('password'));
            $vendor->setPasswordConfirmation($request->getPost('confirmation'));
            $vendorErrors = $vendor->validate();
            if (is_array($vendorErrors)) {
                $errors = array_merge($vendorErrors, $errors);
            }
        }
        return $errors;
    }
    protected function _getVendorForm($vendor)
    {
        /* @var $vendorForm Mage_Vendor_Model_Form */
        $vendorForm = $this->_getModel('vendor/form');

        $vendorForm->setFormCode('vendor_account_create');
        $vendorForm->setEntity($vendor);
        return $vendorForm;
    }
    protected function _dispatchRegisterSuccess($vendor)
    {
        Mage::dispatchEvent(
            'vendor_register_success',
            array('account_controller' => $this, 'vendor' => $vendor)
        );
    }
    protected function _successProcessRegistration(Ccc_Vendor_Model_Vendor $vendor)
    {
        $session = $this->_getSession();
        if ($vendor->isConfirmationRequired()) {
            /** @var $app Mage_Core_Model_App */
            $app = $this->_getApp();
            /** @var $store  Mage_Core_Model_Store*/
            $store = $app->getStore();
            $vendor->sendNewAccountEmail(
                'confirmation',
                $session->getBeforeAuthUrl(),
                $store->getId(),
                $this->getRequest()->getPost('password')
            );
            $vendorHelper = $this->_getHelper('vendor');
            $session->addSuccess($this->__(
                'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                //$vendorHelper->getEmailConfirmationUrl($vendor->getEmail())
            ));
            $url = $this->_getUrl('*/*/index', array('_secure' => true));
        } else {
            $session->setVendorAsLoggedIn($vendor);
            $url = $this->_welcomeVendor($vendor);
        }
        $this->_redirectSuccess($url);
        return $this;
    }
    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }
    protected function _getApp()
    {
        return Mage::app();
    }
    protected function _welcomeVendor(Ccc_Vendor_Model_Vendor $vendor, $isJustConfirmed = false)
    {
        $this->_getSession()->addSuccess(
            $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );


        $vendor->sendNewAccountEmail(
            $isJustConfirmed ? 'confirmed' : 'registered',
            '',
            Mage::app()->getStore()->getId(),
            $this->getRequest()->getPost('password')
        );

        $successUrl = $this->_getUrl('*/*/index', array('_secure' => true));
        if ($this->_getSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }
    protected function _addSessionError($errors)
    {
        $session = $this->_getSession();
        $session->setVendorFormData($this->getRequest()->getPost());
        if (is_array($errors)) {
            foreach ($errors as $errorMessage) {
                $session->addError($this->_escapeHtml($errorMessage));
            }
        } else {
            $session->addError($this->__('Invalid vendor data'));
        }
    }

    public function loginPostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/');
            return;
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session = $this->_getSession();

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);

                    if ($session->getVendor()->getIsJustConfirmed()) {
                        $this->_welcomeVendor($session->getVendor(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Ccc_Vendor_Model_Vendor::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('vendor')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('vendor')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Ccc_Vendor_Model_Vendor::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $session->addError($message);
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose vendor password
                }
            } else {
                $session->addError($this->__('Login and password are required.'));
            }
        }
        $this->_loginPostRedirect();
    }
    protected function _loginPostRedirect()
    {
        $session = $this->_getSession();
        // print_r($session->getBeforeAuthUrl());
        // echo '<br/>';
        // print_r(Mage::getBaseUrl());
        // die();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect vendor to
            $session->setBeforeAuthUrl($this->_getHelper('vendor')->getAccountUrl());
            // Redirect vendor to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag(
                    Ccc_Vendor_Helper_Data::XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD
                )) {
                    $referer = $this->getRequest()->getParam(Ccc_Vendor_Helper_Data::REFERER_QUERY_PARAM_NAME);
                    if ($referer) {
                        // Rebuild referer URL to handle the case when SID was changed
                        $referer = $this->_getModel('core/url')
                            ->getRebuiltUrl($this->_getHelper('core')->urlDecodeAndEscape($referer));
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {

                $session->setBeforeAuthUrl($this->_getHelper('vendor')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() ==  $this->_getHelper('vendor')->getLogoutUrl()) {
            $session->setBeforeAuthUrl($this->_getHelper('vendor')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        $this->_redirectUrl($session->getBeforeAuthUrl(true));
    }


    public function editAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->_initLayoutMessages('catalog/session');

        $block = $this->getLayout()->getBlock('vendor_edit');

        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $data = $this->_getSession()->getVendorFormData(true);
        $vendor = $this->_getSession()->getVendor();
        if (!empty($data)) {
            $vendor->addData($data);
        }
        if ($this->getRequest()->getParam('changepass') == 1) {
            $vendor->setChangePassword(1);
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('Account Information'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }


    public function editPostAction()
    {

        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/edit');
        }
        if ($this->getRequest()->isPost()) {
            $this->getRequest()->getParam('id');
            $vendorObj = $this->_getVendor();

            $vendorData = $this->getRequest()->getPost();
            unset($vendorData['form_key']);
            unset($vendorData['dummy']);
            if (array_key_exists('change_password', $vendorData)) {
                $vendorData['password'] = $vendorData['newpassword'];
                unset($vendorData['change_password']);
            }
            unset($vendorData['newpassword']);
            unset($vendorData['confirmation']);
            $vendorObj->cleanPasswordsValidationData();
            $vendorObj->setData($vendorData);

            $vendorObj->save();
            $this->_getSession()->setVendor($vendorObj)
                ->addSuccess($this->__('The account information has been saved.'));
            $this->_redirect('vendor/account');
            return;

            die();
            /** @var $vendor Mage_Vendor_Model_Vendor */
            $vendor = $this->_getSession()->getVendor();
            $vendor->setOldEmail($vendor->getEmail());
            /** @var $vendorForm Mage_Vendor_Model_Form */
            $vendorForm = $this->_getModel('vendor/form');
            $vendorForm->setFormCode('vendor_account_edit')
                ->setEntity($vendor);

            // echo '<pre>';
            // print_r($vendorForm);
            // die();

            // } else {

            //     $vendorForm->compactData($vendorData);
            $errors = array();

            // if (!$vendor->validatePassword($this->getRequest()->getPost('current_password'))) {
            //     $errors[] = $this->__('Invalid current password');
            // }

            // If email change was requested then set flag
            // $isChangeEmail = ($vendor->getOldEmail() != $vendor->getEmail()) ? true : false;
            // $vendor->setIsChangeEmail($isChangeEmail);

            // // If password change was requested then add it to common validation scheme
            // $vendor->setIsChangePassword($this->getRequest()->getParam('change_password'));

            // if ($vendor->getIsChangePassword()) {
            //     $newPass    = $this->getRequest()->getPost('password');
            //     $confPass   = $this->getRequest()->getPost('confirmation');

            //     if (strlen($newPass)) {
            //         /**
            //          * Set entered password and its confirmation - they
            //          * will be validated later to match each other and be of right length
            //          */
            //         $vendor->setPassword($newPass);
            //         $vendor->setPasswordConfirmation($confPass);
            //     } else {
            //         $errors[] = $this->__('New password field cannot be empty.');
            //     }
            // }

            // Validate account and compose list of errors if any
            // $vendorErrors = $vendor->validate();
            // if (is_array($vendorErrors)) {
            //     $errors = array_merge($errors, $vendorErrors);
            // }
            //}

            if (!empty($errors)) {
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost());
                foreach ($errors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit');
                return $this;
            }

            try {
                $vendor->cleanPasswordsValidationData();

                // Reset all password reset tokens if all data was sufficient and correct on email change
                if ($vendor->getIsChangeEmail()) {
                    $vendor->setRpToken(null);
                    $vendor->setRpTokenCreatedAt(null);
                }

                $vendor->save();
                $this->_getSession()->setVendor($vendor)
                    ->addSuccess($this->__('The account information has been saved.'));

                if ($vendor->getIsChangeEmail() || $vendor->getIsChangePassword()) {
                    $vendor->sendChangedPasswordOrEmail();
                }

                $this->_redirect('vendor/account');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setVendorFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save the vendor.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
    public function logoutAction()
    {
        $session = $this->_getSession();
        $session->logout()->renewSession();

        if (Mage::getStoreConfigFlag(Ccc_Vendor_Helper_Data::XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $session->setBeforeAuthUrl(Mage::getBaseUrl());
        } else {
            $session->setBeforeAuthUrl($this->_getRefererUrl());
        }
        $this->_redirect('*/*/logoutSuccess');
    }

    /**
     * Logout success page
     */
    public function logoutSuccessAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}

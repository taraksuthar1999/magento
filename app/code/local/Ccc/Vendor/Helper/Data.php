<?php

class Ccc_Vendor_Helper_Data extends Mage_Core_Helper_Abstract
{
    const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * Route for vendor account login page
     */
    const ROUTE_ACCOUNT_LOGIN = 'vendor/account/login';

    /**
     * Config name for Redirect Vendor to Account Dashboard after Logging in setting
     */
    const XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD = 'vendor/startup/redirect_dashboard';

    /**
     * Config paths to VAT related vendor groups
     */
    const XML_PATH_VENDOR_VIV_INTRA_UNION_GROUP = 'vendor/create_account/viv_intra_union_group';
    const XML_PATH_VENDOR_VIV_DOMESTIC_GROUP = 'vendor/create_account/viv_domestic_group';
    const XML_PATH_VENDOR_VIV_INVALID_GROUP = 'vendor/create_account/viv_invalid_group';
    const XML_PATH_VENDOR_VIV_ERROR_GROUP = 'vendor/create_account/viv_error_group';

    /**
     * Config path to option that enables/disables automatic group assignment based on VAT
     */
    const XML_PATH_VENDOR_VIV_GROUP_AUTO_ASSIGN = 'vendor/create_account/viv_disable_auto_group_assign_default';

    /**
     * Config path to support email
     */
    const XML_PATH_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    /**
     * WSDL of VAT validation service
     *
     */
    const VAT_VALIDATION_WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService?wsdl';

    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_VENDOR_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
    = 'default/vendor/password/reset_link_expiration_period';

    /**
     * Configuration path to require admin password on vendor password change
     */
    const XML_PATH_VENDOR_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD
    = 'vendor/password/require_admin_user_to_change_user_password';

    /**
     * Configuration path to password forgotten flow change
     */
    const XML_PATH_VENDOR_FORGOT_PASSWORD_FLOW_SECURE = 'admin/security/forgot_password_flow_secure';
    const XML_PATH_VENDOR_FORGOT_PASSWORD_EMAIL_TIMES = 'admin/security/forgot_password_email_times';
    const XML_PATH_VENDOR_FORGOT_PASSWORD_IP_TIMES    = 'admin/security/forgot_password_ip_times';

    /**
     * VAT class constants
     */
    const VAT_CLASS_DOMESTIC    = 'domestic';
    const VAT_CLASS_INTRA_UNION = 'intra_union';
    const VAT_CLASS_INVALID     = 'invalid';
    const VAT_CLASS_ERROR       = 'error';



    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }

    /**
     * Retrieve parameters of vendor login url
     *
     * @return array
     */
    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (
            !$referer && !Mage::getStoreConfigFlag(self::XML_PATH_VENDOR_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('vendor/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }

    /**
     * Retrieve vendor login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)
            );
        }
        return $this->_getUrl('vendor/account/loginPost', $params);
    }

    /**
     * Retrieve vendor logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('vendor/account/logout');
    }

    /**
     * Retrieve vendor dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    /**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    /**
     * Retrieve vendor register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_getUrl('vendor/account/create');
    }

    /**
     * Retrieve vendor register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('vendor/account/createpost');
    }

    /**
     * Retrieve vendor account edit form url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_getUrl('vendor/account/edit');
    }

    /**
     * Retrieve vendor edit POST URL
     *
     * @return string
     */
    public function getEditPostUrl()
    {
        return $this->_getUrl('vendor/account/editpost');
    }

    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_getUrl('vendor/account/forgotpassword');
    }

    /**
     * Check is confirmation required
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        return $this->getVendor()->isConfirmationRequired();
    }

    /**
     * Retrieve confirmation URL for Email
     *
     * @param string $email
     * @return string
     */
    public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('vendor/account/confirmation', array('email' => $email));
    }

    /**
     * Check whether vendors registration is allowed
     *
     * @return bool
     */
    public function isRegistrationAllowed()
    {
        $result = new Varien_Object(array('is_allowed' => true));
        Mage::dispatchEvent('vendor_registration_is_allowed', array('result' => $result));
        return $result->getIsAllowed();
    }
    public function getVendor()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getVendor();
        }
        return $this->_customer;
    }
}

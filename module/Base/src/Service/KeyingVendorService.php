<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\KeyingVendorAdapter;
use Zend\Authentication\AuthenticationService;

class KeyingVendorService extends BaseService
{
    
    const VENDOR_ALL = 'All';
    const VENDOR_LN = 'LexisNexis';
    
    const SRC_USER_FORM = 'User';
    const SRC_SEARCH_USERS_FORM = 'Search Users';
    const SRC_VIEW_KEYED_FORM = 'View Keyed Reports';
    
    const SRC_METRICS_ISBA_FORM = 'Image Status By Agency';
    const SRC_METRICS_OBASF_FORM = 'Operator By Agency Stats';
    const SRC_METRICS_OKA_FORM = 'Operator Keying Accuracy';
    const SRC_METRICS_OSS_FORM = 'Operator Summary Stats';
    const SRC_METRICS_VSBO_FORM = 'VIN Status By Operator';
    const SRC_METRICS_VSS_FORM = 'VIN Status Summary';
    const SRC_METRICS_SSS_FORM = 'SLA Status Summary';
    const SRC_METRICS_AEA_FORM = 'Auto Extraction Accuracy';
    
    const SRC_AUTOEXTRACTMETRICS_AEA = 'Auto Extraction Accuracy';
    const SRC_AUTOEXTRACTMETRICS_AER = 'Auto Extraction Report';
    const SRC_AUTOEXTRACTMETRICS_VPR = 'Volume Productivity Report';
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\KeyingVendorAdapter
     */
    protected $adapterKeyingVendor;
    
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    protected $serviceAuth;
    
    public function __construct(
        Array $config,
        Logger $logger,
        KeyingVendorAdapter $adapterKeyingVendor,
        AuthenticationService $serviceAuth)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterKeyingVendor = $adapterKeyingVendor;
        $this->serviceAuth = $serviceAuth;
    }
    
    /**
     * Get keying vendor details by id
     * @param int $keyingVendorId
     * @return array keying vendor information
     */
    public function fetchKeyingVendorById($keyingVendorId)
    {
        if (empty($keyingVendorId)) {
            return;
        }
        try {
            return $this->adapterKeyingVendor->getKeyingVendorById($keyingVendorId);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    /**
     * Get keying vendor details by name
     * @param string $keyingVendorName
     * @return array keying vendor information
     */
    public function fetchKeyingVendorByName($keyingVendorName)
    {
        if (empty($keyingVendorName)) {
            return;
        }
        try {
            return $this->adapterKeyingVendor->getKeyingVendorByName($keyingVendorName);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    /**
     * Get allowed keying vendors
     * @param $excludeList list of vendors to exclude
     * @return array    Allowed keying vendors array
     */
    public function fetchKeyingVendorNamePairs($excludeList = null)
    {
         try {
            return $this->adapterKeyingVendor->getKeyingVendorNamePairs($excludeList);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at ' . $e->getFile() . ' on line ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            
            return [];
        }// @codeCoverageIgnoreStart
    }
    
    /**
     * Checks if the logged in user is an LN User
     * @return boolean $isLNUser
     */
    public function isLoggedInLNUser()
    {
        $isLNUser = false;
        $loggedInUser = $this->serviceAuth->getIdentity();
        if (!empty($loggedInUser) && !empty($loggedInUser->vendorName) && 
                $loggedInUser->vendorName == KeyingVendorService::VENDOR_LN) {
            $isLNUser = true;
        }
        return $isLNUser;
    }
    
    /**
     * Checks if the logged in user has the same keying vendor id as the one in 
     * the request. This ensures that only the same user can see the list intended
     * just for them
     * @param int $requestkeyingVendorId
     * @return boolean $isUserSameVendor
     */
    public function isLoggedInSameVendor($requestkeyingVendorId)
    {
        if (empty($requestkeyingVendorId)) {
            return;
        }
        $isUserSameVendor = false;
        $loggedInUser = $this->serviceAuth->getIdentity();
        if (!empty($loggedInUser) && !empty($loggedInUser->keyingVendorId) && 
                $loggedInUser->keyingVendorId == $requestkeyingVendorId) {
            $isUserSameVendor = true;
        }
        return $isUserSameVendor;
    }
}

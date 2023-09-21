<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Admin\Controller;

use Zend\Log\Logger;
use Zend\Session\Container;
use SoapFault;

use Base\Helper\LnHelper;
use Base\Controller\BaseController;

class InvalidVinController extends BaseController
{
    /**
     * Amount of the VIN first characters should be replaced during SOAP VIN validation
     * when this validation runs from invalid VIN queue
     */
    const VIN_REPLACEMENT_CHARS_INVALID_VIN_QUEUE = 11;

    /**
     * Amount of the VIN first characters should be replaced during SOAP VIN validation
     * when this validation runs from passes
     */
    const VIN_REPLACEMENT_CHARS_PASS = 6;
    
    /**
     * @var Array
     */
    protected $config;

     /**
     * @var Zend\Session\Container
     */
    protected $session;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Helper\LnHelper
     */
    protected $lnHelper;
    
    public function __construct(
        Array $config,
        Container $session,
        Logger $logger,
        LnHelper $lnHelper)
    {
        $this->config = $config;
        $this->session = $session;
        $this->logger = $logger;
        $this->lnHelper = $lnHelper;

        parent::__construct();
    }
    
    /*
     * Function to list out the potential matches in the third pass dataentry.
     *
     * Multiple 'potential VIN matches' can be returned if the first 6 characters of a VIN
     * have the following substitution(s):
     *
     * EXIST_CHAR   REPLACE_CHAR    REPLACE
     * Y    4   0 or 1 boolean
     * 4    Y   0 or 1 boolean
     * X    K   0 or 1 boolean
     * K    X   0 or 1 boolean
     * 4    H   0 or 1 boolean
     * H    4   0 or 1 boolean
     * 5    S   0 or 1 boolean
     * S    5   0 or 1 boolean
     * Z    2   0 or 1 boolean
     * 2    Z   0 or 1 boolean
     * G    6   0 or 1 boolean
     * 6    G   0 or 1 boolean
     * U    V   0 or 1 boolean
     * V    U   0 or 1 boolean
     * U    4   0 or 1 boolean
     * 4    U   0 or 1 boolean
     * 8    B   0 or 1 boolean
     * B    8   0 or 1 boolean
     * F    E   0 or 1 boolean
     * E    F   0 or 1 boolean
     * T    J   0 or 1 boolean
     * J    T   0 or 1 boolean
     * R    K   0 or 1 boolean
     * K    R   0 or 1 boolean
     * M    N   0 or 1 boolean
     * N    M   0 or 1 boolean
     *
     */
    public function potentialMatchJsonAction()
    {
        $result = null;

        if ($this->validateCsrfToken($this->request->getQuery('csrfToken'))) {
            $vin = $this->request->getQuery('vin');
            $result = $this->potentialMatches($vin);
        } else {
            $result = $this->getInvalidCSRFJsonResponse();
        }
        
        return $this->json->setVariables($result);
    }

    protected function potentialMatches($vin)
    {
        $timeStart = microtime(true);
        $vehicles = null;
        try {
            //@todo fix check for validInputVin (from WS)
            $this->logger->log(Logger::INFO, "Potential Match - VIN WebService Call STARTED");
            
            // depending whether VIN validaton running from Invalid VIN Queue or from passes,
            // we determine the amount of characters which can be replaced
            $vinLocation = $this->request->getQuery('vinLocation');
            $replaceFirstNOnly = ($vinLocation == 'invalidVinQueue') ?
                self::VIN_REPLACEMENT_CHARS_INVALID_VIN_QUEUE :
                self::VIN_REPLACEMENT_CHARS_PASS;
            $vehicles = $this->lnHelper->GeneralVINWebService($vin, $replaceFirstNOnly);
            $this->logger->log(Logger::INFO, "Potential Match - VIN WebService Call: " . $vin . " - SUCCESS");
        }
        catch (SoapFault $fault) {
            $this->logger->log(Logger::ERR, "Potential Match - VIN WebService Call: " . $vin . " - FAILURE - " . $fault->getMessage());
            return null;
        }
        
        $this->logger->log(Logger::ERR, "Potential Match - Webservice call took " . (microtime(true) - $timeStart) . " seconds");
        $count = count($vehicles);
        
        $result = [
            'originalVIN' => $vin,
            'count' => $count
        ];
        $result['vehicles'] = $vehicles;

        return $result;
    }
}

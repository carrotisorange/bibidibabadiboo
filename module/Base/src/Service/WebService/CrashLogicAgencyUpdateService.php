<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service\WebService;

use Zend\Log\Logger;
use Exception;

use Base\Service\BaseService;

class CrashLogicAgencyUpdateService extends BaseService
{    
    
    protected static $instance = null; /* @var $instance UsersExportToAuditHelperService */
    /**
     * @var Array
     */
    private $config;
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    protected $crashLogicConnInfo;

    protected $xml_post_string = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"
                                    xmlns:tem="http://tempuri.org/"
                                    xmlns:app="http://schemas.datacontract.org/2004/07/Appriss.CrashLogic.Common.Services.DataContracts">
                            <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
                                <wsa:Action>http://tempuri.org/IImport/UploadAgencyInfo</wsa:Action>
                                <wsa:To>%serviceUrl%</wsa:To>
                            </soap:Header>
                            <soap:Body>
                                <tem:UploadAgencyInfo>
                                    <tem:request>
                                        <app:Name>%agnName%</app:Name>
                                        <app:Ori>%agnOri%</app:Ori>
                                        <app:RedactDateOfBirth>%dob%</app:RedactDateOfBirth>
                                        <app:RedactDriversLicense>%dl%</app:RedactDriversLicense>
                                        <app:RedactPhoneNumber>%phn%</app:RedactPhoneNumber>
                                        <app:State>%state%</app:State>
                                    </tem:request>
                                </tem:UploadAgencyInfo>
                            </soap:Body>
                        </soap:Envelope>';
    
    
    public function __construct(
        Array $config,
        Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->crashLogicConnInfo = ["url"=>$config['app']['crashlogic']['wsdl'], "uname"=>$config['app']['crashlogic']['login'], "pwd"=>$config['app']['crashlogic']['password']];
    }

    public function updateAgencyInfo($agencyName, $agencyOri, $redactDob, $redactDL, $redactPhNbr, $state)
    {
        $this->logger->log(Logger::INFO, 'Inside updateAgencyInfo');
        try {
            $this->xml_post_string = str_replace("%serviceUrl%", $this->crashLogicConnInfo['url'], $this->xml_post_string);
            $this->xml_post_string = str_replace("%agnName%", $agencyName, $this->xml_post_string);
            $this->xml_post_string = str_replace("%agnOri%", $agencyOri, $this->xml_post_string);
            $this->xml_post_string = str_replace("%dob%", $redactDob, $this->xml_post_string);
            $this->xml_post_string = str_replace("%dl%", $redactDL, $this->xml_post_string);
            $this->xml_post_string = str_replace("%phn%", $redactPhNbr, $this->xml_post_string);
            $this->xml_post_string = str_replace("%state%", $state, $this->xml_post_string);
            $headers = [
                "Content-type: application/soap+xml;charset=UTF-8",
            ]; 

            $this->logger->log(Logger::INFO, 'Going to call CrashLogic redaction webservice. ');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->crashLogicConnInfo['url']);
            curl_setopt($ch, CURLOPT_USERPWD, $this->crashLogicConnInfo['uname'] . ":" . $this->crashLogicConnInfo['pwd']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml_post_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            // tell curl to return the result content instead of outputting it
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            // execute the request
            $output = curl_exec($ch);
            $info = curl_getinfo($ch);
            
            if (curl_errno($ch)) {
                // this would be first hint that something went wrong
                $this->logger->log(Logger::ERR, 'Exception:  Error occured while sending request to crashLogic for redaction '.$agencyName.':'.$agencyOri.':'.$redactDob.':'.$redactDL.':'.$redactPhNbr.':'.$state.' .' . curl_error($ch));
                return false;
            } 
            else {
                // check the HTTP status code of the request
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    $this->logger->log(Logger::INFO, 'Redaction information succesfully sent over to crashLogic ');
                    return true;
                } else {
                    $this->logger->log(Logger::ERR, 'Exception:  Unsuccessful return code from Crashlogic while trying to send over redaction info. Status Code: '.$resultStatus.':'.$agencyName.':'.$agencyOri.':'.$redactDob.':'.$redactDL.':'.$redactPhNbr.':'.$state.' .' .$output .':'.$info);
                    return false;
                }
            }
            curl_close($ch);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: while calling Crashlogic Redaction webservice. ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } 
        return false;
    }
    
}

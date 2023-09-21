<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Soap\Client;

class LnHelper extends AbstractHelper
{
    /**
     * @var Array
     */
    private $config;
    
    public function __construct(Array $config = null)
    {
        $this->config = $config;
    }
    
    /**
     * To get the correct IP address of the client.
     * @return string IP address of a client OR when it comes to $_SERVER['HTTP_X_FORWARDED_FOR'] it can potentially
     * return a comma delimited list of IPs that would look like: clientIp, proxy1IP, proxy2IP, ...
     */
    public function getClientIP()
    {
        if (!empty($_COOKIE['SOURCE_IP'])) {
            $ip = $_COOKIE['SOURCE_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    //LN Password Generation function
    public function generatePassword($length = 8, $level = 2)
    {
        list($usec, $sec) = explode(' ', microtime());
        srand((float) $sec + ((float) $usec * 100000));
        
        $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
        $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";
        
        $password = "";
        $counter = 0;
        
        while ($counter < $length) {
            $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level]) - 1), 1);
            
            // All character must be different
            if (!strstr($password, $actChar)) {
                $password .= $actChar;
                $counter ++;
            }
        }
        
        return $password;
    }

    /**
     * Validates VIN and returns matches by running SOAP request to VIN validation service. If
     * there are no records for the particular VIN - returns empty array, otherwise
     * returns array with matched records (with fields Make, Model, Year, VIN)
     * @param string $proposedVin
     * @param integer $replaceFirstNOnly
     * @return array
     */
    public function GeneralVINWebService($proposedVin, $replaceFirstNOnly) 
    {
        $options = [
            'encoding' => $this->config['app']['soap']['encoding'],
            'login' => $this->config['vinLogin'],
            'password' => $this->config['vinPassword'],
            'soap_version' => $this->config['app']['soap']['version'],
            //need to do this since verisign has self-signed certificate
            'stream_context'=> stream_context_create(
                [
                    'ssl'=> [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]
            )
        ];
        
        $client = new Client($this->config['vinWSDL'], $options);
        
        $params = [
            'DecodeBy' => ['VIN' => strtoupper($proposedVin)],
            'Options' => ['ReturnPossibleVINs' => '1']
        ];

        if (!empty($replaceFirstNOnly)){
            $params['Options']['ReplaceFirstNOnly'] = $replaceFirstNOnly;
        }
        
        //@TODO: Should be removed in Ln prod
        if (APPLICATION_ENV == 'local') {
            $soapResponse = [];
        } else {
            $soapResponse = $client->VINDecode($params);
        }
        
        if (empty($soapResponse->response->Records->Record)) { //Make, Model, Year not returned
            //Invalid VIN
            return [];
        }
        
        $records = $soapResponse->response->Records->Record;
        if ( !is_array($records) ) {
            $records = [$records];
        }
        
        $vehicles = [];
        foreach ($records as $vehicle) {
            $model = '';
            if (!empty($vehicle->VinaData->ModelFull)) {
                $model = $vehicle->VinaData->ModelFull;
            } else if (!empty($vehicle->Model)) {
                $model = $vehicle->Model;
            }

            $vehicles[] = [
                'VIN' => (string) $vehicle->VIN, //Explicit conversion used to convert NULL into empty string
                'Year' => isset($vehicle->Year) ? $vehicle->Year : '', //Default numeric
                'Make' => (string) $vehicle->Make,
                'Model' => (string) $model,
            ];
        }
        
        return $vehicles;
    }
}

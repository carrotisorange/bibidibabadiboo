<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

$configParent = [];

if (file_exists(__DIR__ . '/prod.php')) {
    $configParent = include __DIR__ . '/prod.php';
}

$configEnv = [
    // PHP ini configuration
    'ini_set' => [
        // Display error configuration option
        'display_startup_errors' => '0',
        'display_errors' => '0',
    ],
    
    // Zend page not found and exception configuration
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
    ],
    
    'app' => [
        'ipRestrict' => [
            'wsdl' => 'http://idsloginapp-bct.risk.regn.net/idslogin/wsipr',
        ],
        'riagAuthWsdl'  => 'https://mbsws.prg.risk.regn.net/RIAGAuth/ws_riagauth11.asmx?WSDL',
        'maeAuthWsdl'   => 'https://mbsws.prg.risk.regn.net/ws_mbsauth_ext/ws_mbsauth_ext10.asmx?wsdl',
        'mbsAuthWsdl'   => 'https://mbsws.prg.risk.regn.net/BillingSystemsMBS/ws_mbsauth.asmx?wsdl',
        'lnaaAuthUrl'   => 'https://lnaaws.risk.regn.net/LNAA/LNAAv10/',
    ],
    
    'vinWSDL'   => 'http://iutility.ins.risk.regn.net:7170/WsUtility/VINDecode?wsdl&ver_=3.26',
    'imageWSDL' => [
        //richard keller said this is the Prod/DR auto failover url. It will point to either the prod or the dr server that is active
        'url' => 'https://rsapp.ins.risk.regn.net/rs/api/docserv/soap/v3/docservices.wsdl',
        //this is the actual DR url pointing to the DR servers just in case
        //'url' => "http://rsapp-bct.rs.lexisnexis.net/rs/api/storage/soap/ImageServerWSService.wsdl"
    ],    
    'smtp' => [
        'host' => 'appmail.risk.regn.net',
        'port' => 25
    ],
    'reportPagesForIncreasedMemory' => 150,
    'memoryLimitForBigReports' => '3072M',
    'activeFormsDuration' => '-3 months' //last or previous 3 months
];

return array_replace_recursive($configParent, $configEnv);
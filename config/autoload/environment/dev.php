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
        'display_startup_errors' => '1',
        'display_errors' => '1',
        
        'soap.wsdl_cache_enabled' => '0',
    ],
    
    // Zend page not found and exception configuration
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
    ],
    
    'app' => [
       'crashlogic' => [
            'wsdl' => 'https://prep.getcrashreports.com:8081/Import.svc/basic',
            'soap' => [
                'version' => '2',
            ],
        ],
        'ipRestrict' => [
            'wsdl' => 'http://idsloginsvcs-dev.risk.regn.net/idslogin/wsipr',
        ],
        'riagAuthWsdl'  => 'https://mbswsdev.risk.regn.net/RIAGAuth/ws_riagauth.asmx?WSDL',
        'maeAuthWsdl'   => 'https://mbswsdev.risk.regn.net/ws_mbsauth_ext/ws_mbsauth_ext10.asmx?WSDL',
        'mbsAuthWsdl'   => 'https://mbswsdev.risk.regn.net/BillingSystemsMBS/ws_mbsauth.asmx?WSDL',
        'lnaaAuthUrl'   => 'https://mbswsdev.risk.regn.net/LNAA/LNAAv10/',
        'cdi' => [
            'filePrefix'    => 'TECrash_IN_',
        ],
        'vinPlate' => [
            'accountNumber' => '515487',
        ],
        'isit' => [
            'userInfo' => [
                'environment'   => 'Dev',
            ],
        ],
    ],
    
    'vinWSDL'   => 'http://ecrash-qa.sc.seisint.com:7170/WsUtility/VINDecode?wsdl&ver_=3.26',
    'vinPlateWSDL'  => 'http://10.194.9.67:8886/WsInsurance/VINPlate?wsdl&&ver_=2&flrec_details',
    
    'imageWSDL' => [
        'url' => 'https://rsapp-dev.rs.lexisnexis.net/rs/api/docserv/soap/v3/docservices.wsdl',
    ],
    
    'resources' => [
        'log' => [
            'stream' => [
                'filterParams' => [
                    'priority' => 'DEBUG'
                ],
            ],
        ],
    ],
    
    'registration' => [
        'password'  => 'InnovateCash3#'
    ],
    
    'internalUserExceptions' => ['salgpa01@risk'],
    
    'agencycontribsource' => [
        'mail' => [
            'to' => 'paul.salgado@lexisnexis.com',
        ]
    ],    
    'smtp' => [
        'host' => 'appmail-test.risk.regn.net',
        'port' => 25
    ],
    'reportPagesForIncreasedMemory' => 150,
    'memoryLimitForBigReports' => '3072M',
    'activeFormsDuration' => '-3 months' //last or previous 3 months
];

return array_replace_recursive($configParent, $configEnv);
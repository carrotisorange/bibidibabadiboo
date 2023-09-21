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
            'wsdl' => 'http://idsloginsvcs-qa.risk.regn.net/idslogin/wsipr',
        ],
        'riagAuthWsdl'  => 'https://mbswsqa.risk.regn.net/RIAGAuth/ws_riagauth.asmx?WSDL',
        'maeAuthWsdl'   => 'https://mbswsqa.risk.regn.net/ws_mbsauth_ext/ws_mbsauth_ext10.asmx?WSDL',
        'mbsAuthWsdl'   => 'https://mbswsqa.risk.regn.net/BillingSystemsMBS/ws_mbsauth.asmx?WSDL',
        'lnaaAuthUrl'   => 'https://services-legacy-qa.us-mbs-nonprod.azure.lnrsg.io/LNAA/LNAAv10/',
        'cdi' => [
            'filePrefix'    => 'TECrash_IN_',
        ],
        'vinPlate' => [
            'accountNumber' => '515487',
        ],
        'isit' => [
            'userInfo' => [
                'environment'   => 'QA',
            ],
        ],
    ],
    
    'vinWSDL'   => 'http://ecrash-qa.sc.seisint.com:7170/WsUtility/VINDecode?wsdl&ver_=3.26',
    'imageWSDL' => [
        'url' => 'https://rsapp-qc.rs.lexisnexis.net/rs/api/docserv/soap/v3/docservices.wsdl',
    ],
    'vinPlateWSDL'  => 'http://10.194.3.3:5000/WsInsurance/VINPlate?wsdl&&ver_=2&flrec_details',
    
    'internalUserExceptions' => [
        'salgpa01@risk', 'lazaredx@risk', 'beaswi01@risk', 'kandasy1@risk', 
        'medisa01@risk', 'rabueu01@risk', 'gonzan01@risk', 'enaced01@risk', 
        'adepma01@risk', 'mattsm01@risk'],
    
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
    
    'agencycontribsource' => [
        'mail' => [
            'to' => 'Samuel.Medina@lexisnexisrisk.com',
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

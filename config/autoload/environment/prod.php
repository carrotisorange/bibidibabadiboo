<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

return [
    // PHP ini configuration
    'ini_set' => [
        // Display error configuration option
        'display_errors' => '0',
        'display_startup_errors' => '0',
    ],
    
    // Zend page not found and exception configuration
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
    ],
    
    'siteurl' => APPLICATION_SITEURL,
    'logging' => [
        'enabled' => true,
        'jobs' => [
            'dir' => LOG_PATH,
        ]
    ],
    'app' => [
        'keying' => [
            'cleanup' => [
                'timeLength' => 3,
                'timeUnit' => 'HOUR'
            ]
        ],
        'env' => APPLICATION_ENV,
        'crashlogic' => [
            'wsdl' => 'https://www.getcrashreports.com:8081/Import.svc/basic',
            'soap' => [
                'version' => '2'
            ],
        ],
        'soap' => [
            'encoding' => 'UTF-8',
            'version' => '1'
        ],
        'log' => [
            'path' => LOG_PATH,
            'file_path' => LOG_FILE_PATH,
        ],
        'cdi' => [
            'filePath'    => DATA_PATH . '/../cdi/incoming',
            'filePrefix'  => 'PECrash_IN_'
        ],
        'user' => [
            'passwordExpiration'             => '60',  // Days
            'generatedPasswordExpiration'    => '24',  // Hours
            'maxIdleDays'                    => '90',  // User will be blocked if he had not been logged into the system within this amount of days
            'passwordChangeInterval'         => '7',   // user can not change his password more often then once in this amount of days
            'maxLoginAttemptCount'           => '100'   // user will be blocked after he enters wrong password this amount of times            
        ],
        'riagAuthWsdl'  => 'https://mbsws.prg.risk.regn.net/RIAGAuth/ws_riagauth.asmx?WSDL',
        'maeAuthWsdl' => 'https://mbsws.prg.risk.regn.net/ws_mbsauth_ext/ws_mbsauth_ext10.asmx?WSDL',
        'mbsAuthWsdl' => 'https://mbsws.prg.risk.regn.net/BillingSystemsMBS/ws_mbsauth.asmx?WSDL',
        'lnaaAuthUrl' => 'https://lnaaws.risk.regn.net/LNAA/LNAAv10/',
        'ipRestrict' => [
            'wsdl' => 'http://idsloginapp.risk.regn.net/idslogin/wsipr',
            'applicationIdentifier' => 'ECRASH'
        ],
        'webService' => [
            'messageQueue' => [
                'domain' => 'isit.lexisnexisrisk.net',
                'appName' => 'ECrash Keying',
                'ecrashAppId' => '4',
                'httpSecure' => true,
            ]
        ],
        'isit' => [
            'userInfo' => [
                'userType' => 'Internal LN',
                'domain' => 'No Domain',
                'isThisRequestForYou' => 'No',
                'cloneExistingUser' => 'No',
                'userIdType' => 'Permanent',
                'internalUserStatus' => 'Employee/Contractor',
                'audience' => 'Internal LN',
                'appLocation' => 'US',
                'environment' => 'Production',
            ]
        ],
        'auditTool' => [
            'userList' => [
                'email' => 'audit-app@risk.lexisnexis.com'
            ],
            'nonHumanUserList' => [
                'email' => 'sysaudit@lexisnexis.com'
            ],
            'reviewerId' => '143611', // people soft user id of the reviewer for audit records (143611 - Bipin Jha)
        ],
        'reportEntry' => [
            'queue_distinct_limit' => 400, //limit for queue
            'select_retry_limit' => 25 // no of time retry should happen
        ],
    ],
    
    'vinWSDL'       => 'http://iutility.ins.risk.regn.net:7170/WsUtility/VINDecode?wsdl&ver_=3.26',
    'vinPlateWSDL'  => 'http://iespnonfcra.ins.risk.regn.net:5000/WsInsurance/VINPlate?wsdl&&ver_=2&flrec_details',
    
    'imageWSDL' => [
        'url' => 'https://rsapp.ins.risk.regn.net/rs/api/docserv/soap/v3/docservices.wsdl',
        'timeout' => 300 //seconds
    ],
    
    'retrieveImageFromWebService' => [
        'enabled' => true
    ],
    
    // Session
    'session' => [
        'cookie'        => 'ECRKEYINSESSID',
        'cookietimeout' => '12', //Hours
        'timeout'       => 30, // In minutes(30). How long to keep the session alive.
        'timeoutWarning' => 10, // In minutes(10). Alert notification window will be open automatically for session timeout.
    ],
    
    'registration' => [
        'user'      => 'adminecrkeyin',
        'password'  => 'M@ng0123',
        'domain'    => 'ecrkeyin',
        'mbsrole'   => 'ECRKEYIN_KEYUSER'
    ],

    'internalDomains' => ['risk', 'lexisnexis', 'lexisnexisrisk', 'noam', 'relx', 'reedelsevier', 'elsevier'],
    
    'internalUserExceptions' => [],
    
    'lnaa' => [
        'registration' => [
            'domain'=> 'mbs'
        ],
    ],
    
    'mbs' => [
        'registration' => [
            'domain'=> 'mbs'
        ],
    ],
    
    'adminrole' => [
        'adminecrkeyin' => 'ECRKEYIN_ADMIN',
        'domainecrkeyin'=> 'MBSAUTHEXT_DOMAINADMIN_ECRKEYIN',
        'domainmbs'     => 'MBSAUTHEXT_DOMAINADMIN_MBS'
    ],
    
    // Pagination
    'pagination' => [
        'perpage' => 30,
    ],
    
    'cdi' => [
        'dir' => [
            'logging'    => '/ap/ecrash/log/keying',
        ]
    ],
    
    'flashmessage' => [
        'timeout' => 10,
    ],
    'viewPath' => 'reportview',
    'paginatorPath' => 'paginator',
    'autoExtractionEnabled' => 1,
    'agencycontribsource' => [
        'graceperiod' => 10,
        'mail' => [
            'templatePath' => 'agencycontribsourceincidents',
            'template' => 'agencycontribsourceincidents.phtml',
            'subject' => 'Agency Contributory Source Updates: Incidents Affected',
            'to' => 'eCrash.DevOncall@lexisnexis.com',
            // Newly added to send email
            'from' => 'ecrash-keying@lexisnexis.com',
        ]
    ],    
    'smtp' => [
        'host' => 'appmail.risk.regn.net',
        'port' => 25
    ],
    'reportPagesForIncreasedMemory' => 150,
    'memoryLimitForBigReports' => '3072M',
    'activeFormsDuration' => '-3 months' //last or previous 3 months
];

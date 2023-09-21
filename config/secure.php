<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

return [
    'db' => [
        'master' => [
            'driver'    => 'Pdo_Mysql',
            'host'      => 'localhost',
            'dbname'    => 'ecrash_v3.1_uat',
            'username'  => 'root',
            'password'  => '',
            'port'      => '',
            'charset'   => 'utf8'
        ],

        'keying_autoextract' => [
            'driver'    => 'Pdo_Mysql',
            'host'      => 'localhost',
            'dbname'    => 'keying_autoextract',
            'username'  => 'root',
            'password'  => '',
            'port'      => '',
            'charset'   => 'utf8'
        ],
        
  'mbs' => [
            'driver'    => 'Pdo_Mysql',
            'host'      => '',
            'dbname'    => '',
            'username'  => '',
            'password'  => '',
            'port'      => '3308',
            'charset'   => 'utf8'
        ],
    ],
    
    'app' => [
        'db' => [
            'alias' => [
                'master'    => 'master',
                'slave'     => 'master',
            ],
        ],
        'webService' => [
           'messageQueue' => [
                'isitLogin'    => '',
                'isitPassword' => '',
            ],
        ],
        'crashlogic' => [
            'login'    => '',
            'password' => '',
        ],
    ],
    
    'vinLogin'      => '',
    'vinPassword'   => '',
    
    'vinPlateLogin'     => '',
    'vinPlatePassword'  => '',   

 'mbs' => [
        'registration' => [
            'user'      => '',
            'password'  => '',
        ],
    ],
 
 'lnaa' => [
        'registration' => [
            'user'      => '',
            'password'  => '',
        ],
    ],
    
    'imageWSDL' => [
        'user' => '',
        'password' => ''
    ]     
];


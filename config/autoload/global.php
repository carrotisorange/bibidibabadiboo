<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;

return [
    'app_name' => 'eCrash Keying',
    // Session configuration.
    'session_config' => [
        'cookie_lifetime'     => 60 * 60 * 1, // Session cookie will expire in 1 hour.
        'gc_maxlifetime'      => 60 * 60 * 24 * 10, // How long to store session data on server (for 10 days).
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ],
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory', 
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];

<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Set the Application path
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
}

define('APP_ENV_LOCAL', 'local');

if (!defined('APPLICATION_ENV')) {
    if (getenv('APPLICATION_ENV')) {
        define('APPLICATION_ENV', getenv('APPLICATION_ENV'));
    } elseif (file_exists(APPLICATION_PATH . '/config/environment.php')) {
        define('APPLICATION_ENV', include(APPLICATION_PATH . '/config/environment.php'));
    } else {
        define('APPLICATION_ENV', APP_ENV_LOCAL);
    }
}

// Make the URL Dynamic to what the HTTP HOST header is.
if (!empty($_SERVER['HTTP_HOST'])) {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) || !empty($_SERVER['HTTP_X_VARNISH'])) {
        // this request went through the load balancer, which means we're probably an HTTPS connection on the front-end.
        $_SERVER['HTTPS'] = true;
    }
    
    define('APPLICATION_SITEURL', 'http' . (!empty($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/');
} else {
    define('APPLICATION_SITEURL', 'http://' . php_uname('n'));
}

// Data storage path of application
$dataPath = initDirectory(APPLICATION_PATH . '/data', 0775);
if (!defined('DATA_PATH')) {
    define('DATA_PATH', $dataPath);
}

// Session storage path
//$sessionPath = initDirectory(DATA_PATH . '/session', 0775);
$sessionPath = initDirectory('/ap/ecrash/session/keying', 0775);
//$sessionPath = initDirectory('/datahub_dump/Keying/sessions', 0777);
if (!empty($sessionPath)) {
    ini_set('session.save_path', $sessionPath);
}

// Define application log path
//$logPath = initDirectory(APPLICATION_PATH . '/log', 0777);
$logPath = initDirectory('/ap/ecrash/log/keying', 0777);
if (!defined('LOG_PATH')) {
    define('LOG_PATH', $logPath);
}

$currentDate = date("Ymd");
$fileMode = "a+";
$filePermission = 0777;
// Make sure application log file is exists or not. If not exists then create application log file.
$appLogFilePath = initFile(LOG_PATH . '/application_'. $currentDate .'.log', $fileMode, $filePermission);

// Define application log file path
if (!defined('LOG_FILE_PATH')) {
    define('LOG_FILE_PATH', $appLogFilePath);
}

// Make sure php log file is exists or not. If not exists then create php error log file inside the LOG_PATH.
$phpErrorFilePath = initFile(LOG_PATH . '/php_error_'. $currentDate .'.log', $fileMode, $filePermission);
if (!empty($phpErrorFilePath)) {
    ini_set("error_log", $phpErrorFilePath);
}

if (!defined('APPLICATION_PUBLIC_PATH')) {
    define('APPLICATION_PUBLIC_PATH', initDirectory(APPLICATION_PATH . '/public', 0775));
}

// Report form template path
if (!defined('REPORTFORM_TEMPLATE_PATH')) {
    $reportFormTemplatePath = initDirectory(DATA_PATH . '/forms', 0644);
    define('REPORTFORM_TEMPLATE_PATH', $reportFormTemplatePath);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
$application = Application::init($appConfig);

/**
 * Function to create the directory with the given permission
 *
 * @param string    $directoryPath  Path to be initialize
 * @param int       $permission     Permission to be assigned if directory not exists.
 * @return string                   Return absolute path of the directory
 */
function initDirectory($directoryPath, $permission = 0755)
{
    if ((!file_exists($directoryPath)) && (!mkdir($directoryPath, $permission, true))) {
        echo 'The ' . $directoryPath . ' has not been set up properly. Please be sure checkout initialization has been run.';
        exit(0);
    }
    
    return realpath($directoryPath);
}

/**
 * Function to create a file with the given permission
 *
 * @param string    $filePath       File to be created if not exists
 * @param string    $mode           Mode of the file to be created
 * @param integer   $permission     Permission of the file to be created
 * @return string                   Return absolute path of the file
 */
function initFile($filePath, $mode = 'a+', $permission = null)
{
    if (!file_exists($filePath)) {
        $filePointer = fopen($filePath, $mode);
        if (!empty($filePointer)) {
            fclose($filePointer);
            if (!empty($permission)) {
                chmod($filePath, $permission);
            }
        } else {
            echo 'The ' . $filePath . ' has not been set up properly.';
            exit(0);
        }
    }
    
    return realpath($filePath);
}

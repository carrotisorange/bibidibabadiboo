<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

class EcrashUtilsArrayService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    public function __construct(
        Array $config,
        Logger $logger)
    {
        $this->config   = $config;
        $this->logger   = $logger;
    }
    
    /**
     * Implode a nested array using the key to an element within the nested arrays
     * @param string $glue
     * @param array $arr
     * @param string $key
     * @return array
     */
    public function implodeAlt( $glue, $arr, $key )
    {
        try {
            $pieces = [];
            foreach ( $arr as $el ) {
                if ( isset( $el[ $key ] ) ) {
                    $pieces[] = $el[ $key ];
                }
            }
            return implode( $glue, $pieces );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $msg = 'Origin: ' . $origin . '; Exception while doing alt implosion on array: ' . $e->getMessage();
            $this->logger->log(Logger::ERR, $msg);
            return [];
        } // @codeCoverageIgnoreEnd
    }
    
}

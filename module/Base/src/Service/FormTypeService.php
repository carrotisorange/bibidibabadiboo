<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;

use Base\Adapter\Db\FormTypeAdapter;

class FormTypeService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\FormTypeAdapter
     */
    protected $adapterAgency;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormTypeAdapter $adapterFormType)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterFormType = $adapterFormType;
    }
    
   /**
     * Select all Form types
     * 
     * @return array all form types, else return empty array; on exception of failure
     */
    public function fetchAllFormTypes()
    {   
        return $this->adapterFormType->fetchAllFormTypes();
    }
    
    /**
     * Select all Form types only used in keying
     * 
     * @return array all form types only used in keying, else return empty array; on exception of failure
     */
    public function fetchAllKeyedFormTypes()
    {   
        return $this->adapterFormType->fetchAllKeyedFormTypes();
    }
}

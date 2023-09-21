<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class BaseAdapter
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @param array $config Application configuration
     */
    public function __construct(Array $config)
    {
        $this->config = $config;
        $this->dbMaster = $this->config['app']['db']['alias']['master'];
        $this->dbSlave = $this->config['app']['db']['alias']['slave'];
    }
    
    /**
     * To provide the master database adapter
     * @return object Zend\Db\Adapter\Adapter
     */
    public function getMasterDbAdapter()
    {
        return new Adapter($this->config['db'][$this->dbMaster]);
    }
    
    /**
     * To provide the mbs database adapter
     * @return object Zend\Db\Adapter\Adapter
     */
    public function getMbsDbAdapter()
    {
        return new Adapter($this->config['db']['mbs']);
    }

    /**
     * To provide the slave database adapter
     * @return object Zend\Db\Adapter\Adapter
     */
    public function getSlaveDbAdapter()
    {
        return new Adapter($this->config['db'][$this->dbSlave]);
    }

    public function getAutozoningDbAdapter() {
        return new Adapter($this->config['db']['keying_autoextract']);
    }

}

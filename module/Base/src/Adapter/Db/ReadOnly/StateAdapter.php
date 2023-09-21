<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;

class StateAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'state';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    public function fetchStatesWithReports()
    {
        $select = $this->getSelect();
        $select->from(['sta' => 'state']);
        $select->columns(['state_id' => new Expression('DISTINCT sta.state_id'), 'name_full' => 'sta.name_full'], false);
        $select->join(["frm" => "form"], "sta.state_id = frm.state_id", []);
        $select->join(["rep" => "report"], "frm.form_id = rep.form_id", []);
        return $this->fetchAll($select);
    }
}

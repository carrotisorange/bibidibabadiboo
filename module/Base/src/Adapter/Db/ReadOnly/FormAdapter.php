<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\ReadOnly;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

use Base\Service\EntryStageService;
use Base\Adapter\Db\DbAbstract;

class FormAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function fetchFormsWithReports()
    {
        $select = $this->getSelect();
        $select->from(['frm' => 'form']);
        $select->columns(['form_id' => $this->getDistinct('frm.form_id'), 'name_external' => 'frm.name_external' ], false);
        $select->join(["rep" => "report"], "frm.form_id = rep.form_id", []);

        return $this->fetchAll($select);
    }


}

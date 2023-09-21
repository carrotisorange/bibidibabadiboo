<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Base\Service\KeyingVendorService;

class AutozoningDataCoordinateAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'autozoning_coordinates_data';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function getCoordinateData($reportId) {
        $columns = [
            'autozoning_data' => new Expression('uncompress(autozoning_data)'),
        ];

        $select = $this->getSelect();
        $select->from($this->table);
        $select->columns($columns);
        $select->where(['report_id' => $reportId]);
        return $this->fetchOne($select);
    }
    public function test() {
        echo 'test';
    }
    
}

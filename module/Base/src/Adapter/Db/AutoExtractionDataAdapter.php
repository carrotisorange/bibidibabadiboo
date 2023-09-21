<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Zend\Db\Sql\Select;

class AutoExtractionDataAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'auto_extraction_data';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function getExtractedData($reportId)
    {
        $columns = [
            'entryData' => 'entry_data',
        ];

        $select = $this->getSelect();
        $select->from(['ae' => $this->table]);
        $select->columns($columns);
        $select->join(['aeip' => 'auto_extraction_image_process'], 'aeip.report_id = ae.report_id', []);
        $select->where(['ae.report_id' => $reportId]);
        $select->where(['aeip.ml_response' => 1]);
        return $this->fetchRow($select);
    }
    
    /**
     * Get narrative data
     *
     * @param int $reportId
     * @return string narrative data
     */
    public function getNarrativeData($reportId)
    {
        $columns = [
            'narrative' => new Expression('uncompress(narrative)'),
        ];

        $select = $this->getSelect();
        $select->from(['ae' => $this->table]);
        $select->columns($columns);
        $select->join(['aeip' => 'auto_extraction_image_process'], 'aeip.report_id = ae.report_id', []);
        $select->where(['ae.report_id' => $reportId]);
        $select->where(['aeip.ml_response' => 1]);
        return $this->fetchOne($select);
    }

}

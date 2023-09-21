<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Log\Logger;
use Zend\Db\Sql\Select;

class AutoExtractionImageProcessAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'auto_extraction_image_process';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function hasAutoExtracted($reportId)
    {
        $select = $this->getSelect();
        $select->from(['aeip' => $this->table]);
        $select->columns(['ml_response' => 'ml_response']);
		$select->join(['aed' => 'auto_extraction_data'], 'aed.report_id = aeip.report_id', []);
        $select->where(['aeip.report_id' => $reportId]);
		
        return $this->fetchOne($select);
    }

    public function updateHandwrittenReport($reportId)
    {
        return $this->update(['is_handwritten' => 1], ['report_id' => $reportId]);
    }
}

<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class ReportEntryDataValueAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'report_entry_data_value';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
	
	public function insertField(
		$reportEntryId,
		$fieldId,
		$value)
	{
		return $this->insert([
			'report_entry_id' => $reportEntryId,
			'form_field_common_id' => $fieldId,
			'value' => $value
		]);
	}
}
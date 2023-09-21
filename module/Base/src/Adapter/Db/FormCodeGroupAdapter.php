<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class FormCodeGroupAdapter extends DbAbstract
{
    /**
     * Table name
     * @var string Table name
     */
    protected $table = 'form_code_group';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
     
    public function insertGroup( $desc )
    {
        return $this->insert([
            'description' => $desc
        ]);
    }
        
    public function fetchValueListNames( $formCodeGroupId )
    {
        $sql = "
			SELECT fcl.name 
			FROM form_code_list_group_map fclgm
			JOIN form_code_list fcl
			ON fclgm.form_code_list_id = fcl.form_code_list_id
			WHERE form_code_group_id = :form_code_group_id
		";

        $bind = [
            'form_code_group_id' => $formCodeGroupId,
        ];

        return $this->getAdapter()->fetchAll( $sql, $bind );
    }

    /**
     * Get a lookup map for mapping values to descriptions or descriptions to values for a particular form code list entity type. 
     * @param int $formCodeGroupId
     * @param [string] $formCodeListName ('injuryStatus') any valid code list from form code list table
     * @return array
     * Note: SQL Logic that follows here must closely match the ReportEntryController _getFormValueLists logic used by keying app.
     */
    public function fetchCodeListMap( $formCodeGroupId, $formCodeListName = 'injuryStatus' )
    {
        $sql = "SELECT DISTINCT
				fcl.name AS codeMapName,
				fcp.code,
				fcp.description
			FROM form_code_group AS fcg
				JOIN form_code_list_group_map AS fclgm USING (form_code_group_id)
				JOIN form_code_list AS fcl USING (form_code_list_id)
				JOIN form_code_list_pair_map USING (form_code_list_id)
				JOIN form_code_pair AS fcp USING (form_code_pair_id)
			WHERE fcg.form_code_group_id = :form_code_group_id
            AND fcl.name = :fcl_name
			ORDER BY fcl.name, fcp.code, fcp.description";

        $bind = [
            'form_code_group_id' => $formCodeGroupId,
            'fcl_name' => $formCodeListName,
        ];

        return $this->getAdapter()->fetchAll( $sql, $bind );
    }           

}

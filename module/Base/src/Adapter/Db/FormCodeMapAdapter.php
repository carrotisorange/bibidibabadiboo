<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Log\Logger;

class FormCodeMapAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_code_map';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }

    public function getAllFormCodePairs($formCodeGroupId, $codeMapName = null)
    {
        $sql = "
            SELECT * FROM(
                (SELECT
                    fcl.name AS codeMapName,
                    fcp.code,
                    fcp.description,
                    fclpm.child_class_name
                FROM form_code_group AS fcg
                    JOIN form_code_list_group_map AS fclgm USING (form_code_group_id)
                    JOIN form_code_list AS fcl USING (form_code_list_id)
                    JOIN form_code_list_pair_map AS fclpm USING (form_code_list_id)
                    JOIN form_code_pair AS fcp USING (form_code_pair_id)
                WHERE fcg.form_code_group_id = :form_code_group_id)
                UNION
                (SELECT 'City' as `codeMapName`, `city_name` as code, `state_abbr` as description, '' as child_class_name from city) 
            ) as codeMap
            ORDER BY codeMapName, code, description
        ";
        $bind = [
            'form_code_group_id' => $formCodeGroupId,
        ];
        $valueLists = [];

        foreach ($this->adapter->createStatement($sql, $bind)->execute() as $row) {
            $valueLists[$row['codeMapName']][$row['code']] = [
                'value' => $row['description'],
                'class_name' => $row['child_class_name']
            ];
        }

        foreach (array_keys($valueLists) as $key) {
           uksort($valueLists[$key], 'strnatcmp');
        }

        if (!empty($codeMapName)) {
            return $valueLists[$codeMapName];
        } else {
            return $valueLists;
        }
    }

    public function getFieldMultiselectCodePairs($formCodeGroupId)
    {
        $columns = [
            'codeMapName' => 'fcl.name',
            'code' => 'fcp.code',
            'description' => 'fcp.description'
        ];

        $select = $this->getSelect()
            ->columns($columns, false)
            ->from(['fcg' => 'form_code_group'])
            ->join(['fclgm' => 'form_code_list_group_map'], 'fclgm.form_code_group_id = fcg.form_code_group_id', [])
            ->join(['fcl' => 'form_code_list'], 'fcl.form_code_list_id = fclgm.form_code_list_id', [])
            ->join(['fclpm' => 'form_code_list_pair_map'], 'fclpm.form_code_list_id = fcl.form_code_list_id', [])
            ->join(['fcp' => 'form_code_pair'], 'fcp.form_code_pair_id = fclpm.form_code_pair_id', [])
            ->where('fcg.form_code_group_id = :form_code_group_id')
			->where('fcl.is_multiselect = 1');
        
        $bind = [
            'form_code_group_id' => $formCodeGroupId
        ];

        $fieldCodePairs = $this->fetchAll($select, $bind);
        $valueLists = [];

        foreach ($fieldCodePairs as $row) {
            $valueLists[$row['codeMapName']][$row['code']] = $row['description'];
        }

        foreach (array_keys($valueLists) as $key) {
           uksort($valueLists[$key], 'strnatcmp');
        }

        return $valueLists;
    }
}

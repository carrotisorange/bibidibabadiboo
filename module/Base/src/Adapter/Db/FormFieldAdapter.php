<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

class FormFieldAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_field';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    public function getCommonMapByFormSystemName($formSystemName, $commonPathAsKey = true, $groupByKey = false)
    {
        $columns = [
            'keyPath' => new Expression('LOWER(CONCAT_WS("/", ff.path, ff.name))'),
            'fullPathCommon' => new Expression('CONCAT_WS("/", ffc.path, ffc.name)'),
            'fullPathVendor' => new Expression('CONCAT_WS("/", ff.path, ff.name)'),
            'isEnum' => 'is_enum',
            'isIncludedInMetadata' => 'is_included_in_metadata'
        ];
        if ($commonPathAsKey) {
            $columns['keyPath'] = new Expression('LOWER(CONCAT_WS("/", ffc.path, ffc.name))');
        }
        
        $select = $this->getSelect();
        $select->from(['ff' => $this->table]);
        $select->columns($columns);
        $select->join(['ffc' => 'form_field_common'], 'ff.form_field_common_id = ffc.form_field_common_id', []);
        $select->join(['fs' => 'form_system'], 'fs.form_system_id = ff.form_system_id', []);
        $select->where('fs.name_internal = :name_internal');
        $bind = ['name_internal' => $formSystemName];
        $result = [];

        if ($groupByKey) {
            $rawRecords = $this->fetchAll($select, $bind);
            foreach ($rawRecords as $record) {
                $result[$record['keyPath']][] = $record;
            }
        } else {
            $result = $this->fetchAssoc($select, $bind);
        }

        return $result;
    }
    
    public function getCodeDescriptionPairFieldsByFormSystemId($formSystemId) {
        $columns = [
            'name' => $this->getDistinct('name')
        ];
        $select = $this->getSelect();
        $select->from(['ff' => $this->table]);
        $select->columns($columns);
        $select->where([
            'ff.is_code_value_pair = 1',
            'ff.form_system_id = :form_system_id'
        ]);
        $bind = ['form_system_id' => $formSystemId];
        return $this->fetchCol($select, $bind);
    }

    public function getCriticalityFieldsByFormSystemId($formSystemId) {
        $columns = [
            'name' => new Expression('LOWER(name)'),
            'is_critical' => 'is_critical',
            'is_major' => 'is_major',
            'is_minor' => 'is_minor'
        ];
        $select = $this->getSelect();
        $select->from(['ff' => $this->table]);
        $select->columns($columns);
        $select->where([
            'ff.form_system_id = :form_system_id'
        ]);
        $select->where('ff.is_critical = 1 OR ff.is_major = 1 OR ff.is_minor = 1');
        $bind = ['form_system_id' => $formSystemId];
        return $this->fetchAssoc($select, $bind);
    }
}
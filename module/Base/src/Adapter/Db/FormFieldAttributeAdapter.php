<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Exception;

class FormFieldAttributeAdapter extends DbAbstract
{
    /**
     * @var string
     * @var string Table name
     */
    protected $table = 'form_field_attribute';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
        $this->adapter = $adapter;
    }
    
    /**
     * Gets basic information about a form group id
     *
     * @param integer $reportId
     * @return array
     */
    public function fetchByGroupId($formFieldAttributeGroupId)
    {
        $select = $this->getSelect();
        $select->from(['ffa' => $this->table]);
        $select->columns([
            'isAvailable' => "is_available",
            'isSkipped' => "is_skipped",
            'isRequired' => "is_required"
        ]);
        
        $select->join(['ff' => 'form_field'], 'ffa.form_field_id = ff.form_field_id', ["isEnum" => "is_enum"]);
        $select->join(['ffc' => 'form_field_common'], 'ff.form_field_common_id = ffc.form_field_common_id', ['fieldName' => "name"]);
        $select->where('ffa.form_field_attribute_group_id = :form_field_attribute_group_id');
        $bind = ['form_field_attribute_group_id' => $formFieldAttributeGroupId];
        $select->order('ffa.tab_index ASC');

        return $this->fetchAll($select, $bind);
    }

    public function getTabOrder($formFieldAttrGroupId) {
        $select = $this->getSelect();
        $select->from(['ffa' => $this->table]);
        $select->columns([
            'fieldId' => "form_field_id",
            'tabIndex' => "tab_index"
        ]);
        $select->join(['ff' => 'form_field'], 'ff.form_field_id = ffa.form_field_id', [], Select::JOIN_LEFT);
        $select->join(['ffc' => 'form_field_common'], 'ff.form_field_common_id = ffc.form_field_common_id', ['fieldName' => "name", 'path'], Select::JOIN_LEFT);
        $select->where('ffa.form_field_attribute_group_id = :form_field_attribute_group_id');
        $bind = ['form_field_attribute_group_id' => $formFieldAttrGroupId];
        $tabOrder = [];
        $fieldAttributes = $this->fetchAll($select, $bind);
        foreach($fieldAttributes as $attrInfo) {
            $tabOrder[$attrInfo['tabIndex']] = $attrInfo['fieldName'];
        }

        return $tabOrder;
    }

    public function updateAttributesByGroupId($formFieldAttrGroupId, $elements)
    {
        $this->adapter->getDriver()->getConnection()->beginTransaction();
        try {
            // Because the form does not supply id numbers directly, we need to figure out what they are.
            $selectFormFieldAttributeIdsByTabOrder = $this->getSelect();
            $selectFormFieldAttributeIdsByTabOrder->where('form_field_attribute_group_id = :form_field_attribute_group_id');
            $selectFormFieldAttributeIdsByTabOrder->order('tab_index ASC');
            $bind = ['form_field_attribute_group_id' => $formFieldAttrGroupId];
            $formFieldAttributesByTabOrder = $this->fetchAll($selectFormFieldAttributeIdsByTabOrder, $bind);

            foreach ($elements as $elementIndex => $element) {
                $formId = $formFieldAttributesByTabOrder[$elementIndex]['form_field_attribute_id'];
                $available = (isset($element['available']) ? 1 : 0);
                $skipped = 0;
                $required = 0;
                if (isset($element['selection'])) {
                    if ($element['selection'] == "REQUIRED") {
                        $skipped = 0;
                        $required = 1;
                    } else if ($element['selection'] == "SKIPPED") {
                        $skipped = 1;
                        $required = 0;
                    }
                }

                $updateData = [
                    'is_available' => $available,
                    'is_skipped' => $skipped,
                    'is_required' => $required,
                ];

                $this->update($updateData, ['form_field_attribute_id' => $formId]);
            }

            $this->adapter->getDriver()->getConnection()->commit();

        } catch (Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollBack();
            throw $e;
        }
    }
}
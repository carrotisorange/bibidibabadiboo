<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

use Base\Service\EntryStageService;

class FormAdapter extends DbAbstract
{
    
    const UNIVERSAL_FORM = 'Universal V1.1';
    const CRU_FORM = 'CRU Go Forward';
    
    /**
     * @var string
     * Table name
     */
    protected $table = 'form';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
		$this->adapter = $adapter;
    }
    
    public function fetchFormsWithReports()
    {
        $select = $this->getSelect();
        $select->from(['frm' => 'form']);
        $select->columns(['form_id' => $this->getDistinct('frm.form_id'), 'name_external' => new Expression('frm.name_external') ]);
        $select->join(["rep" => "report"], "frm.form_id = rep.form_id", []);
        return $this->fetchAll($select);
    }
    
    /**
     * Get form details
     * @param int $formId
     * @return array Form details
     */
    public function getFormInfo($formId)
    {
        $sql = "
            SELECT
                f.form_id AS formId,
                f.form_type_id AS formTypeId,
                f.state_id AS stateId,
                f.agency_id AS agencyId,
                f.form_template_id AS formTemplateId,
                f.form_field_attribute_group_id AS formFieldAttributeGroupId,
                f.name_external AS nameExternal,
                f.name_vendor AS nameVendor,
                s.name_abbr AS stateAbbr,
                s.name_full AS stateName,
                ft.code AS formType,
                ft.description AS formTypeDescription,
                fte.name_internal AS formTemplate,
                fte.name_external AS formTemplateExternal,
                fs.name_internal AS formSystem,
                a.name AS formAgency
            FROM form AS f
                LEFT JOIN state AS s USING (state_id)
                JOIN form_type AS ft USING (form_type_id)
                LEFT JOIN form_template AS fte USING (form_template_id)
                LEFT JOIN form_system AS fs USING (form_system_id)
                LEFT JOIN agency AS a USING (agency_id)
            WHERE f.form_id = :form_id
        ";
        
        $bind = ['form_id' => $formId];
        
        return $this->fetchRow($sql, $bind);
    }
    
    /**
     * To get list of agency based on the selected state/agency
     * @param int $stateId
     * @param int $agencyId
     * @return array, List of agency
     */
    public function getFormIdNamePairs($stateId, $agencyId, $duration)
    {
        $select = $this->getSelect();
        $select->from(['f' => 'form']);
        $select->join(['r' => 'report'], 'f.form_id = r.form_id', [], 'Left');
        $select->join(['ft' => 'form_type'], 'ft.form_type_id = f.form_type_id', [], 'Left');
        $select->join(['s' => 'state'], 's.state_id = f.state_id', [], 'Left');
        $select->join(['ftp' => 'form_template'], 'ftp.form_template_id = f.form_template_id', [], 'Left');
        $select->join(['a' => 'agency'], 'a.agency_id = f.agency_id', [], 'Left');
        
        $columns = [
            'formId' => $this->getDistinct('f.form_id'),
            'formName' => 'f.name_external',
            'formType' => 'ft.description',
            'formState' => 's.name_abbr',
            'formTemplate' => 'ftp.name_external',
            'formAgency' => 'a.name'
        ];
        $select->columns($columns, false);
        
        $bind = [];
        if (!empty($stateId)) {
            $select->where('f.state_id = :state_id');
            $bind['state_id'] = $stateId;
        }
        
        if (!empty($agencyId)) {
            $select->where('f.agency_id = :agency_id');
            $bind['agency_id'] = $agencyId;
        }
        
        $select->where('f.is_active = 1');
        
        $select->where('f.name_external IN (:universal_form, :cru_form)');
        $bind['universal_form'] = self::UNIVERSAL_FORM;
        $bind['cru_form'] = self::CRU_FORM;
        
        if (!empty($duration)) {
            $select->where('r.date_created >= :duration');
            $bind['duration'] = date('Y-m-d H:i:s', strtotime($duration));
        }
        
        return $this->fetchAll($select, $bind);
    }
    
    public function getAllowedFormPairs($userId, $stateId, $workTypeId, $includeAgencySpecificForms)
    {
        $select = $this->getSelect();
        $select->from(['u' => 'user']);
        $select->columns([]);
        $select->join(['ues' => 'user_entry_stage'], 'ues.user_id = u.user_id', []);
        $select->join(['es' => 'entry_stage'], 'es.entry_stage_id = ues.entry_stage_id', []);
        $select->join(['ufp' => 'user_form_permission'], 'ufp.user_id = u.user_id', []);
        $select->join(['f' => 'form'], 'f.form_id = ufp.form_id', ['formId' => 'form_id', 'nameExternal' => 'name_external']);
        $select->where([
            'u.user_id = :user_id',
            'f.state_id = :state_id',
            'es.name_internal = :name_internal',
            'ufp.work_type_id = :work_type_id',
            'f.is_active = 1'
        ]);

        if (empty($includeAgencySpecificForms)) {
            $select->where('f.agency_id IS NULL');
        }

        $bind = [
            'user_id' => $userId,
            'state_id' => $stateId,
            'name_internal' => EntryStageService::STAGE_ALL,
            'work_type_id' => $workTypeId
        ];

        return $this->fetchPairs($select, $bind);
    }

    public function fetchTemplateNameInternal($formId)
    {
        $select = $this->getSelect();
        $select->from(['frm' => $this->table]);
        $select->columns([]);
        $select->join(['tpl' => 'form_template'], 'frm.form_template_id = tpl.form_template_id', ['name_internal']);
        $select->where('frm.form_id = :form_id');
        $bind = ['form_id' => $formId];

        return $this->fetchRow($select, $bind);
    }

    public function updateAttributeGroup($formId, $attributeGroupId)
    {
        return $this->update(
            ['form_field_attribute_group_id' => $attributeGroupId], ['form_id' => $formId]
        );
    }

    /**
     * Create or update agency forms
     * @param numeric|int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false) Create incident forms when true; else create crash form
     * @param numeric|int $stateId 
     * @param [bool] $activateForms (false) if true will activate forms after creating new form records.
     * @return bool
     */
    public function createOrUpdateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId, $activateForms = false)
    {
        try {
            $fnStatus = true;
            /*
             * We insert the new forms disabled/in-active and let the activate function handle activation because we 
             * have a FK constraint that will allow enabling only one form with same form type per agency is active.
             */
            $formIsActive = '0';
            $sql = "
                insert into form(form_type_id, state_id, agency_id, form_template_id, entry_stage_process_group_id, 
                    form_field_attribute_group_id, name_external, is_active) 
                select ft.form_type_id, a.state_id, a.agency_id, 
                    (select form_template_id from form_template where name_internal='universal-sectional'), 
                    (select entry_stage_process_group_id from entry_stage_process_group 
                        where description = '2 Pass'), 
                    (select form_field_attribute_group_id from form_field_attribute_group 
                        where description = 'Universal V1.1 Fields'), 
                    (select name_external from form_template where name_internal='universal-sectional'), {$formIsActive} 
                        from form_type ft, agency a 
                        where a.mbsi_agency_id = :mbsAgencyId 
                        and ft.form_type_id NOT IN ( 
                            select f.form_type_id from form f join form_type ft on f.form_type_id = ft.form_type_id 
                            and f.agency_id = (select agency_id from agency where mbsi_agency_id = :mbsAgencyId) and 
                            f.form_template_id = (select form_template_id from form_template 
                            where name_internal='universal-sectional') 
                )";
            $insertSql = $this->addWhereToFormsSql($sql, $isIncidentForms);
            $bind = [
                'mbsAgencyId' => $mbsAgencyId
            ];

            $pdo = $this->adapter->query($insertSql, $bind);
            $tf = $this->pdoQueryOperationStatus($pdo);
            if (!$tf) {
                // @codeCoverageIgnoreStart
                $fnStatus = false;
            }
            // @codeCoverageIgnoreEnd

            if ($activateForms) {
                $tf = $this->activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId);
                if (!$tf) {
                    // @codeCoverageIgnoreStart
                    $fnStatus = false;
                }
                // @codeCoverageIgnoreEnd
            }

            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }
    }
        // @

    /**
     * Add appropriate where condition to $sql for incident forms or crash form
     * @param string $sql 
     * @param bool|mixed $isIncidentForms (true|false|0|1) true|1|non-empty == incident forms; false|0|empty == crash forms
     * @return string
     */
    protected function addWhereToFormsSql($sql, $isIncidentForms)
    {
        if (!empty($isIncidentForms)) {
            $sql .= " AND ft.allowed_incident=1 ";
        } else {
            $sql .= " AND ft.code='A' ";
        }

        return $sql;
    }

    /**
     * Emit a portion of SQL used as select qualifier for form selection based on form type id
     * @param bool|mixed $isIncidentForms (true|false|0|1) true|1|non-empty == incident forms; false|0|empty == crash forms
     * @return string
     * Note: assumes bind info in caller logic has the mbsAgencyId set
     */
    protected function genFormTypeIdSql($isIncidentForms)
    {
        $formTypeIdSql = "
                f.form_type_id IN (
                    SELECT DISTINCT f.form_type_id from form f join form_type ft on f.form_type_id = ft.form_type_id AND
                        f.agency_id = ( select agency_id from agency where mbsi_agency_id = :mbsAgencyId 
                        ORDER BY agency_id DESC 
                        LIMIT 1 
                    ) 
                ";
        $formTypeIdSql = $this->addWhereToFormsSql($formTypeIdSql, $isIncidentForms);
        $formTypeIdSql .= ')';

        return $formTypeIdSql;
    }

    /**
     * Activate forms for an agency (special logic to only activate the first by form_type_id due to FK constraints imposed)
     * @param numeric|int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms 
     * @param numeric|int $stateId 
     * @return bool
     */
    public function activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId)
    {
        try {
            $fnStatus = true;
            $activeFormTypeIds = [];

            $forms = $this->findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId);
            /*
             * Remove base forms (where agency id null) as we do not want to impact common forms, however the query 
             * returns the base form for use in enumerations cloning or other purposes. Note, during code review
             * this logic came up for discussion and it is now a safety mechanism to prevent activation of base forms
             * that previously came into the form set via a malformed query. That query has been improved now to 
             * eliminate these "base" forms, however this loop to check and eliminate base forms from the actvation
             * cycle will remain as a safety mechanism.
             */
            foreach ($forms as $index => $form) {
                $agencyId = $form['agency_id'];
                if (empty($agencyId)) {
                    unset($forms[$index]);
                }
            }
            sort($forms);

            foreach ($forms as $form) {
                $formIsActive = $form['is_active'];
                $formTypeId = $form['form_type_id'];
                if ($formIsActive) {
                    $activeFormTypeIds[$formTypeId] = 1;
                }
            }

            /*
             * Activate forms that are not already active by form_type_id
             */
            foreach ($forms as $form) {

                $formId = $form['form_id'];
                $formIsActive = $form['is_active'];
                $formTypeId = $form['form_type_id'];
                if (empty($formIsActive) && empty($activeFormTypeIds[$formTypeId])) {
                    $bind = [
                        'formId' => $formId
                    ];
                    $updateSql = "update form set is_active = 1 where form_id=:formId and is_active=0";
                    try {
                        $pdo = $this->adapter->query($updateSql, $bind);
                        $tf = $this->pdoQueryOperationStatus($pdo);
                        if (!$tf) {
                            $fnStatus = false;
                        } else {
                            $activeFormTypeIds[$formTypeId] = 1; // add the form type we activated to the array to avoid exceptions on update within loop
                        }
                    } catch (Exception $e) {
                        continue; // before-update trigger can cause exception
                    }
                }
            }
            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Deactivate agency forms
     *
     * @param $mbsAgencyId MBS Agency ID
     * @param $isIncidentForms (true/false) deactivate incident forms when true; else create crash form
     * @return bool
     */
    public function deactivateFormsForAgency($mbsAgencyId, $isIncidentForms)
    {
        try {
            $sql = "
                update form f join form_type ft on f.form_type_id = ft.form_type_id 
                and f.agency_id = (select agency_id from agency where mbsi_agency_id = :mbsAgencyId)
                and f.form_template_id = (select form_template_id from form_template where name_internal='universal-sectional')
                set f.is_active = 0 where f.is_active = 1";
            $sql = $this->addWhereToFormsSql($sql, $isIncidentForms);
            $bind = [
                'mbsAgencyId' => $mbsAgencyId
            ];
            $pdo = $this->adapter->query($sql, $bind);
            $tf = $this->pdoQueryOperationStatus($pdo);

            return $tf;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return null;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve all forms for mbs agency that are active or inactive excluding forms named '%TEST%'
     * @param int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false)
     * @param int $stateId
     * @param [bool|mixed] $excludeTestForms (false) if true, will exclude forms with name_external containing 'test'
     * @return array
     * Note: This function returns forms where agency_id is null (the base form) also which is used for cloning enum
     * values. Dev note: considering splitting up to not include base form and getting base form in separate function
     */
    public function findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId,
        $excludeTestForms = false)
    {
        try {
            $bind = [
                'mbsAgencyId' => $mbsAgencyId,
                'stateId' => $stateId
            ];

            $columns = '*';
            $select = $this->getSelect();
            $select->from(['f' => $this->table], $columns);
            $select->where("f.state_id = :stateId");
            //Get base form AND agency specific forms
            $select->where("
                f.agency_id = ( 
                    select agency_id from agency where mbsi_agency_id = :mbsAgencyId 
                    ORDER BY agency_id DESC 
                    LIMIT 1 
                )");

            $select->where("
                f.form_template_id = (
                    select form_template_id from form_template where name_internal='universal-sectional' 
                    ORDER BY form_template_id DESC 
                    LIMIT 1
                )");

            $select->where($this->genFormTypeIdSql($isIncidentForms));

            $select->where("
                f.entry_stage_process_group_id = (
                    select entry_stage_process_group_id from entry_stage_process_group where description = '2 Pass' 
                    LIMIT 1
                )");

            $select->where("
                f.form_field_attribute_group_id = (
                    select form_field_attribute_group_id from form_field_attribute_group where description = 'Universal V1.1 Fields' 
                    ORDER BY form_field_attribute_group_id DESC LIMIT 1
                )");

            if ($excludeTestForms) {
                $select->where("f.name_external NOT LIKE '%test%'");
            }

            $select->order('form_id ASC');
            $select->order('agency_id ASC');

            $rs = $this->fetchAll($select, $bind);
            return $rs;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Find base form related to a state and or type of accident or ecrash form type
     * @param int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false)
     * @param int $stateId
     * @param [bool|mixed] $excludeTestForms (false) if true, will exclude forms with name_external containing 'test'
     * @param [bool|mixed] $isActive (true) 
     * @return array
     */
    public function findBaseUniversalFormByFormType($mbsAgencyId, $isIncidentForms, $stateId, $excludeTestForms = false,
        $isActive = true)
    {
        try {
            $bind = [
                'mbsAgencyId' => $mbsAgencyId,
                'stateId' => $stateId
            ];
            $columns = '*';
            $select = $this->getSelect();
            $select->from(['f' => $this->table], $columns);
            $select->where("f.state_id = :stateId");
            $select->where("f.agency_id IS NULL");

            if ($isActive) {
                $select->where("f.is_active = 1");
            }

            $select->where("
                f.form_template_id = (
                    select form_template_id from form_template where name_internal='universal-sectional' 
                    ORDER BY form_template_id DESC 
                    LIMIT 1
                )");

            $select->where($this->genFormTypeIdSql($isIncidentForms));

            $select->where("
                f.form_field_attribute_group_id = (
                    select form_field_attribute_group_id from form_field_attribute_group where description = 'Universal V1.1 Fields' 
                    ORDER BY form_field_attribute_group_id DESC LIMIT 1
                )");

            if ($excludeTestForms) {
                $select->where("f.name_external NOT LIKE '%test%'");
            }

            $select->order('form_id ASC');

            $rs = $this->fetchRow($select, $bind);

            return $rs;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
        // @codeCoverageIgnoreEnd
    }

    public function getAssocLists($formId)
    {
        $sql = "
            select l.form_code_list_id as id, l.name as name
              from form_code_list l
                join form_code_list_group_map lgm using (form_code_list_id)
                join form_code_group g using (form_code_group_id)
                join form f using (form_code_group_id)
              where f.form_id = :form_id
              order by l.name
        ";
        $bind = ['form_id' => $formId]; 
       return $this->fetchPairs($sql, $bind);
    }
    
    public function getUnAssocLists($formId)
    {
        $sql = "
            select l.form_code_list_id as id, l.name as name
              from form_code_list l
                join form_code_list_group_map lgm using (form_code_list_id)
                join form_code_group g using (form_code_group_id)
                join form f on (f.form_code_group_id <> g.form_code_group_id)
              where f.form_id = :form_id
              order by l.name
        ";

        $bind = ['form_id' => $formId]; 
        return $this->fetchPairs($sql, $bind);
    }
    
    public function getFormNamesRelatedByGroup($formId)
    {
       $sql = "
            SELECT f2.name_external AS formName,
                ft.description AS formType,
                s.name_abbr AS formState,
                a.name AS formAgency
            FROM form f1
                JOIN form f2 USING (form_code_group_id)
                LEFT JOIN form_type ft ON (f2.form_type_id = ft.form_type_id)
                LEFT JOIN state s ON (f2.state_id = s.state_id)
                LEFT JOIN agency a ON (f2.agency_id = a.agency_id)
            WHERE f1.form_id = 1
                AND f1.form_id <> f2.form_id
            ORDER BY f2.name_external
        ";

        $bind = ['form_id' => $formId];

        return $this->fetchAll($sql, $bind);
    }
    
    public function getFormNamesRelatedByList($formId, $listId)
    {
        $select = $this->getSelect();
        $select->from($this->table);
        $select->columns(['form_code_group_id']);
        $select->where(["form_id" => $formId]);

        $groupId =  $this->fetchOne($select);
        
        $sql = "
            select f.name_external as name from form f
              join form_code_group g using (form_code_group_id)
              join form_code_list_group_map lgm using (form_code_group_id)
              where f.form_code_group_id <> :form_code_group_id
                and lgm.form_code_list_id = :form_code_list_id
        ";

        $bind = [
            'form_code_group_id' => $groupId,
            'form_code_list_id' => $listId
        ];

        return $this->fetchCol($sql, $bind);
    }
    
    public function updateGroup($formId, $groupId)
    {
        return $this->update(
            ['form_code_group_id' => $groupId], ['form_id' => $formId]
        );
    }
}

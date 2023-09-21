<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class FormCodeGroupConfigurationAdapter extends DbAbstract
{
    /**
     * @var string Table name
     */
    protected $table = 'form_code_group_configuration';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }

    /**
     * Derive the form code configuration record(s) from db. Default is single row based on formTemplateId with 
     * other optional params in order or precendence and we will only consider the first record found as valid (*1).
     * @param int|string $formTemplateId
     * @param [int|string] $stateId (null)
     * @param [int|string] $agencyId (null)
     * @return array or false on exception.
     * 
     * Note 1 - Due to redundant entries in form table where the original content of the form_code_group_configuration
     * table was derived, an approach to sort and limit to the first record found is adapted to make that id / FK to 
     * use for other form tables. A maintenance effort to cleanup extraneous form and form_code_group_configuration data
     * should be planned for later effort (per code review comments and discussion for jira injury codes story ECH-4540).
     */
    public function fetchFormCodeConfiguration( $formTemplateId, $stateId = null, $agencyId = null )
    {        
        /*
        * COMPLETELY CUSTOM PER STATE AND AGENCY: See if record exists when both agency and state are also specified.
        */
        if (!empty($stateId) && !empty($agencyId)) {
            $select = $this->getSelect();
            $select->where([
                'form_template_id = :form_template_id',
                'state_id = :state_id',
                'agency_id = :agency_id'
            ]);
            $select->order('form_code_group_configuration_id ASC');
            $select->limit(1);
            $bind = [
                'form_template_id' => $formTemplateId,
                'state_id' => $stateId,
                'agency_id' => $agencyId
            ];
            $rs = $this->fetchRow($select, $bind);

            if (!empty($rs)) {
                return $rs;
            }
        }

        /*
        * CUSTOM PER STATE (ALL AGENCIES): See if record exists if state only specified
        */
        if (!empty($stateId)) {
            //Get by form template Id only
            $select = $this->getSelect();
            $select->where([
                'form_template_id = :form_template_id',
                'state_id = :state_id'
            ]);
            $select->where('agency_id IS NULL');
            $select->order('form_code_group_configuration_id ASC');
            $select->limit(1);
            $bind = [
                'form_template_id' => $formTemplateId,
                'state_id' => $stateId
            ];
            $rs = $this->fetchRow($select, $bind);

            if (!empty($rs)) {
                return $rs;
            }
        }

        /*
        * GENERIC BY TEMPLATE ID: See if record exists if only form template id is specified
        */
        $select = $this->getSelect();
        $select->where([
            'form_template_id = :form_template_id'
        ]);
        $select->where('state_id IS NULL');
        $select->where('agency_id IS NULL');
        $select->order('form_code_group_configuration_id ASC');
        $select->limit(1);
        $bind = [
            'form_template_id' => $formTemplateId
        ];
        $rs = $this->fetchRow($select, $bind);

        return $rs;
    }
}

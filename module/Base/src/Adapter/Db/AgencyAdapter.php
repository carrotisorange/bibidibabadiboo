<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db;

use Zend\Db\Adapter\Adapter;

class AgencyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'agency';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * Select all active agencies
     * 
     * @return array all active agencies, else return empty array; on exception of failure
     */
    public function fetchActive()
    {
        $select = $this->getSelect();
        $select->where(['is_active' => 1]);
        return $this->fetchAll($select);
    }

    /**
     * Select all active agencies under a given state
     * 
     * @param int $stateId
     * @return array all active agencies under specified state, else return empty array; on exception of failure
     */
    public function fetchActiveByState($stateId)
    {
        $columns = [
            'agency_id' => 'agency_id',
            'name' => 'name'
        ];
        $select = $this->getSelect();
        $select->columns($columns)
            ->where([
                'state_id = :stateId',
                'is_active = 1'
            ])
            ->order($columns['name']);
        $bind = [
            'stateId' => $stateId
        ];
        
        return $this->fetchAll($select, $bind);
    }
    
    /**
     * Select all agencies under a given state
     * 
     * @param int $stateId
     * @return array all agencies under specified state, else return empty array; on exception of failure
     */
    public function fetchAllByState($stateId = null)
    {
        $select = $this->getSelect();
        $select->where([
            'state_id = :stateId'
        ]);
        
        $bind = ['stateId' => $stateId];
        $select->order('name ASC');
        
        return $this->fetchAssoc($select, $bind);
    }
    
    public function fetchAgenciesWithReports()
    {
        $select = $this->getSelect();
        $select->from(['agn' => 'agency']);
        $select->columns(['agency_id' => $this->getDistinct('agn.agency_id'), 'name' => 'agn.name' ], false);
        $select->join(['rep' => 'report'], 'agn.agency_id = rep.agency_id', []);
        
        return $this->fetchAll($select);
    }

    /**
     * Select all active agencies under a given state but only getting the name and id
     * 
     * @param int $stateId
     * @return array all active agencies under a specific state but only having agency id and name information,
     * else return empty array; on exception of failure
     */
    public function fetchActiveAgencyIdNamePairs($stateId)
    {
        $columns = [
            'agencyId' => 'agency_id',
            'agencyName' => 'name'
        ];
        $select = $this->getSelect();
        $select->columns($columns);
        $select->where([
            'state_id = :stateId',
            'is_active = 1'
        ]);
        $select->order('name');
        $bind = [
            'stateId' => $stateId
        ];
        
        return $this->fetchPairs($select, $bind);
    }
    
    /**
     * Fetch a single row for one agency by agency id.
     * @param int $agencyId
     * @return Agency data Row as array
     */
    public function getAgencyByAgencyId($agencyId)
    {
        $select = $this->getSelect();
        $select->where(['agency_id = :agency_id']);
        $bind = ['agency_id' => $agencyId];
        
        return $this->fetchRow($select, $bind);
    }
    
    /**
     * Select agencies based on the given form id
     * 
     * @param int $formId
     * @return array all agencies based on the given form id
     */
    public function fetchActiveByFormId($formId)
    {
        $sql = "
            SELECT
                a.*
            FROM agency AS a
            INNER JOIN form AS f USING(agency_id)
            WHERE f.form_id = :form_id
        ";
        $bind = ['form_id' => $formId];
        
        return $this->fetchRow($sql, $bind);
    }

    /**
     * Select the latest mbs date added and date changed
     * 
     * @return array the latest mbs activity dates, else empty array; on exception of failure
     */
    public function fetchLatestMbsAgencySyncDates()
    {
        $sql = "SELECT 
                    IFNULL(MAX(mbs_date_added), '0000-00-00 00:00:00') latestMbsDateAdded,
                    IFNULL(MAX(mbs_date_changed), '0000-00-00 00:00:00') latestMbsDateChanged
                FROM $this->table";
        return $this->fetchRow($sql);
    }

    /**
     * Creates or updates an eCrash Agency
     * @param object $mbsAgency
     * @return boolean true on insert or create success, false on failure or invalid primary key of mbsAgencyId
     */
    public function createOrUpdateAgency($mbsAgency, $crashLogicSuccess)
    {
        if (!isset($mbsAgency->mbsAgencyId) || !is_numeric($mbsAgency->mbsAgencyId)) {
            return false;
        }

        $mbsAgencyId = $mbsAgency->mbsAgencyId;

        $select = $this->getSelect();
        $select->where(['mbsi_agency_id' => $mbsAgencyId]);
        $agencyRow = $this->fetchRow($select);

        $update = [];
        $pk = NULL;
        if (!empty($agencyRow)) {
            $update['state_id'] = $mbsAgency->stateId;
            $update['name'] = $mbsAgency->agencyName;
            $update['company_status'] = $mbsAgency->companyStatus;
            $update['is_active'] = $mbsAgency->keyingStatus;
            $update['agency_ori'] = $mbsAgency->agencyOri;           
            $update['is_admin_portal_active'] = $mbsAgency->adminPortalStatus;
            $update['admin_portal_loc_badge_img'] = $mbsAgency->adminPortalLocBadgeImg;
            $update['admin_portal_hit_run_rpts'] = $mbsAgency->adminPortalHitRunRpts;
            $update['admin_portal_not_investigated'] = $mbsAgency->adminPortalNotInvestigated;
            $update['admin_portal_rendering_rpts_opt'] = $mbsAgency->adminPortalRenderingRptsOpt;
            $update['admin_portal_watermark'] = $mbsAgency->adminPortalWatermark;
            $update['admin_portal_shr_rpt'] = $mbsAgency->adminPortalShrRpt;
            $update['admin_portal_agency_ori'] = $mbsAgency->adminPortalAgencyOri;
            $update['admin_portal_upload_report'] = $mbsAgency->adminPortalUploadReport;
            $update['admin_crash_reports'] = $mbsAgency->adminCrashReports;
            $update['admin_incident_reports'] = $mbsAgency->adminIncidentReports;
            
            if ($crashLogicSuccess) {
                $update['mbs_date_added'] = $mbsAgency->dateAdded;
                $update['mbs_date_changed'] = $mbsAgency->dateChanged;
            }
            
            $update['show_all_analytics'] = $mbsAgency->adminPortalRptsAnalytics;
            $update['keying_rpt_nbr_fmt'] = $mbsAgency->keyingRptNbrFmt;
            $update['drivers_exchange_flag'] = $mbsAgency->driversExchangeFlag;
            $update['admin_portal_upload_photo'] = $mbsAgency->admin_portal_upload_photo;
            $update['is_iyetek_active'] = $mbsAgency->iyeTekFlag;
            $update['is_dors_active'] = $mbsAgency->dorsFlag;
            $update['selfservice'] = $mbsAgency->allowSelfService;
            $update['contact_emailaddress'] = $mbsAgency->allowSelfServiceEmail;
            $update['citizen_filed_rpt_flag'] = $mbsAgency->citizenFiledRptFlag;
            $update['admin_portal_upload_witness_stmt'] = $mbsAgency->adminPortalUploadWitness;
            $update['redact_report'] = $mbsAgency->redact_report;
            $update['redact_command_center'] = $mbsAgency->command_center;
            $update['redact_internal_agency_user'] = $mbsAgency->internal_agency_user;
            $update['redact_external_agency_user'] = $mbsAgency->external_agency_user;
            $update['redact_phone_number'] = $mbsAgency->phone_number;
            $update['redact_date_of_birth'] = $mbsAgency->date_of_birth;
            $update['redact_drivers_license'] = $mbsAgency->drivers_license;
            $update['allow_edit_keyed_data'] = $mbsAgency->allowEditKeyData;
            $update['allow_vin_alert'] = $mbsAgency->vinAlertFunct;
            $update['longitude'] = floatval($mbsAgency->map_longitude);
            $update['latitude'] = floatval($mbsAgency->map_latitude);
            $update['zoom_level'] = $mbsAgency->map_zoom_level;
            $update['city'] = $mbsAgency->city;
            $update['allow_suppress_non_rel_report'] = $mbsAgency->suppressNonReleasableRpts;
            $update['exclude_non_rel_report'] = $mbsAgency->excludeNonReleasableRpts;
            $update['agency_type'] = $mbsAgency->agency_type;
            $update['allow_people_search'] = $mbsAgency->peopleSearch;
            $update['is_ethos_active'] = $mbsAgency->ethos;
            $update['suppress_cru_source'] = $mbsAgency->suppress_cru_source;

            $pk = $this->update($update,['mbsi_agency_id' => $mbsAgencyId]);
        } else {
            $agencyInsert = [
                'state_id' => $mbsAgency->stateId,
                'name' => $mbsAgency->agencyName,
                'company_status' => $mbsAgency->companyStatus,
                'is_active' => $mbsAgency->keyingStatus,
                'mbsi_agency_id' => $mbsAgencyId,
                'agency_ori' => $mbsAgency->agencyOri,
                'is_admin_portal_active' => $mbsAgency->adminPortalStatus,
                'admin_portal_loc_badge_img' => $mbsAgency->adminPortalLocBadgeImg,
                'admin_portal_hit_run_rpts' => $mbsAgency->adminPortalHitRunRpts,
                'admin_portal_not_investigated' => $mbsAgency->adminPortalNotInvestigated,
                'admin_portal_rendering_rpts_opt' => $mbsAgency->adminPortalRenderingRptsOpt,
                'admin_portal_watermark' => $mbsAgency->adminPortalWatermark,
                'admin_portal_shr_rpt' => $mbsAgency->adminPortalShrRpt,
                'admin_portal_agency_ori' => $mbsAgency->adminPortalAgencyOri,
                'admin_portal_upload_report' => $mbsAgency->adminPortalUploadReport,
                'admin_crash_reports' => $mbsAgency->adminCrashReports,
                'admin_incident_reports' => $mbsAgency->adminIncidentReports,
                'show_all_analytics' => $mbsAgency->adminPortalRptsAnalytics,
                'keying_rpt_nbr_fmt' => $mbsAgency->keyingRptNbrFmt,
                'drivers_exchange_flag' => $mbsAgency->driversExchangeFlag,
                'admin_portal_upload_photo' => $mbsAgency->admin_portal_upload_photo,
                'is_iyetek_active' => $mbsAgency->iyeTekFlag,
                'is_dors_active' => $mbsAgency->dorsFlag,
                'selfservice' => $mbsAgency->allowSelfService,
                'contact_emailaddress' => $mbsAgency->allowSelfServiceEmail,
                'citizen_filed_rpt_flag' => $mbsAgency->citizenFiledRptFlag,
                'admin_portal_upload_witness_stmt' => $mbsAgency->adminPortalUploadWitness,
                'redact_report' => $mbsAgency->redact_report,
                'redact_command_center' => $mbsAgency->command_center,
                'redact_internal_agency_user' => $mbsAgency->internal_agency_user,
                'redact_external_agency_user' => $mbsAgency->external_agency_user,
                'redact_phone_number' => $mbsAgency->phone_number,
                'redact_date_of_birth' => $mbsAgency->date_of_birth,
                'redact_drivers_license' => $mbsAgency->drivers_license,
                'allow_edit_keyed_data' => $mbsAgency->allowEditKeyData,
                'allow_vin_alert' => $mbsAgency->vinAlertFunct,
                'longitude' => floatval($mbsAgency->map_longitude),
                'latitude' => floatval($mbsAgency->map_latitude),
                'zoom_level' => $mbsAgency->map_zoom_level,
                'city' => $mbsAgency->city,
                'allow_suppress_non_rel_report' => $mbsAgency->suppressNonReleasableRpts,
                'exclude_non_rel_report' => $mbsAgency->excludeNonReleasableRpts,
                'agency_type' => $mbsAgency->agency_type,
                'allow_people_search' => $mbsAgency->peopleSearch,
                'is_ethos_active' => $mbsAgency->ethos,
                'suppress_cru_source' => $mbsAgency->suppress_cru_source,
            ];
            
            if ($crashLogicSuccess) {
                $agencyInsert['mbs_date_added'] = $mbsAgency->dateAdded;
                $agencyInsert['mbs_date_changed'] = $mbsAgency->dateChanged;
            }
            
            $pk = $this->insert($agencyInsert);
        }

        return $pk;
    }

    /**
     * Select the active agency based on its mbs agency id
     * 
     * @param int $mbsAgencyId the mbs agency id equivalent
     * @return array existing active agency, else empty array; on exception of failure
     */
    public function fetchAgencyByMbsAgencyId($mbsAgencyId)
    {
        try {
            if (empty($mbsAgencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbs agency id is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $sql = "SELECT * FROM $this->table WHERE mbsi_agency_id = :mbsAgencyId";
            $bind = ['mbsAgencyId' => $mbsAgencyId];
            return $this->fetchRow($sql, $bind);
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }
    }
}

<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */

namespace Base\Adapter\Db\Mbs;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;

use Base\Adapter\Db\DbAbstract;

class AgencyAdapter extends DbAbstract
{
    /**
     * @var string
     * Table name
     */
    protected $table = 'v_agency';
    
    /**
     * @param object $adapter Zend\Db\Adapter\Adapter
     */
    public function __construct(Adapter $adapter)
    {
        parent::__construct($adapter, $this->table);
    }
    
    /**
     * @param string $lastSyncedMbsDateAdded
     * @param string $lastSyncedMbsDateChanged
     * @return array 
     */
    public function getAgencies($lastSyncedMbsDateAdded, $lastSyncedMbsDateChanged) 
    {
        return $this->fetchAll(
            "SELECT 
                UPPER(TRIM(s.state_code)) stateCode,
                a.agency_name agencyName,
                a.agency_id mbsAgencyId,
                a.agency_ori_code agencyOri,
                a.company_status companyStatus,
                IF(a.keying_status = 'A', 1, 0) keyingStatus,
                a.active_admin_portal_flag adminPortalStatus,
                a.admin_portal_loc_badge_img adminPortalLocBadgeImg,
                a.admin_portal_hit_run_rpts adminPortalHitRunRpts,
                a.admin_portal_not_investigated adminPortalNotInvestigated,
                a.admin_portal_rendering_rpts_opt adminPortalRenderingRptsOpt,
                a.admin_portal_watermark adminPortalWatermark,
                a.admin_portal_shr_rpt adminPortalShrRpt,
                a.admin_portal_agency_ori adminPortalAgencyOri,
                a.admin_portal_upload_report adminPortalUploadReport,
                a.admin_crash_reports adminCrashReports,
                a.admin_incident_reports adminIncidentReports,
                a.date_added dateAdded,
                a.date_changed dateChanged,
                a.admin_portal_rpts_n_analytics adminPortalRptsAnalytics,
                a.keying_rpt_nbr_fmt keyingRptNbrFmt,
                a.drivers_exchange_flag driversExchangeFlag,
                a.admin_portal_upload_photo admin_portal_upload_photo,
                a.iyeTek_flag iyeTekFlag,
                a.dors_flag dorsFlag,
                a.allow_self_service allowSelfService,
                a.allow_self_service_email allowSelfServiceEmail,
                a.citizen_filed_rpt_flag citizenFiledRptFlag,
                a.admin_portal_upload_witness adminPortalUploadWitness,
                a.redact_report redact_report,
                a.command_center command_center,
                a.internal_agency_user internal_agency_user,
                a.external_agency_user external_agency_user,
                a.phone_number phone_number,
                a.date_of_birth date_of_birth,
                a.drivers_license drivers_license,
                a.Is_edit_keyed_data allowEditKeyData,
                                a.vin_alert_funct vinAlertFunct,
                a.map_longitude map_longitude,
                a.map_latitude map_latitude,
                                a.map_zoom_level map_zoom_level,
                a.city city,
                                a.suppress_non_releasable_rpts suppressNonReleasableRpts,
                                a.exclude_non_releasable_rpts excludeNonReleasableRpts,
                                a.agency_type agency_type,
                a.people_search peopleSearch,
                a.Ethos ethos,
                a.suppress_cru_source suppress_cru_source
            FROM v_agency a
                JOIN agency_state s ON (a.agency_state_id = s.agency_state_id)
            WHERE a.date_added > :dateAdded
                OR a.date_changed > :dateChanged",
            [
                'dateAdded' => $lastSyncedMbsDateAdded,
                'dateChanged' => $lastSyncedMbsDateChanged
            ]
        );
    }

}

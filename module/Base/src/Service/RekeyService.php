<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

use Base\Adapter\Db\ReportEntryAdapter;
use Base\Adapter\Db\FormAdapter;
use Base\Adapter\Db\ReportAdapter;
use Base\Adapter\Db\FormsToRekeyAdapter;
use Base\Adapter\Db\RekeyUserFormPermissionAdapter;
use Base\Service\ReportService;
use Base\Service\ReportStatusService;
use Base\Service\ReportEntryQueueService;
use Base\Service\EntryStageService;
use Base\Service\FormService;
use Base\Service\ReportEntryService;

/**
 * Manages the rekey process completely by getting all the reports that need to be rekeyed from form_to_rekey table, finds the traslated
 * reports from report table and sets thier id to keying.
 * will queue at most 100 reports at a time.
 * This is specific to the ReportEntry keying queue.
 */
class RekeyService extends BaseService
{
    const WORK_TYPE_ECRASH = '1';
    
    const ADD_KEY_STR_NONE = 'None';
    const ADD_KEY_STR_PAPER = 'Paper Additional Keying';
    const ADD_KEY_STR_ELECTRONIC = 'Electronic Additional Keying';
    
    const PAPER_ADDITIONAL_KEYING = 0;
    const ELECTRONIC_ADDITIONAL_KEYING = 1;
    const NO_ADDITIONAL_KEYING = 2;
    
    /**
     * @var Array
     */
    protected $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\ReportEntryAdapter
     */
    protected $adapterReportEntry;

    /**
     * @var Base\Adapter\Db\FormAdapter
     */
    protected $adapterForm;

    /**
     * @var Base\Adapter\Db\FormsToRekeyAdapter
     */
    protected $adapterFormsToRekey;
    
    /**
     * @var Base\Adapter\Db\RekeyUserFormPermissionAdapter
     */
    protected $adapterRekeyUserFormPermission;
    
    /**
     * @var Base\Service\ReportService
     */
    protected $serviceReport;
    
    /**
     * @var Base\Service\ReportStatusService
     */
    protected $serviceReportStatusService;
    
    /**
     * @var Base\Service\ReportEntryQueueService
     */
    protected $serviceReportEntryQueue;
    
    /**
     * @var Base\Service\EntryStageService
     */
    protected $serviceEntryStage;
    
    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;
    
    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;

    protected $rekeyUserId = 9999;
    protected $batchCount = 0;
    protected $errorMessage;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportEntryAdapter $adapterReportEntry,
        FormAdapter $adapterForm,
        ReportAdapter $adapterReport,
        FormsToRekeyAdapter $adapterFormsToRekey,
        ReportService $serviceReport,
        ReportStatusService $serviceReportStatus,
        ReportEntryQueueService $serviceReportEntryQueue,
        RekeyUserFormPermissionAdapter $adapterRekeyUserFormPermission,
        EntryStageService $serviceEntryStage,
        FormService $serviceForm,
        ReportEntryService $serviceReportEntry)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterReportEntry = $adapterReportEntry;
        $this->adapterForm = $adapterForm;
        $this->adapterReport = $adapterReport;
        $this->adapterFormsToRekey = $adapterFormsToRekey;
        $this->serviceReport = $serviceReport;
        $this->serviceReportStatus = $serviceReportStatus;
        $this->serviceReportEntryQueue = $serviceReportEntryQueue;
        $this->adapterRekeyUserFormPermission = $adapterRekeyUserFormPermission;
        $this->serviceEntryStage = $serviceEntryStage;
        $this->serviceForm = $serviceForm;
        $this->serviceReportEntry = $serviceReportEntry;
    }
    
    public function isQueuedForRekey($reportId)
    {
        $result = false;
        $formAndEntryId = $this->adapterReportEntry->getMaxCompletedFormAndEntryId($reportId);

        if (!empty($formAndEntryId)) {
            $entryFormInfo = $this->adapterForm->getFormInfo($formAndEntryId['form_id']);
            $reportInfo = $this->adapterReport->getRelatedInfo($reportId);
            $reportFormInfo = $this->adapterForm->getFormInfo($reportInfo['formId']);

            // @TODO: Should remove FormService::SYSTEM_IYETEK. And how to adapt rekey in universal form.
            if ($reportInfo['formId'] != $formAndEntryId['form_id']
                && $entryFormInfo['formSystem'] == FormService::SYSTEM_UNIVERSAL
                //&& $reportFormInfo['formSystem'] == FormService::SYSTEM_IYETEK
                && $reportFormInfo['formSystem'] == FormService::SYSTEM_IYETEK) {

                $result = true;
            }
        }

        return $result;
    }

    public function getRekeyEntryStage()
    {
        return $this->serviceEntryStage->getIdByInternalName(EntryStageService::STAGE_REKEY);
    }
    
    public function getERekeyEntryStage()
    {
        return $this->serviceEntryStage->getIdByInternalName(EntryStageService::STAGE_ELECTRONIC_REKEY);
    }
    
    public function updateFormPermissionsViaPost($formIds, $userId, $keyingType)
    {
        $this->adapterRekeyUserFormPermission->delete([
            'user_id' => $userId,
            'type' => $keyingType
        ]);
        
        foreach ($formIds as $formId) {
            try {
                $this->adapterRekeyUserFormPermission->insert([
                    'user_id' => $userId,
                    'form_id' => $formId,
                    'type' => $keyingType
                ]);
            } catch (Exception $e) {
                return false;
            }
        }
        
        return true;
    }
    
    public function getUserRekeyFormPermission($userId, $keyingType)
    {
        return $this->adapterRekeyUserFormPermission->getUserFormPermissions($userId, $keyingType);
    }
}

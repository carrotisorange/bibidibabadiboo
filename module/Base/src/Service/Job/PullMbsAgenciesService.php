<?php
namespace Base\Service\Job;

use Zend\Log\Logger;
Use Zend\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Db\Adapter\Adapter;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Base\Service\Mbs\AgencyService as MbsAgencyService;
use Base\Service\AgencyService;
use Base\Service\Mbs\AgencyContributorySourceService as MbsAgencyContributorySourceService;
use Base\Service\AgencyContributorySourceService;
use Base\Service\StateService;
use Base\Service\MailerService;
use Base\Service\FormService;
use Base\Service\FormWorkTypeService;
use Base\Service\WebService\CrashLogicAgencyUpdateService;
use Base\Service\Cdi\EnumeratorService;
use Base\Service\EcrashUtilsArrayService;

class PullMbsAgenciesService extends JobAbstract
{
    /**DbAbstract
     * @var Base\Service\MbsAgencyService
     */
    protected $serviceMbsAgency;
    /**
     * @var Base\Service\AgencyService
     */
    protected $serviceAgency;
    /**
     * @var Base\Service\MbsAgencyContributorySourceService
     */
    protected $serviceMbsAgencyContributorySource;
    /**
     * @var Base\Service\AgencyContributorySourceService
     */
    protected $serviceAgencyContributorySource;
    /**
     * @var Base\Service\StateService
     */
    protected $serviceState;
    /**
     * @var Base\Service\MailerService
     */
    protected $serviceMailer;
    /**
     * @var Base\Service\FormService
     */
    protected $serviceForm;
    /**
     * @var Base\Service\Cdi\EnumeratorService
     */
    protected $serviceEnumerator;
    /**
     * @var Base\Service\FormWorkTypeService
     */
    protected $serviceFormWorkType;
    /**
     * @var Base\Service\WebService\CrashLogicAgencyUpdateService
     */
    protected $serviceCrashLogicAgencyUpdate;
    /**
     * @var Array
     */
    protected $config;
    /**
     * @var Base\Service\EcrashUtilsArrayService
     */
    protected $serviceEcrashUtilsArray;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        MbsAgencyService $serviceMbsAgency,
        AgencyService $serviceAgency,
        MbsAgencyContributorySourceService $serviceMbsAgencyContributorySource,
        AgencyContributorySourceService $serviceAgencyContributorySource,
        StateService $serviceState,
        MailerService $serviceMailer,
        FormService $serviceForm,
        EnumeratorService $serviceEnumerator,
        FormWorkTypeService $serviceFormWorkType,
        CrashLogicAgencyUpdateService $serviceCrashLogicAgencyUpdate,
        $adapter,
        Array $config,
        $log,
        EcrashUtilsArrayService $serviceEcrashUtilsArray)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        $this->serviceMbsAgency = $serviceMbsAgency;
        $this->serviceAgency = $serviceAgency;
        $this->serviceMbsAgencyContributorySource = $serviceMbsAgencyContributorySource;
        $this->serviceAgencyContributorySource = $serviceAgencyContributorySource;
        $this->serviceState = $serviceState;
        $this->serviceMailer = $serviceMailer;
        $this->serviceForm = $serviceForm;
        $this->serviceFormWorkType = $serviceFormWorkType;
        $this->serviceEnumerator = $serviceEnumerator;
        $this->serviceCrashLogicAgencyUpdate = $serviceCrashLogicAgencyUpdate;
        $this->config = $config;
        $this->logger = $log;
        $this->adapter = $adapter;
        $this->serviceEcrashUtilsArray = $serviceEcrashUtilsArray;
    }

    protected function runJob()
    {
        $this->logger->log(Logger::INFO, 'Pull Mbs Agencies Job started');
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            //update agency
            $dates = $this->serviceAgency->getLatestMbsAgencySyncDates();
            
            $mbsAgencies = $this->serviceMbsAgency->getAgencies(
                $dates['latestMbsDateAdded'], $dates['latestMbsDateChanged']
            );
            $numAgencies = count($mbsAgencies);
            $this->logger->log(Logger::INFO, "MBS Agencies created after {$dates['latestMbsDateAdded']} or " .
                    "updated after {$dates['latestMbsDateChanged']} : " . $numAgencies);

            if ($numAgencies > 0) {
                $this->syncAgencies($mbsAgencies);
            }

            //update agency_contributory_source 
            $contribSourceDates = $this->serviceAgencyContributorySource->getLatestMbsContribSourceSyncDates();

            $mbsAgencyContribSources = $this->serviceMbsAgencyContributorySource->getAgencyContributorySources(
                    $contribSourceDates['latestMbsDateAdded'], $contribSourceDates['latestMbsDateChanged']
            );
            $numContribSources = count($mbsAgencyContribSources);

            $this->logger->log(Logger::INFO, "MBS Agency Contributory Sources created after {$contribSourceDates['latestMbsDateAdded']} or " .
                    "updated after {$contribSourceDates['latestMbsDateChanged']} : " . $numContribSources);
            if ($numContribSources > 0) {
                $this->syncAgencyContribSources($mbsAgencyContribSources);
                $this->processUpdateDeleteContribIncidents();
            }

            $this->adapter->getDriver()->getConnection()->commit();
            $this->logger->log(Logger::INFO, "Pull Mbs Agencies Job completed");

            return parent::RETURN_CODE_SUCCESS;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);

            $this->adapter->getDriver()->getConnection()->rollBack();

            return parent::RETURN_CODE_FAILED;
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Sync Agencies' latest information from Mbs to eCrash
     * 
     * @param array $mbsAgencies list of agency entries for syncing to ecrash
     */
    public function syncAgencies($mbsAgencies)
    {
        try {
            $stateAbbrIdPairs = $this->serviceState->getStateAbbrIdPairs();
            
            $updateDateField = true;
            
            foreach ($mbsAgencies as $mbsAgencyCrashLogic) {
                try{
                    $mbsAgencyCrashLogic= (object) $mbsAgencyCrashLogic;

                    if ($mbsAgencyCrashLogic->redact_report == "1" && $mbsAgencyCrashLogic->command_center == "1") {
                        $crashLogicresult= $this->serviceCrashLogicAgencyUpdate->updateAgencyInfo(
                                $mbsAgencyCrashLogic->agencyName,
                                $mbsAgencyCrashLogic->agencyOri,
                                $mbsAgencyCrashLogic->date_of_birth,
                                $mbsAgencyCrashLogic->drivers_license,
                                $mbsAgencyCrashLogic->phone_number,
                                $mbsAgencyCrashLogic->stateCode
                                );
                        if ($updateDateField) {
                            $updateDateField = $crashLogicresult;
                        }

                        if (!$crashLogicresult) {
                            $this->logger->log(Logger::ERR, 'Crashlogic webservice returned false  ' . print_r($mbsAgencyCrashLogic, true));
                        } else {
                            $this->logger->log(Logger::INFO, 'Crashlogic webservice returned success  ' . print_r($mbsAgencyCrashLogic, true));
                        }
                    } else {
                        $this->logger->log(Logger::INFO, 'Crashlogic webservice not called since redact report and command center both are not 1 ' . print_r($mbsAgencyCrashLogic, true));
                    }
                } //@codeCoverageIgnoreStart
                catch (Exception $e) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                    $errMsg = 'Exception Occured while calling Crashlogic update Agency: ' . $e->getMessage() . ' @ ' . $origin;
                    $this->logger->log(Logger::ERR, $errMsg);
                } // @codeCoverageIgnoreEnd
                
            }
            
            foreach ($mbsAgencies as $mbsAgency) {
                $mbsAgency = (object) $mbsAgency;
                $stateCode = $mbsAgency->stateCode;
                $stateId = isset($stateAbbrIdPairs[$stateCode]) ? $stateAbbrIdPairs[$stateCode] : null;

                /*
                 * Record original state of the eCrash agency for use in various logic below (record before sync update)
                 */
                $mbsAgencyId = $mbsAgency->mbsAgencyId;
                $origEcrashAgency = $this->serviceAgency->getAgencyByMbsAgencyId($mbsAgencyId);
				if (empty($origEcrashAgency)) $origEcrashAgency = false;
				
                if (is_numeric($stateId)) {
                    $mbsAgency->stateId = $stateId;
                    
                    if ($this->serviceAgency->createOrUpdateAgency($mbsAgency, $updateDateField)) {
                        $this->logger->log(Logger::INFO, 'Synced agency ' . print_r($mbsAgency, true));
                        $this->processAgencyForms($mbsAgency, $stateId, $origEcrashAgency);
                    } else {
                        $this->logger->log(Logger::ERR, 'Error saving agency ' . print_r($mbsAgency, true));
                    }
                } else {
                    $this->logger->log(Logger::ERR, 'eCrash State entry not available for ' . print_r($mbsAgency, true));
                }
            }
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Sync Agency Contributory Sources' latest information from Mbs to eCrash 
     * 
     * @param array $mbsAgencyContribSources list of agency contributory source entries for syncing to ecrash
     */
    public function syncAgencyContribSources($mbsAgencyContribSources)
    {
        try {
            foreach ($mbsAgencyContribSources as $mbsAgencyContribSource) {
                $mbsAgencyContribSource = (object) $mbsAgencyContribSource;
                if ($this->serviceAgencyContributorySource->createOrUpdateAgencyContribSource($mbsAgencyContribSource)) {
                    $this->logger->log(Logger::INFO, 'Synced agency contributory source ' . print_r($mbsAgencyContribSource, true));
                } else {
                    $this->logger->log(Logger::ERR, 'Error saving agency contributory source ' . print_r($mbsAgencyContribSource, true));
                }
            }
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Check if there are affected incidents by updates to agency contributory sources and send an email if there is
     * 
     * @return bool false on exception of failure
     */
    protected function processUpdateDeleteContribIncidents()
    {
        try {
            $affected = $this->serviceAgencyContributorySource->getAffectedContribIncidents();
            
            if (count($affected) > 0) {
                $agencyContribMail = $this->config['agencycontribsource']['mail'];
                $mailConfig = new \stdClass();
                $mailConfig->templatePath = $agencyContribMail['templatePath'];
                $mailConfig->template = $agencyContribMail['template'];
                $mailConfig->subject = $agencyContribMail['subject'];
                $mailConfig->to = $agencyContribMail['to'];
                $mailConfig->from = $agencyContribMail['from'];
                
                $this->serviceMailer->sendHtmlMail($mailConfig, ['affected' => $affected]);
            }
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Create/Update and activate/deactivate agency forms based on MBS settings (adminCrashReports and adminIncidentReports)
     * @param object $mbsAgency
     * @param int $stateId
     * @param array $origEcrashAgency
     * @return boolean
     */
    public function processAgencyForms($mbsAgency, $stateId, $origEcrashAgency)
    {
        try {
            $mbsAgencyId = $mbsAgency->mbsAgencyId;
            /*
             * Process Incident Reports
             */
            $isIncidentForms = true;
            if ($mbsAgency->adminIncidentReports == '1') {
                $tf = $this->createUpdateFormsAndData($mbsAgencyId, $isIncidentForms, $stateId);

                if (!$tf) {
                    $this->logger->log(Logger::ERR, 'Failure by create or update incident forms routine for mbsAgencyId ' . $mbsAgencyId);
                }

                $tf = $this->activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId);

                if (!$tf) {
                    $this->logger->log(Logger::ERR, 'Failure by form activation routine for incident forms for mbsAgencyId ' . $mbsAgencyId);
                }

            } else if ($mbsAgency->adminIncidentReports == '0') {
                $ecrashAgencyIsCandidateForDeactivation = $this->isEcrashAgencyCandidateForDeactivation($origEcrashAgency,
                        $isIncidentForms);

                if ($ecrashAgencyIsCandidateForDeactivation) {
                    if ($this->deactivateFormsForAgency($mbsAgencyId, $isIncidentForms)) {
                        $this->logger->log(Logger::INFO, 'Deactivated incident forms for mbsAgencyId ' . $mbsAgencyId);
                    } else {
                        $this->logger->log(Logger::ERR, 'Error deactivating incident forms for mbsAgencyId ' . $mbsAgencyId);
                    }
                } else {
                    $this->logger->log(Logger::DEBUG, 'Deactivation skipped because ecrash agency already deactivated for mbsAgencyId ' . $mbsAgencyId);
                }
            }

            /*
             * Process Crash Reports
             */
            $isIncidentForms = false;
            if ($mbsAgency->adminCrashReports == '1') {
                $tf = $this->createUpdateFormsAndData($mbsAgencyId, $isIncidentForms, $stateId);
                if (!$tf) {
                    $this->logger->log(Logger::INFO, 'Failure by created or update crash form routine for mbsAgencyId ' . $mbsAgencyId);
                }

                $tf = $this->activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId);

                if (!$tf) {
                    $this->logger->log(Logger::INFO, 'Failure by form activation routine for crash forms for mbsAgencyId ' . $mbsAgencyId);
                }
            } else if ($mbsAgency->adminCrashReports == '0') {
                $ecrashAgencyIsCandidateForDeactivation = $this->isEcrashAgencyCandidateForDeactivation($origEcrashAgency,
                        $isIncidentForms);

                if ($ecrashAgencyIsCandidateForDeactivation) {
                    if ($this->deactivateFormsForAgency($mbsAgencyId, $isIncidentForms)) {
                        $this->logger->log(Logger::INFO, 'Deactivated crash form for mbsAgencyId ' . $mbsAgencyId);
                    } else {
                        $this->logger->log(Logger::ERR, 'Error deactivating crash form for mbsAgencyId ' . $mbsAgencyId);
                    }
                } else {
                    $this->logger->log(Logger::DEBUG, 'Deactivation skipped because ecrash agency already deactivated for mbsAgencyId ' . $mbsAgencyId);
                }
            }
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);                        
            return false;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Check if the eCrash version of agency is candidate for deactivation based on the admin_crash_reports and admin_incident_reports fields
     * @param array $origEcrashAgency
     * @param bool $isIncidentForms
     * @return bool
     */
    public function isEcrashAgencyCandidateForDeactivation($origEcrashAgency, $isIncidentForms)
    {
        try {
            switch ($isIncidentForms)
            {
                case '0':
                    /* Crash Reports */
                    $ecrashAgencyIsCandidateForDeactivation = $origEcrashAgency['admin_crash_reports'] == 1;
                    break;
                case '1':
                    /* Incident Reports */
                    $ecrashAgencyIsCandidateForDeactivation = $origEcrashAgency['admin_incident_reports'] == 1;
                    break;
                default:
                    $ecrashAgencyIsCandidateForDeactivation = false;
            }
            return $ecrashAgencyIsCandidateForDeactivation;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin); 
            return false;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Create new universal forms, enumeration data, and add default form work type records for new forms.
     * @param type $mbsAgencyId
     * @param type $isIncidentForms
     * @param type $stateId
     * @param [bool] $activateForms (false) if true will activate forms after creating new form records.
     * @return boolean
     */
    public function createUpdateFormsAndData($mbsAgencyId, $isIncidentForms, $stateId, $activateForms = false)
    {
        try {
            $fnStatus = $this->serviceForm->createOrUpdateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId, $activateForms);
            if ($fnStatus) {
                $this->logger->log(Logger::INFO, 'Created or updated crash form for mbsAgencyId ' . $mbsAgencyId);
                /*
                 * Setup enumeration Values for new forms
                 */
                if ($this->cloneEnumValsFromExistingForm($mbsAgencyId, $isIncidentForms, $stateId)) {
                    $this->logger->log(Logger::INFO, 'Created or updated incident form enum values for mbsAgencyId ' . $mbsAgencyId);
                } else {
                    $fnStatus = false;
                    $this->logger->log(Logger::INFO, 'ERROR: Failed to create or update incident form enum values for mbsAgencyId ' . $mbsAgencyId);
                }
                /*
                 * Setup Form Work Type Records for new forms
                 */
                if ($this->setupFormWorkTypeRecords($mbsAgencyId, $isIncidentForms, $stateId)) {
                    $this->logger->log(Logger::INFO, 'Created form work type records for mbsAgencyId ' . $mbsAgencyId);
                } else {
                    $fnStatus = false;
                    $this->logger->log(Logger::INFO, 'ERROR: Failed to create form work type records for mbsAgencyId ' . $mbsAgencyId);
                }
            } else {
                $this->logger->log(Logger::ERR, 'Failed to created or updated crash forms for mbsAgencyId ' . $mbsAgencyId);
            }
            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin); 
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Activate forms for a MBSI agency
     * @param int $mbsAgencyId
     * @param bool $isIncidentForms
     * @param int $stateId
     * @return boolean
     */
    public function activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId)
    {
        try {
            $fnStatus = true;
            $tf = $this->serviceForm->activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId);
            if ($tf) {
                $this->logger->log(Logger::INFO, 'Forms activated for forms for mbsAgencyId ' . $mbsAgencyId);
            } else {
                $fnStatus = false;
                $this->logger->log(Logger::ERR, 'Failure by form activation routine for forms for mbsAgencyId ' . $mbsAgencyId);
            }
            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin); 
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Deactivate forms for a MBSI agency
     * @param int $mbsAgencyId
     * @param bool $isIncidentForms
     * @return boolean
     */
    public function deactivateFormsForAgency($mbsAgencyId, $isIncidentForms)
    {
        try {
            $fnStatus = true;
            $tf = $this->serviceForm->deactivateFormsForAgency($mbsAgencyId, $isIncidentForms);
            if ($tf) {
                $this->logger->log(Logger::INFO, 'Forms deactivated for forms for mbsAgencyId ' . $mbsAgencyId);
            } else {
                $fnStatus = false;
                $this->logger->log(Logger::ERR, 'Failure by form deactivation routine for forms for mbsAgencyId ' . $mbsAgencyId);
            }
            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        }// @codeCoverageIgnoreEnd
    }

    /**
     * Clone enum values for a form matching form template id and state id.
     * @param int $mbsAgencyId
     * @param bool $isIncidentForms
     * @param int $stateId
     * @return bool
     * @throws Exception
     * Note: This function is useful for both new forms and updating enumerations for existing forms.
     */
    public function cloneEnumValsFromExistingForm($mbsAgencyId, $isIncidentForms, $stateId)
    {
        try {

            /*
             * Get the forms to clone to
             */
            $allForms = $this->serviceForm->findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId);

            /*
             * Ensure our query succeeded and that a form was inserted previously. We should get more than one form.
             */
            if (empty($allForms)) {
                $origin = __CLASS__ .'::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Expected forms were not found to create enumerations for, for mbsi agency id: ' . $mbsAgencyId;
                $this->logger->log(Logger::ERR, $errMsg . ' @ ' . $origin);
                return false;
            }

            /*
             * Log info for debug
             */
            $formCnt = count($allForms);
            $allFormIds = explode(',', $this->serviceEcrashUtilsArray->implodeAlt(',', $allForms, 'form_id'));
            $allFormIdsCsv = implode(', ', $allFormIds);
            $msg = 'Found ' . $formCnt . ' universal form ids: ' . $allFormIdsCsv . ' for mbsi agency id: ' . $mbsAgencyId . ' to clone/update enumerations.';
            $this->logger->log(Logger::INFO, $msg);

            /*
             * Get the first form id to clone from (re. SQL in get forms function has order by form_id ASC)
             * This logic assumes the first form in array if good form to clone from.
             */
            $isActive = true;
            $excludeTestForms = true;
            $baseFormInfo = $this->serviceForm->findBaseUniversalFormByFormType($mbsAgencyId, $isIncidentForms, $stateId,
                    $excludeTestForms, $isActive);
            if (empty($baseFormInfo)) {
                //try and find an inactive base form if we cannot find active base form
                $isActive = false;
                $baseFormInfo = $this->serviceForm->findBaseUniversalFormByFormType($mbsAgencyId, $isIncidentForms, $stateId,
                        $excludeTestForms, $isActive);
            }
            if (empty($baseFormInfo)) {
                //try and find an inactive base form if we cannot find active base form include 'test' forms
                $isActive = false;
                $excludeTestForms = false;
                $baseFormInfo = $this->serviceForm->findBaseUniversalFormByFormType($mbsAgencyId, $isIncidentForms, $stateId,
                        $excludeTestForms, $isActive);
            }
            if (empty($baseFormInfo)) {
                $errMsg = 'Could not find a base form to clone enumeration values from for MBS agency id: ' . $mbsAgencyId;
                $this->logger->log(Logger::ERR, $errMsg);
                return false; // cannot clone enums
            }

            if (!empty($baseFormInfo['form_id'])) {
                $formIdToCloneFrom = $baseFormInfo['form_id'];
            }

            if (empty($formIdToCloneFrom)) {
                /*
                 * Last resort, we will try and clone from the first form for the agency itself, however this is a last 
                 * ditch effort that assumes someone has added or fixed the enumeration ids for the agency form. 
                 */
                if (!empty($allFormIds[0])) {
                    $formIdToCloneFrom = $allFormIds[0];
                }
            }

            if (empty($formIdToCloneFrom)) {
                $errMsg = 'Could not derive base form id to clone enumeration values from for MBS agency id: ' . $mbsAgencyId;
                $this->logger->log(Logger::ERR, $errMsg);
                return false;
            } else {
                $msg = 'Derived 1 universal form id: ' . $formIdToCloneFrom . ' for mbsi agency id: ' . $mbsAgencyId . ' to clone/update enumerations FROM.';
                $this->logger->log(Logger::INFO, $msg);
            }

            /*
             * Prep working and message vars
             */
            $formIdsToCloneTo = $allFormIds;
            $formIdsToCloneToCsv = implode(', ', $formIdsToCloneTo);

            /*
             * Log some info to help monitor and debug (TO form ids info)
             */
            $msg = 'Derived ' . $formCnt . ' universal form ids ' . $formIdsToCloneToCsv . ' for mbsi agency id: ' . $mbsAgencyId . ' to clone/update enumerations TO.';
            $this->logger->log(Logger::INFO, $msg);

            /*
             * Fetch enum set to copy from existing enums for the prior existing form id to clone.
             */
			$commonEnumFields = EnumeratorService::COMMON_ENUM_VALS_FIELDS;
            $enumVals = $this->serviceEnumerator->fetchEnumerationValues($formIdToCloneFrom, $commonEnumFields);

            if (empty($enumVals)) {
                $msg = 'Enum values were not found for mbsi agency id: ' . $mbsAgencyId . ' base form id ' . $formIdToCloneFrom . ' to clone/update enumerations from.';
                $this->logger->log(Logger::INFO, $msg);
            }

            /*
             * Clone enum values from base form to other forms found.
             */
            if (!empty($enumVals)) {

                foreach ($formIdsToCloneTo as $formId) {

                    foreach ($enumVals as $enum) {
                        $enumerationFound = false; // used for integrity checking each enum record.
                        $fieldAndVals = [
                            'form_id' => $formId,
                            'enumeration_field_id' => $enum['enumeration_field_id'],
                            'enumeration_value' => $enum['enumeration_value'],
                            'enumeration_value_vendor' => $enum['enumeration_value_vendor'],
                            'field_name' => $enum['field_name']
                        ];

                        if (!empty($enum['additional_info_field_name'])) {
                            $fieldAndVals['additional_info_field_name'] = $enum['additional_info_field_name'];
                        }
                        /*
                         * We check fist to see if the enumeration value already exists. Needed to avoid flooding log
                         * with constraint related errors in some cases (dev, unit test, possibly prod).
                         */
                        $enumValRs = $this->serviceEnumerator->readEnumerationValue($fieldAndVals);
                        if (!empty($enumValRs)) {
                            $enumerationFound = true;
                        } else {
                            $rowsInserted = $this->serviceEnumerator->insertEnumerationValue($fieldAndVals);
                            if ($rowsInserted) {
                                $enumerationFound = true;
                            }
                        }

                        if (!$enumerationFound) {
                            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                            $errMsg = 'Error inserting cloned enum vals from for new form id: ' . $formId . "\r\n";
                            $errMsg .= '...Enum vals attempted to be inserted: ' . print_r($fieldAndVals, true) . "\r\n";
                            $this->logger->log(Logger::ERR, 'ERROR: ' . $errMsg . ' @ ' . $origin);
                        }
                    }
                }
            }
            return true;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } // @codeCoverageIgnoreEnd
    }

    /**
     * Add form work type records for any forms that do not have form work type records
     * @param int $mbsAgencyId
     * @param bool $isIncidentForms
     * @param int $stateId
     * @return bool
     * @throws Exception
     * Note: This function is useful for both new forms and updating enumerations for existing forms.
     */
    public function setupFormWorkTypeRecords($mbsAgencyId, $isIncidentForms, $stateId)
    {
        try {
            $fnStatus = true;
            /*
             * Get the forms to check and/or setup form work type records for
             */
            $allForms = $this->serviceForm->findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId);
            /*
             * Ensure our query succeeded and that a form was inserted previously. We should get more than one form.
             */
            if (empty($allForms)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Expected forms were not found to create form work type records for, for mbsi agency id: ' . $mbsAgencyId;
                $this->logger->log(Logger::ERR, 'ERROR: ' . $errMsg . ' @ ' . $origin);

                return false;
            }

            /*
             * Log info for debug
             */
            $allFormIds = explode(',', $this->serviceEcrashUtilsArray->implodeAlt(',', $allForms, 'form_id'));
            $allFormIdsCsv = implode(', ', $allFormIds);
                        $formCnt = count($allFormIds);
            $msg = 'Found ' . $formCnt . ' universal form ids: ' . $allFormIdsCsv . ' for mbsi agency id: ' . $mbsAgencyId . ' to check/setup form work type records for.';
            $this->logger->log(Logger::INFO, $msg);

            /*
             * Get all valid work types
             */
            $workTypes = $this->serviceFormWorkType->fetchWorkTypes();
            $workTypeIds = explode(',', $this->serviceEcrashUtilsArray->implodeAlt(',', $workTypes, 'work_type_id'));

            foreach ($allFormIds as $formId) {
                $formWorkTypeIds = $this->serviceFormWorkType->fetchWorkTypesByFormId($formId);
                if (empty($formWorkTypeIds)) {
                    /*
                     * Only add form work types for forms that do not have work types setup as we may have already
                     * manually disabled a work type for a form in the past. For initial set, we will add all work
                     * types.
                     */
                    foreach ($workTypeIds as $workTypeId) {
                        $tf = $this->serviceFormWorkType->insertFormWorkType($formId, $workTypeId);
                        if (!$tf) {
                            $fnStatus = false;
                            $msg = 'Failed to add form work type id ' . $workTypeId . ' for form id: ' . $formId . ', MBSI agency id: ' . $mbsAgencyId . ', State id: ' . $stateId;
                            $this->logger->log(Logger::ERR, $msg);
                        }
                    }
                }
            }
            return $fnStatus;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        } // @codeCoverageIgnoreEnd
    }
    
}

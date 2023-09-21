<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;

class UsersExportToAuditHelperService extends BaseService
{
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    public function __construct(
        Array $config,
        Logger $logger)
    {
        $this->config   = $config;
        $this->logger   = $logger;
    }

    /**
     * Map the current environment into the proper env for audit mail env value.
     * @param [string] $currentEnv (defaults to APPLICATION_ENV)
     * @return string
     */
    public function resolveEnvironment($currentEnv = null)
    {
        try {
            if (empty($currentEnv)) {
                $currentEnv = APPLICATION_ENV;
            }
            $envMap = [
                'prod' => 'prod', // Production normal ops
                'dr' => 'dr', // Production backup
                'ua' => 'ua',
                'qc' => 'qc', // QC team normal testing work
                'dev' => 'dev', // Development team use against the dev environment resources
                
                // @TODO: The following environment configurations are not yet added. dr, testing_prod, testing_dr, testing_ua, testing_qc, testing_dev
                /*
                 * Special case environment setup for development testing.
                 */
                'testing_prod' => 'prod', // Development team use against production environment resources
                'testing_dr' => 'dr', // Development team use against production DR environment resources
                'testing_ua' => 'ua', // Development team use against the QC environment resources
                'testing_qc' => 'qc', // Development team use against the QC environment resources
                'testing_dev' => 'dev', // Development team use against the dev environment resources
            ];
            return array_key_exists($currentEnv, $envMap) ? $envMap[$currentEnv] : ucfirst($currentEnv);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            return( ucfirst($currentEnv) );
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Generate the mail subject based on current environment
     * @param [string] $currentEnv (will default to APPLICATION_ENV)
     * @return string
     */
    public function genMailSubjectEnv($currentEnv = null)
    {
        try {
            if (empty($currentEnv)) {
                $currentEnv = APPLICATION_ENV;
            }

            $envMap = [
                'prod' => 'ecrash-keying', // audit understands this is normal production
                'dr' => 'eCrashKeyingDR',
                'ua' => 'eCrashKeyingUA',
                'qc' => 'eCrashKeyingQC',
                'dev' => 'eCrashKeyingDEV',
                /*
                 * Special case environment setup for development testing.
                 */
                'testing_prod' => 'ecrash-keying', // Development team use against production environment resources
                'testing_dr' => 'eCrashKeyingDR', // Development team use against production DR environment resources
                'testing_ua' => 'eCrashKeyingUA', // Development team use against UA environment resources
                'testing_qc' => 'eCrashKeyingQC', // Development team use against the QC environment resources
                'testing_dev' => 'eCrashKeyingDEV', // Development team use against the dev environment resources
            ];
            if (array_key_exists($currentEnv, $envMap)) {
                $mailSubj = $envMap[$currentEnv];
            } else {
                $mailSubj = 'eCrashKeying' . strtoupper($currentEnv);
            }
            return $mailSubj;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            return( 'eCrashKeying' . strtoupper($currentEnv) );
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Generate the lifecycle field value based on current environment
     * @param [string] $currentEnv (will default to APPLICATION_ENV)
     * @return string
     */
    public function genLifeCycle($currentEnv = null)
    {
        try {
            if (empty($currentEnv)) {
                $currentEnv = APPLICATION_ENV;
            }

            $lifeCycleMap = [
                'prod' => 'Production',
                'dr' => 'DR',
                'ua' => 'Staging', // i.e. User Acceptance Testing
                'qc' => 'QA',
                'dev' => 'Dev',
                /*
                 * Special case environment setup for development testing.
                 */
                'testing_prod' => 'Production', // Development team use against production environment resources
                'testing_dr' => 'DR', // Development team use against production DR environment resources
                'testing_ua' => 'Staging', // Development team use against UA environment resources
                'testing_qc' => 'QA', // Development team use against the QC environment resources
                'testing_dev' => 'Dev', // Development team use against the dev environment resources
            ];
            if (array_key_exists($currentEnv, $lifeCycleMap)) {
                $lifeCycle = $lifeCycleMap[$currentEnv];
            } else {
                $lifeCycle = strtoupper($currentEnv);
            }
            return $lifeCycle;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            return( strtoupper($currentEnv) );
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Generate the mail subject for audit based on type of human or system and environment.
     * @param [bool] $humanUsersReport (true)
     * @param [bool] $systemUsersReport (false)
     * @param [string] $currentEnv (will default to APPLICATION_ENV)
     * @return string
     * @throws Exception
     */
    public function genMailSubject($humanUsersReport = true, $systemUsersReport = false, $currentEnv = null)
    {
        try {
            if ($humanUsersReport && $systemUsersReport) {
                throw new Exception('Human and System indicators cannot be on simultaneously. Use only one at a time.');
            }
            if (!$humanUsersReport && !$systemUsersReport) {
                throw new Exception('At least one report type indicator for Human or System is required.');
            }

            if (empty($currentEnv)) {
                $currentEnv = APPLICATION_ENV;
            }

            $environment = $this->resolveEnvironment($currentEnv);

            $mailSubjectEnv = $this->genMailSubjectEnv($environment);

            if ($humanUsersReport) {
                $subjPrefix = 'aud.it';
            } else if ($systemUsersReport) {
                $subjPrefix = 'sysaud.it'; // Indicates Non-human (system) users report.
            }

            $suffix = 'Extract Date - ' . date('Ymd');

            $mailSubj = "{$subjPrefix}:{$mailSubjectEnv}:{$suffix}";

            return $mailSubj;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            die($errMsg);
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Get the audit user reviewer id for review of potentially bad user id account.
     * @return int
     * Note: A valid and configured reviewr_id must be configured. If not, we must die. Per Crystal Wood who rejected
     * idea of sending a default value in the event reviewer_id is not configured.
     */
    public function retrieveReviewerId()
    {
        try {
            $reviewerId = null;
            if (!empty($this->config['app']['auditTool']['reviewerId'])) {
                $reviewerId = $this->config['app']['auditTool']['reviewerId'];
            }

            if (empty($reviewerId) or ! is_numeric($reviewerId)) {
                throw new Exception('Reviewer id must be configured in application.ini >> app.auditTool.reviewerId=###### and cannot be empty. Must be valid people soft user id.');
            }
            return $reviewerId;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            die($errMsg);
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Get the UAR audit system To email address
     * @return string
     */
    public function retrieveUarAuditEmailAddress($humanUsersReport = true, $systemUsersReport = false)
    {
        try {
            if ($humanUsersReport && $systemUsersReport) {
                throw new Exception('Human and System indicators cannot be on simultaneously. Use only one at a time.');
            }
            if (!$humanUsersReport && !$systemUsersReport) {
                throw new Exception('At least one report type indicator for Human or System is required.');
            }
            if ($humanUsersReport) {
                if (!empty($this->config['app']['auditTool']['userList']['email'])) {
                    $email = $this->config['app']['auditTool']['userList']['email'];
                } else {
                    // Default if not in config
                    $email = 'ALP.QueueIT@lexisnexis.com';
                }
            } else if ($systemUsersReport) {
                if (!empty($this->config['app']['auditTool']['nonHumanUserList']['email'])) {
                    $email = $this->config['app']['auditTool']['nonHumanUserList']['email'];
                } else {
                    // Default if not in config
                    $email = 'sysaudit@lexisnexis.com';					
                }
            }
            return $email;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            die($errMsg);
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Get the UAR audit system BCC email address
     * @return string
     */
    public function retrieveUarAuditEmailBccAddress($humanUsersReport = true, $systemUsersReport = false)
    {
        $defaultBccEmailAddress = '';

        try {
            if ($humanUsersReport && $systemUsersReport) {
                throw new Exception('Human and System indicators cannot be on simultaneously. Use only one at a time.');
            }
            if (!$humanUsersReport && !$systemUsersReport) {
                throw new Exception('At least one report type indicator for Human or System is required.');
            }

            $email = $defaultBccEmailAddress; // default to no BCC email address if not specified in config
            if ($humanUsersReport) {
                if (!empty($this->config['app']['auditTool']['userList']['emailBCC'])) {
                    $email = $this->config['app']['auditTool']['userList']['emailBCC'];
                }
            } else if ($systemUsersReport) {
                if (!empty($this->config['app']['auditTool']['nonHumanUserList']['emailBCC'])) {
                    $email = $this->config['app']['auditTool']['nonHumanUserList']['emailBCC'];
                }
            }
            return $email;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            return( $defaultBccEmailAddress );
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Get the mail from address for sending to  the UAR audit system.
     * @return string
     */
    public function retrieveFromEmailAddress()
    {
        $defaultFromEmailAddress = 'ecrash-keying@lexisnexis.com';		
        try {
            if (!empty($this->config['app']['auditTool']['userList']['from'])) {
                $email = $this->config['app']['auditTool']['userList']['from'];
            } else {
                $email = $defaultFromEmailAddress; // Default if not in config
            }
            return( $email );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            return( $defaultFromEmailAddress );
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Generate the base file name for the CSV file.
     * @param [bool] $humanUsersReport (true)
     * @param [bool] $systemUsersReport (false)
     * @return string
     * @throws Exception
     */
    public function genCsvFileName($humanUsersReport = true, $systemUsersReport = false)
    {
        try {
            if ($humanUsersReport && $systemUsersReport) {
                throw new Exception('Human and System indicators cannot be on simultaneously. Use only one at a time.');
            }
            if (!$humanUsersReport && !$systemUsersReport) {
                throw new Exception('At least one report type indicator for Human or System is required.');
            }
            if ($humanUsersReport) {
                $csvFileName = "userAuditExport_" . date('Ymd') . ".csv";
            } else if ($systemUsersReport) {
                $csvFileName = 'nonHumanUserAuditExport_' . date('Ymd') . '.csv';
            }
            return $csvFileName;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $errMsg = 'Exception: ' . $e->getMessage() . ' @ ' . $origin;
            $this->logger->log(Logger::ERR, $errMsg);
            die($errMsg);
        }  // @codeCoverageIgnoreEnd
    }
    
}

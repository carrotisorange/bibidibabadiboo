<?php
namespace Base\Service\Job;

use Zend\Log\Logger;
Use Zend\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

use Base\Service\Job\ProcessCheck\ProcessCheckInterface;
use Base\Service\UserService;
use Base\Service\UsersExportToAuditHelperService;
use Base\Service\MailerService;

class UsersExportToAuditService extends JobAbstract
{
    /**
     * @var Base\Service\UserService 
     */
    protected $serviceUser;

    /**
     * @var Base\Service\UsersExportToAuditHelperService 
     */
    protected $helper;
        
    /**
     * @var Array 
     */
    protected $config;
    
    /**
     * @var Base\Service\MailerService
     */
    protected $serviceMailer;
    
    public function __construct(
        ProcessCheckInterface $jobProcess,
        UserService $serviceUser,
        UsersExportToAuditHelperService $serviceUsersExportToAuditHelper,
        Array $config,
        $log,
        MailerService $serviceMailer)
    {
        parent::__construct(
            $jobProcess,
            $config,
            $log
        );
        
        $this->serviceUser = $serviceUser;
        $this->helper = $serviceUsersExportToAuditHelper;
        $this->config = $config;
        $this->logger = $log;
        $this->serviceMailer = $serviceMailer;
    }
    
    protected function runJob() 
    {
        $this->genNonHumanUsersAuditReport();
        $this->genHumanUsersAuditReport();
    }

    /**
     * Generate non-human users csv mail report to audit system.
     * @param [bool] $sendMail (true) can be used for unit testing to not actually send mail and flood system with logs.
     * @return bool
     */
    protected function genNonHumanUsersAuditReport($sendMail = true)
    {
        try {
            /*
             * Set primary controls for indicaing whether to get human users or non-human (system/bot) user accounts.
             */
            $humanUsersReport = false;
            $systemUsersReport = true;
            $todayDate = date('Ymd');
            /*
             * Setup excluded roles to get only the non-human (system/bot) user list to report.
             */
            $userRolesToExclude = [
                UserService::ROLE_GUEST,
                UserService::ROLE_OPERATOR,
                UserService::ROLE_SUPER_OPERATOR,
                UserService::ROLE_SUPERVISOR,
                UserService::ROLE_ADMIN
            ];
            $userList = $this->serviceUser->getUserList([], true, false, $userRolesToExclude, false, true);
            // Leverage helper to derive various items
            $mailSubjectEnv = $this->helper->genMailSubjectEnv();
            $mailSubject = $this->helper->genMailSubject($humanUsersReport, $systemUsersReport);
            $lifeCycle = $this->helper->genLifeCycle();
            $reviewerId = $this->helper->retrieveReviewerId();
            $csvFileName = $this->helper->genCsvFileName($humanUsersReport, $systemUsersReport);
            $csvFileToAttach = APPLICATION_PATH . '/data/temp/' . $csvFileName;
            $handle = fopen($csvFileToAttach, 'w');
            $counter = 0;
            /*
             * Custom Header for Non-Human (System) User Report 
             * create a header line - header line is NOT processed.
             * Note: This has slightly different structure when first intro'd from legacy Human report.
             */
            $line = [
                'reviewer_id', // field name change for non-human report (emp_id in legacy human report)
                'emp_first_name',
                'emp_last_name',
                'resource_name',
                'resource_type',
                'resource_user_id',
                'role',
                'reference',
                'resource_group',
                'business_unit',
                'lifecycle',
                'user_comment',
                'extract_date',
                'IDS_type' // new field for non-human report. Can contain anything. Free form field.
            ];
            fputcsv($handle, $line);

            foreach ($userList as $user) {
                $counter++;
                $line = [
                    $reviewerId, // is $user['peoplesoftEmployeeId'] in human report
                    ucwords(strtolower($user['nameFirst'])),
                    ucwords(strtolower($user['nameLast'])),
                    'eCrash Keying', #resource_name
                    'Application', #resource_type
                    $user['username'], // resource_user_id
                    $user['role'],
                    '', #reference
                    '', #resource_group
                    'Insurance', #business_unit
                    $lifeCycle, #lifecycle
                    '', #user_comment
                    $todayDate,
                    '' // IDS_type
                ];
                fputcsv($handle, $line);
            }
            fclose($handle);

            $to = $this->helper->retrieveUarAuditEmailAddress($humanUsersReport, $systemUsersReport);
            $bcc = $this->helper->retrieveUarAuditEmailBccAddress($humanUsersReport, $systemUsersReport);
            $from = $this->helper->retrieveFromEmailAddress();
            $mailBodyText = 'CSV for today from ' . $mailSubjectEnv;
            $tf = $this->sendAuditEmail($sendMail, $to, $from, $mailSubject, $mailBodyText, $csvFileToAttach, $csvFileName, $bcc);
            if ($tf) {
                $this->logger->log(Logger::INFO, 'Email to Audit tool sent, amount of non-human system users: ' . $counter);
            } else {
                $errMsg = 'ERROR: Email to Audit tool was NOT sent, amount of non-human system users: ' . $counter;
                $this->logger->log(Logger::ERR, $errMsg);
                $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . __LINE__ . '; mail system malfunciton. Send failure. ' . $errMsg;
                //$this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            }

            return $tf;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Legacy function for generating the human users csv mail report to audit system.
     * @param [bool] $sendMail (true) can be used for unit testing to not actually send mail and flood system with logs.
     * @return bool
     */
    protected function genHumanUsersAuditReport($sendMail = true)
    {
        try {
            /*
             * Set primary controls for indicaing whether to get human users or non-human (system/bot) user accounts.
             */
            $humanUsersReport = true;
            $systemUsersReport = false;
            $todayDate = date('Ymd');
            /*
             * Setup excluded roles to get human user list to report (exclude system/bot roles).
             */
            $userRolesToExclude = [
                UserService::ROLE_SYSTEM
            ];
            $userList = $this->serviceUser->getUserList([], true, false, $userRolesToExclude, true);
            // Leverage helper to derive various items
            $mailSubjectEnv = $this->helper->genMailSubjectEnv();
            $mailSubject = $this->helper->genMailSubject($humanUsersReport, $systemUsersReport);
            $lifeCycle = $this->helper->genLifeCycle();
            $csvFileName = $this->helper->genCsvFileName($humanUsersReport, $systemUsersReport);

            $csvFileToAttach =  APPLICATION_PATH . '/data/temp/' . $csvFileName;
            $handle = fopen($csvFileToAttach, 'w');
            $counter = 0;
            //create a header line - header line is NOT processed.
            //header line names were obtained from documentation
            $line = [
                'emp_id',
                'emp_first_name',
                'emp_last_name',
                'resource_name',
                'resource_type',
                'resource_user_id',
                'role',
                'reference',
                'resource_group',
                'business_unit',
                'lifecycle',
                'user_comment',
                'extract_date'
            ];
            fputcsv($handle, $line);
            foreach ($userList as $user) {
                $counter++;
                $line = [
                    $user['peoplesoftEmployeeId'],
                    ucwords(strtolower($user['nameFirst'])),
                    ucwords(strtolower($user['nameLast'])),
                    'eCrash Keying', #resource_name
                    'Application', #resource_type
                    $user['username'],
                    $user['role'],
                    '', #reference
                    '', #resource_group
                    'Insurance', #business_unit
                    $lifeCycle, #lifecycle
                    '', #user_comment
                    $todayDate
                ];
                fputcsv($handle, $line);
            }
            fclose($handle);
            $to = $this->helper->retrieveUarAuditEmailAddress($humanUsersReport, $systemUsersReport);
            $bcc = $this->helper->retrieveUarAuditEmailBccAddress($humanUsersReport, $systemUsersReport);
            $from = $this->helper->retrieveFromEmailAddress();
            $mailBodyText = 'CSV for today from ' . $mailSubjectEnv;

            $tf = $this->sendAuditEmail($sendMail, $to, $from, $mailSubject, $mailBodyText, $csvFileToAttach, $csvFileName, $bcc);

            if ($tf) {
                $this->logger->log(Logger::INFO, 'Email to Audit tool sent, amount of human users: ' . $counter);
            } else {
                $errMsg = 'ERROR: Email to Audit tool was NOT sent, amount of human users: ' . $counter;
                $this->logger->log(Logger::ERR, $errMsg);
                $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . __LINE__ . '; mail system malfunciton. Send failure. ' . $errMsg;
                $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            }
            
            return $tf;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        }  // @codeCoverageIgnoreEnd
    }

    /**
     * Sends an email with attachment to audit system.
     * @param bool $sendMail (true) if false will skip actual send, i.e. for unit testing.
     * @param string $to email address for to user
     * @param string $from email address for from user
     * @param string $mailSubject text for subject line
     * @param string $mailBodyText text for body
     * @param string $csvFileToAttach full path of the csv file to attach
     * @param string $csvFileName base filename to use for user download when detaching the csv
     * @param [bool] $bcc (null)
     * @param [bool] $cc (null)
     * @param [bool] $unlinkCsvFile (true)
     * @return boolean
     */
    public function sendAuditEmail($sendMail, $to, $from, $mailSubject, $mailBodyText, $csvFileToAttach, $csvFileName,
            $bcc = null, $cc = null, $unlinkCsvFile = true)
    {
        try {
            if ($sendMail) {
                $mailConfig = new \stdClass();
                $mailConfig->subject = $mailSubject;
                $mailConfig->to = $to;
                $mailConfig->from = $from;
                $mailConfig->body = $mailBodyText;
                $mailConfig->bcc = $bcc;
                $mailConfig->cc = $cc;
                $variables = ['attachmentFile' => $csvFileToAttach, 'attachmentFileName' => $csvFileName];
                
                try {
                    /*
                     * If no exception, mail is accepted for delivery, note however does not guarantee delivery 
                     * if transport service fails. If an exception occurs, then TransportInterface (Smtp or Sendmail) 
                     * failed and tossed a TransportInterface (Smtp or Sendmail) likely.
                     */
                    $this->serviceMailer->sendTextMail($mailConfig, $variables);
                    $tf = true;
                } catch (Exception $e) {
                    $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . __LINE__ . '; exception line: ' . $e->getLine();
                    $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
                    $tf = false;
                }
            } else {
                // for some basic unit testing, do not send, but mock success or failure for unit testing.
                $tf = (bool) rand(0, 1);
            }

            if ($unlinkCsvFile) {
                unlink($csvFileToAttach);
            }
            
            return $tf;
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '; L' . $e->getLine();
            $this->logger->log(Logger::ERR, 'Exception: ' . $e->getMessage() . ' @ ' . $origin);
            return false;
        }  // @codeCoverageIgnoreEnd
    }
    
}

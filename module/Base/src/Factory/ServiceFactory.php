<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Factory;

use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Soap\Client as SoapClient;
use Zend\Http\Client as HttpClient;
use SoapFault;
use Exception;
use RuntimeException;
use InvalidArgumentException;
use Zend\Log\Writer\Stream;
use Zend\Log\Writer\Noop;
use Zend\Log\Filter\Priority;

use Base\Adapter\Db\BaseAdapter;
use Base\Adapter\Db\FormTypeAdapter;
use Base\Adapter\Db\AgencyAdapter;
use Base\Adapter\Db\StateAdapter;
use Base\Adapter\Db\UserAdapter;
use Base\Adapter\Db\UserRoleAdapter;
use Base\Adapter\Db\ReportStatusAdapter;
use Base\Adapter\Db\UserAccuracyAdapter;
use Base\Adapter\Db\UserAccuracyInvalidAdapter;
use Base\Adapter\Db\VinStatusAdapter;
use Base\Adapter\Db\EntryStageAdapter;
use Base\Adapter\Db\FormAdapter;
use Base\Adapter\Db\FormWorkTypeAdapter;
use Base\Adapter\Db\WorkTypeAdapter;
use Base\Adapter\Db\ReportAdapter;
use Base\Adapter\Db\UserEntryStageAdapter;
use Base\Adapter\Db\UserNoteAdapter;
use Base\Adapter\Db\UserFormPermissionAdapter;
use Base\Adapter\Db\IsitTicketAdapter;
use Base\Adapter\Db\IsitTicketStatusAdapter;
use Base\Adapter\Db\IsitTicketTypeAdapter;
use Base\Adapter\Db\IsitTicketLogAdapter;
use Base\Adapter\Db\IsitTicketLogTypeAdapter;
use Base\Adapter\Db\ReportEntryAdapter;
use Base\Adapter\Db\ReportEntryQueueAdapter;
use Base\Adapter\Db\FormsToRekeyAdapter;
use Base\Adapter\Db\RekeyUserFormPermissionAdapter;
use Base\Adapter\REST\Isit\CurlAdapter as IsitCurlAdapter;
use Base\Adapter\Soap\MbsAuthAdapter;
use Auth\Adapter\Soap\MaeAuthAdapter;
use Auth\Adapter\REST\LNAAAuthAdapter;
use Auth\Adapter\Soap\IpRestrictAdapter;
use Base\Adapter\Db\ReadOnly\ReportStatusAdapter as ReadOnlyReportStatusAdapter;
use Base\Adapter\Db\ReadOnly\ReportAdapter as ReadOnlyReportAdapter;
use Base\Adapter\Db\ReadOnly\AgencyAdapter as ReadOnlyAgencyAdapter;
use Base\Adapter\Db\ReadOnly\StateAdapter as ReadOnlyStateAdapter;
use Base\Adapter\Db\ReadOnly\FormAdapter as ReadOnlyFormAdapter;
use Base\Adapter\Db\ReadOnly\ReportEntryAdapter as ReadOnlyReportEntryAdapter;
use Base\Adapter\Db\ReadOnly\VinStatusAdapter as ReadonlyVinStatusAdapter;
use Base\Adapter\Db\ReportNoteAdapter;
use Base\Adapter\Db\ReportCruAdapter;
use Base\Adapter\Db\FormSystemAdapter;
use Base\Adapter\Db\FormFieldAdapter;
use Base\Adapter\Db\ReportEntryDataAdapter;
use Base\Adapter\Db\EntryStageProcessAdapter;
use Base\Adapter\Db\FormFieldCommonAdapter;
use Base\Adapter\Db\UserEntryPrefetchAdapter;
use Base\Adapter\Db\ReportEntryQueueHistoryAdapter;
use Base\Adapter\Db\FormCodeGroupConfigurationAdapter;
use Base\Adapter\Db\FormCodeMapAdapter;
use Base\Adapter\Db\FormFieldAttributeAdapter;
use Base\Adapter\Db\ReportQueueAdapter;
use Base\Adapter\Db\ReportQueueHistoryAdapter;
use Base\Adapter\Db\FlagAdapter;
use Base\Adapter\Db\ReportFlagAdapter;
use Base\Adapter\Db\ReportFlagHistoryAdapter;
use Base\Adapter\Db\FormNoteAdapter;
use Base\Adapter\Db\VendorAdapter;
use Base\Adapter\Db\Extract\EnumerationValueAdapter;
use Base\Adapter\Db\Extract\EnumerationMapAdapter;
use Base\Adapter\Db\Extract\EnumerationFieldAdapter;
use Base\Adapter\Db\Mbs\AgencyAdapter as MbsAgencyAdapter;
use Base\Adapter\Db\Mbs\AgencyContributorySourceAdapter as MbsAgencyContributorySourceAdapter;
use Base\Adapter\Db\AgencyContributorySourceAdapter;
use Base\Adapter\Db\FormCodeGroupAdapter;
use Base\Adapter\Db\FormCodeListAdapter;
use Base\Adapter\Db\FormCodeListGroupMapAdapter;
use Base\Adapter\Db\FormCodeListPairMapAdapter;
use Base\Adapter\Db\FormCodePairAdapter;
use Base\Adapter\Db\StateConfigurationAdapter;
use Base\Adapter\Db\ReportEntryDataValueAdapter;
use Base\Adapter\Db\AutoExtractionDataAdapter;
use Base\Adapter\Db\AutoExtractionImageProcessAdapter;
use Base\Adapter\Db\AutoExtractionAccuracyAdapter;
use Base\Adapter\Db\AutozoningDataCoordinateAdapter;
use Base\Adapter\Db\KeyingVendorAdapter;
use Base\Adapter\Db\QualityControlRemarkAdapter;


class ServiceFactory extends BaseFactory
{
    /**
     * @var Base\Adapter\Db\BaseAdapter
     */
    protected $adapterBase;
    
    protected function getDbAdapter(Array $config)
    {
        return $this->getBaseAdapter($config, true);
    }
    
    /**
     * To get the base adapter
     * @param array     $config         Application configuration
     * @param boolean   $returnObject   True/False
     * @return void/object              Return void or [Base\Adapter\Db\BaseAdapter] object
     */
    protected function getBaseAdapter(Array $config, $returnObject = false)
    {
        if (empty($this->adapterBase)) {
            $this->adapterBase = new BaseAdapter($config);
        }
        
        if (!empty($returnObject)) {
            return $this->adapterBase;
        }
    }
    
    /**
     * To get the user adapter
     * @param array     $config     Application configuration
     * @param object    $logger  [Zend\Log\Logger]
     * @return object               [Base\Adapter\Db\UserAdapter]
     */
    public function getUserAdapter(Array $config, Logger $logger)
    {
        $this->getBaseAdapter($config);

        return new UserAdapter(
            $this->adapterBase->getMasterDbAdapter(),
            $this->getLnaaAuthAdapter($config, $logger)
        );
    }
    
    /**
     * To get the user adapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\UserRoleAdapter]
     */
    public function getUserRoleAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new UserRoleAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the user adapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\EntryStageAdapter]
     */
    public function getEntryStageAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new EntryStageAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * @return Zend\Soap\Client
     */
    protected function getSoapClient($configSoap, $wsdlLocation, $login = null, $password = null, $soapVersion = null)
    {
        try {
            $params = [
                'encoding' => $configSoap['encoding'],
                //need to do this since verisign has self-signed certificate
                'stream_context'=> stream_context_create(
                    [
                        'ssl'=> [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ]
                )
            ];
            
            if (empty($soapVersion)) {
                $params['soap_version'] = $configSoap['version'];
            } else {
                $params['soap_version'] = $soapVersion;
            }
            
            if (!empty($login)) {
                $params['login'] = $login;
            }
            
            if (!empty($password)) {
                $params['password'] = $password;
            }
            
            return new SoapClient(
                $wsdlLocation, $params
            );
            // @codeCoverageIgnoreStart
            // TODO: Store exception in application log file
        } catch (Exception $e) {
            /**
              * @TODO: Store the soap exception in application log.
              */
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            //$logger = $container->get('Zend\Log');
            //$logger->crit($origin . $e->getMessage());
            return null;
        }// @codeCoverageIgnoreEnd
    }
    
    public function getMaeAuthSoapClient($wsdl)
    {
        try {
            $options = [
                //need to do this since verisign has self-signed certificate
                'stream_context'=> stream_context_create(
                    [
                        'ssl'=> [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ]
                )
            ];
            
            return new SoapClient($wsdl, $options);
            // @codeCoverageIgnoreStart
        } catch (SoapFault $e) {
            return null;
        }  catch (Exception $e) {
            return null;
        } catch (RuntimeException $e) {
            return null;
        }// @codeCoverageIgnoreEnd
    }

    public function getLnaaAuthHttpClient($lnaaAuthUrl)
    {
        try {
            $options = [
                //need to do this since verisign has self-signed certificate
                'stream_context'=> stream_context_create(
                    [
                        'ssl'=> [
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        ]
                    ]
                )
            ];
            
            return new HttpClient($lnaaAuthUrl, $options);               
        }  catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();            
            $logger->log(Logger::ERR, $origin . $e->getMessage());            
            return null;
        } catch (InvalidArgumentException $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            $logger->log(Logger::ERR, $origin . $e->getMessage());            
            return null;
        }
    }
    
    /**
     * To get the user adapter
     * @param array     $config     Application configuration
     * @return object               [Auth\Adapter\Soap\MaeAuthAdapter]
     */
    public function getMaeAuthAdapter(Array $config)
    {
        return new MaeAuthAdapter(
            $config,
            $this->getMaeAuthSoapClient($config['app']['maeAuthWsdl'])
        );
    }

    /**
     * To get the user adapter
     * @param array     $config     Application configuration
     * @param object    $logger     Zend\Log\Logger
     * @return object               [Auth\Adapter\REST\LNAAAuthAdapter]
     */
    public function getLnaaAuthAdapter(Array $config, $logger)
    {
        return new LNAAAuthAdapter(
            $logger,
            $config,
            $this->getLnaaAuthHttpClient($config['app']['lnaaAuthUrl'])
        );
    }
    
    /**
     * To get the MbsAuthAdapter
     * @param array     $config     Application configuration
     * @param object    $container  [Interop\Container\ContainerInterface]
     * @return object               [Base\Adapter\Soap\MbsAuthAdapter]
     */
    public function getMbsAuthAdapter(Array $config, ContainerInterface $container)
    {
        $configSoap = $config['app']['soap'];
        
        return new MbsAuthAdapter(
            $container->get('Logger'),
            $config,
            $this->getSoapClient($configSoap, $config['app']['mbsAuthWsdl'])
        );
    }
    
    public function getAgencyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new AgencyAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the state adapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\StateAdapter]
     */
    public function getStateAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new StateAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    public function getReportStatusAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportStatusAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the ReportEntryAdapter
     * @param array     $config Application configuration
     * @return object           [Base\Adapter\Db\ReportEntryAdapter]
     */
    public function getReportEntryAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReportEntryAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IpRestrictAdapter
     * @param array     $config     Application configuration
     * @return object               [Auth\Adapter\Soap\IpRestrictAdapter]
     */
    public function getIpRestrictAdapter(Array $config)
    { 
        $soap = $config['app']['soap'];
        $wsdl = $config['app']['ipRestrict']['wsdl'];
        $applicationIdentifier = $config['app']['ipRestrict']['applicationIdentifier'];
        
        return new IpRestrictAdapter(
            $this->getSoapClient($soap, $wsdl),
            $applicationIdentifier
        );
    }
    
    public function getFormTypeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return new FormTypeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the FormAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormAdapter]
     */
    public function getFormAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    public function getReadOnlyReportStatusAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyReportStatusAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadOnlyReportAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyReportAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadOnlyAgencyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyAgencyAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadOnlyStateAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyStateAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadOnlyFormAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyFormAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadOnlyReportEntryAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadOnlyReportEntryAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }

    public function getReadonlyVinStatusAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReadonlyVinStatusAdapter(
            $this->adapterBase->getSlaveDbAdapter()
        );
    }
    
    /**
     * To get the FormWorkTypeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormWorkTypeAdapter]
     */
    public function getFormWorkTypeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormWorkTypeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the WorkTypeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\WorkTypeAdapter]
     */
    public function getWorkTypeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new WorkTypeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the ReportAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportAdapter]
     */
    public function getReportAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the UserEntryStageAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\UserEntryStageAdapter]
     */
    public function getUserEntryStageAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserEntryStageAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the UserNoteAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\UserNoteAdapter]
     */
    public function getUserNoteAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserNoteAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the UserFormPermissionAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\UserFormPermissionAdapter]
     */
    public function getUserFormPermissionAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserFormPermissionAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IsitTicketAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\IsitTicketAdapter]
     */
    public function getIsitTicketAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new IsitTicketAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IsitTicketStatusAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\IsitTicketStatusAdapter]
     */
    public function getIsitTicketStatusAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new IsitTicketStatusAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IsitTicketTypeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\IsitTicketTypeAdapter]
     */
    public function getIsitTicketTypeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new IsitTicketTypeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IsitTicketLogAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\IsitTicketLogAdapter]
     */
    public function getIsitTicketLogAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new IsitTicketLogAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the IsitTicketLogTypeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\IsitTicketLogTypeAdapter]
     */
    public function getIsitTicketLogTypeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new IsitTicketLogTypeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    public function getClientIsitCurl($config, $container)
    {
        $logger = $container->get('Logger');
        try {
            $clientHttpConfig = [
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => [
                    CURLOPT_CONNECTTIMEOUT => 120,  // Number of seconds to wait for connect
                    CURLOPT_TIMEOUT        => 60,   // Number of seconds to wait for response
                ],
            ];
            $clientHttp = new HttpClient(null, $clientHttpConfig);
            $configMessageQueue = $config['app']['webService']['messageQueue'];
            
            return new IsitCurlAdapter(
                $configMessageQueue['httpSecure'],
                $configMessageQueue['domain'],
                $configMessageQueue['isitLogin'],
                $configMessageQueue['isitPassword'],
                $configMessageQueue['ecrashAppId'],
                $clientHttp,
                $config
            );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            #$errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $logger->log(Logger::ERR, $origin . $e->getMessage());
            
            return null;
        } catch (InvalidArgumentException $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . '() L' . $e->getLine();
            $logger->log(Logger::ERR, $origin . $e->getMessage());
            
            return null;
        }// @codeCoverageIgnoreEnd
    }
    
    /**
     * To get the ReportNoteAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportNoteAdapter]
     */
    public function getReportNoteAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportNoteAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the ReportCruAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportCruAdapter]
     */
    public function getReportCruAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportCruAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormSystemAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormSystemAdapter]
     */
    public function getFormSystemAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormSystemAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormFieldAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormFieldAdapter]
     */
    public function getFormFieldAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormFieldAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the ReportEntryDataAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportEntryDataAdapter]
     */
    public function getReportEntryDataAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportEntryDataAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the EntryStageProcessAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\EntryStageProcessAdapter]
     */
    public function getEntryStageProcessAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new EntryStageProcessAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormFieldCommonAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormFieldCommonAdapter]
     */
    public function getFormFieldCommonAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormFieldCommonAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the UserEntryPrefetchAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\UserEntryPrefetchAdapter]
     */
    public function getUserEntryPrefetchAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserEntryPrefetchAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the ReportEntryQueueAdapter
     * @param array     $config     Application configuration
     * @param object    $container  [Interop\Container\ContainerInterface]
     * @return object               [Base\Adapter\Db\ReportEntryQueueAdapter]
     */
    public function getReportEntryQueueAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);
        return new ReportEntryQueueAdapter(
            $this->adapterBase->getMasterDbAdapter(),
            $container->get('Logger'),
            $config
        );
    }

    /**
     * To get the ReportEntryQueueHistoryAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportEntryQueueHistoryAdapter]
     */
    public function getReportEntryQueueHistoryAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportEntryQueueHistoryAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormCodeGroupConfigurationAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormCodeGroupConfigurationAdapter]
     */
    public function getFCGCAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormCodeGroupConfigurationAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormCodeMapAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormCodeMapAdapter]
     */
    public function getFormCodeMapAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormCodeMapAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormFieldAttributeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormFieldAttributeAdapter]
     */
    public function getFormFieldAttributeAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormFieldAttributeAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FormFieldAttributeAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FormFieldAttributeAdapter]
     */
    public function getReportQueueAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportQueueAdapter(
            $this->adapterBase->getMasterDbAdapter(),
            $this->getReportQueueHistoryAdapter($config),
            $this->getEntryStageAdapter($config)
        );
    }

    /**
     * To get the ReportQueueHistoryAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportQueueHistoryAdapter]
     */
    public function getReportQueueHistoryAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportQueueHistoryAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the FlagAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\FlagAdapter]
     */
    public function getFlagAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FlagAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the ReportFlagAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\ReportFlagAdapter]
     */
    public function getReportFlagAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new ReportFlagAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the ReportFlagHistoryAdapter
     * @param array     $config     Application configuration
     * @param object    $container  [Interop\Container\ContainerInterface]
     * @return object               [Base\Adapter\Db\ReportFlagHistoryAdapter]
     */
    public function getReportFlagHistoryAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);
        return new ReportFlagHistoryAdapter(
            $this->adapterBase->getMasterDbAdapter(),
            $container->get('Logger')
        );
    }

    /**
     * To get the FormsToRekeyAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormsToRekeyAdapter]
     */
    public function getFormsToRekeyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new FormsToRekeyAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the RekeyUserFormPermissionAdapter
     * @param array   $config   Application configuration
     * @return object           [Base\Adapter\Db\RekeyUserFormPermissionAdapter]
     */
    public function getRekeyUserFormPermissionAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new RekeyUserFormPermissionAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    public function getFormNoteAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormNoteAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the UserAccuracyAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\UserAccuracyAdapter]
     */
    public function getUserAccuracyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserAccuracyAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the UserAccuracyInvalidAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\UserAccuracyInvalidAdapter]
     */
    public function getUserAccuracyInvalidAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new UserAccuracyInvalidAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    public function getVendorAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return new VendorAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    protected function getJobLog(Array $config, $jobName)
    {
        $log = new Logger();
        if (!empty($config['logging']['enabled'])) {
            $logFile = $config['logging']['jobs']['dir'] . '/' . date('Ymd') . '_' . $jobName . '.log';
            $writer = new Stream($logFile);
            $priority = (empty($config['logging']['jobs']['priority']))
                ? Logger::DEBUG
                : constant(get_class($log) . '::' . $config['logging']['jobs']['priority']);
            $filter = new Priority($priority);
            $writer->addFilter($filter);
        } else {
            $writer = new Noop();
        }
        $log->addWriter($writer);
        
        return $log;
    }

    public function getEnumerationValueAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);

        return new EnumerationValueAdapter($this->adapterBase->getMasterDbAdapter(), $container->get('Logger'));
    }

    public function getEnumerationMapAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return new EnumerationMapAdapter($this->adapterBase->getMasterDbAdapter());
    }

    public function getEnumerationFieldAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return new EnumerationFieldAdapter($this->adapterBase->getMasterDbAdapter());
    }

    public function getMbsAgencyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return new MbsAgencyAdapter($this->adapterBase->getMbsDbAdapter());
    }

    public function getMbsAgencyContributorySourceAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);

        return new MbsAgencyContributorySourceAdapter($this->adapterBase->getMbsDbAdapter(), $container->get('Logger'));
    }

    public function getAgencyContributorySourceAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);

        return new AgencyContributorySourceAdapter($config, $this->adapterBase->getMasterDbAdapter(), $container->get('Logger'));
    }

    public function getAdapter(Array $config)
    {
        $this->getBaseAdapter($config);

        return $this->adapterBase->getMasterDbAdapter();
    }
    
    /**
     * To get the FormCodeGroupAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormCodeGroupAdapter]
     */
    public function getFormCodeGroupAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormCodeGroupAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the FormCodeListAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormCodeListAdapter]
     */
    public function getFormCodeListAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormCodeListAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the FormCodeListGroupMapAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormCodeListGroupMapAdapter]
     */
    public function getFormCodeListGroupMapAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormCodeListGroupMapAdapter($this->adapterBase->getMasterDbAdapter());
    }

    /**
     * To get the FormCodeListPairMapAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormCodeListPairMapAdapter]
     */
    public function getFormCodeListPairMapAdapter(Array $config, ContainerInterface $container)
    {
        $this->getBaseAdapter($config);
        
        return new FormCodeListPairMapAdapter($this->adapterBase->getMasterDbAdapter(), $container->get('Logger'));
    }
    
    /**
     * To get the FormCodePairAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\FormCodePairAdapter]
     */
    public function getFormCodePairAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new FormCodePairAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    public function getStateConfigurationAdapter(Array $config){
        $this->getBaseAdapter($config);
        
        return new StateConfigurationAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the ReportEntryDataValueAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\ReportEntryDataValueAdapter]
     */
    public function getReportEntryDataValueAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new ReportEntryDataValueAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the AutoExtractionDataAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\AutoExtractionDataAdapter]
     */
    public function getAutoExtractionDataAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new AutoExtractionDataAdapter($this->adapterBase->getMasterDbAdapter());
    }

    /**
     * To get the AutoExtractionImageProcessAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\AutoExtractionImageProcessAdapter]
     */
    public function getAutoExtractionImageProcessAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        
        return new AutoExtractionImageProcessAdapter($this->adapterBase->getMasterDbAdapter());
    }
    
    /**
     * To get the AutoExtractionAccuracyAdapter
     * @param array   $config       Application configuration
     * @return object               [Base\Adapter\Db\AutoExtractionAccuracyAdapter]
     */
    public function getAutoExtractionAccuracyAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new AutoExtractionAccuracyAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
    
    /**
     * To get the KeyingVendorAdapater
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\KeyingVendorAdapater]
     */
    public function getKeyingVendorAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new KeyingVendorAdapter(
             $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the QualityControlRemarkAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\QualityControlRemarkAdapter]
     */
    public function getQualityControlRemarkAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new QualityControlRemarkAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }

    /**
     * To get the QualityControlRemarkAdapter
     * @param array     $config     Application configuration
     * @return object               [Base\Adapter\Db\QualityControlRemarkAdapter]
     */
    public function getAutozoningDataCoordinateAdapter(Array $config)
    {
        $this->getBaseAdapter($config);
        return new AutozoningDataCoordinateAdapter(
            $this->adapterBase->getMasterDbAdapter()
        );
    }
}

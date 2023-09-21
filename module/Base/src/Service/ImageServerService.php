<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;
use Exception;
use Zend\Soap\Client;

use Base\Adapter\Db\ReportAdapter;

class ImageServerService extends BaseService
{    
    const STATUS_ERROR = 'ERROR';
    const FORMAT_PDF = 'PDF';
    const COLOR_OPTION_LOW_GRAY = 'LOW_GRAY';
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;

    /**
     * @var Base\Adapter\Db\ReportAdapter
     */
    protected $adapterReport;
    
    public function __construct(
        Array $config,
        Logger $logger,
        ReportAdapter $adapterReport,
		Client $client)
    {
		$this->config   = $config;
		$this->logger   = $logger;
		$this->adapterReport   = $adapterReport;
		$this->client = $client;
    }
    
    /**
     * Pulls pdf image from ImageServer based on reportId
     * @param mixed $reportId string or number for CDI report id.
     * @param [string] $imageSavePath (null) override default image save path if set. Full path to file including ext.
     * @param [bool] $cacheEnabled (true) set to false to disable file based cache check and force repull (unit test use).
     * @return bool
     * Note: default $imageSavePath is: APPLICATION_PATH . '/../public/images/reports/' . $reportId . '.pdf'
     */
    public function pullImageFromServer($reportId, $imageSavePath = null, $cacheEnabled = true) {
        try {
            if (empty($imageSavePath)) {
                $imageSavePath = APPLICATION_PATH .'/public/images/reports/' . $reportId . '.pdf';
                //$imageSavePath = '/datahub_dump/Keying/reports/' . $reportId . '.pdf';
            }
            /*
             * If file cache enabled, check for image existence to bypass web service call. Also do a sanity check
             * on file size to ensure we do not have some null image in file. We use 1 byte for sanity check although
             * this should likely be configured, and the images should ultimately be aged. @LNTODO
             */
            if ($cacheEnabled && file_exists($imageSavePath) && filesize($imageSavePath) > 1) {
                @chmod($imageSavePath, 0777); // just in case perms were hit by external sources (precautionary)
                return true;
            }
            
            /*
             * Get report to get image hash key and work type info.
             */
            $report = $this->adapterReport->getHashKey($reportId);
            if (empty($report) || empty($report['hash_key'])) {
                $this->logger->log(Logger::ERR, 'ImageServer: Report Info Retrieval Failure. Cannot retrieve hashkey to pull image.');
                return false;
            }
            
            $imageHashKey = $report['hash_key'];
            $workTypeId = $report['work_type_id'];
            
            //@TODO: Local environment response should be removed in future
            if (APPLICATION_ENV != 'local') {
                $return = $this->retrieveImageFromWebService($imageHashKey, $workTypeId);
            } else {
                $return = (object) ['status' => (object) ['success' => false]];
            }
            
            if (empty($return) || !is_object($return) || (!empty($return->status) && !$return->status->success)) {
                $this->logger->log(Logger::ERR, 'ImageServer: Data from Image Server Failed: ' 
                    . var_export($return->status, true)
                );
                return false;
            }

            $data = base64_decode($return->output->content);
            
            if (empty($data)) {
                $this->logger->log(Logger::ERR, 'ImageServer: Data from Image Server Failed (No doc attached): ' 
                    . var_export($return->status, true)
                );
                return false;
            }
            
            $bytesWritten = file_put_contents($imageSavePath, $data); // false if failed to write file else byte count
            if (!$bytesWritten) {
                $this->logger->log(Logger::ERR, 'ImageServer: Failed to write image data to save path cache file: ' . $imageSavePath);
                return false;
            } else {
                chmod($imageSavePath, 0777);
            }

            $this->logger->log(Logger::DEBUG, 'ImageServer: Successfully pulled image from image server');

            return true;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, 'ImageServer: pullImageFromServer failed : report id = ' . $reportId . ' : ' . $e->getMessage());
            return false;
        }   
    }

    /**
     * Retrieve an image object from web service
     * @param string $imageHash
     * @param int $workTypeId
     * @return object
     */
    public function retrieveImageFromWebService($imageHash, $workTypeId) 
    {
        try {
            if (isset($this->config) && isset($this->config['retrieveImageFromWebService'])
                && isset($this->config['retrieveImageFromWebService']['enabled'])
                && !$this->config['retrieveImageFromWebService']['enabled']) {
                
                //Return blank image template
                $blankImageFile = file_get_contents(APPLICATION_PATH . '/public/images/blank-image.pdf');
                return (object) [
                    'output' => [
                        'content' => $blankImageFile
                    ],
                    'status' => [
                        'success' => true
                    ]
                ];
            }

            $request = $this->getRetrieveImageRequest($imageHash, $workTypeId);
            $oldTimeout = ini_set('default_socket_timeout', $this->config['imageWSDL']['timeout']);
            $oldMaxExecutionTime = ini_set('max_execution_time', $this->config['imageWSDL']['timeout']);
            $response = $this->client->process($request);
            ini_set('default_socket_timeout', $oldTimeout);
            ini_set('max_execution_time', $oldMaxExecutionTime);
            
            return $response;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            ini_set('default_socket_timeout', $oldTimeout);
            ini_set('max_execution_time', $oldMaxExecutionTime);
            $this->logger->log(Logger::ERR, 'ImageServer: retrieveImageFromWebService failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function getRetrieveImageRequest($imageHash, $workTypeId, $imageType = null)
    {
        $retrieveParams = [
            'hash' => $imageHash,
            'format' => empty($imageType) ? self::FORMAT_PDF : $imageType,
            'modify' => [
                'color' => self::COLOR_OPTION_LOW_GRAY
            ]
        ];
        /*
         * Removing cover page for cru.
         */
        if ($workTypeId != '1') {
            $retrieveParams['pages'] = [
                'remove' => [
                    'page' => 0
                ]
            ];
        }
        $request = [
            'retrieve' => $retrieveParams
        ];
        
        return $request;
    }
}

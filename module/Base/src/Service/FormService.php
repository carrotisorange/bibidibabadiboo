<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Zend\Log\Logger;

use Base\Adapter\Db\FormAdapter;
use Base\Adapter\Db\ReadOnly\FormAdapter as ReadOnlyFormAdapter;
use Base\Adapter\Db\FormSystemAdapter;
use Base\Service\DataTransformerService;

class FormService extends BaseService
{
    /**
     * @TODO: Will be removed
     * Silver light form constant
     */
    //const SYSTEM_IYETEK = 'iyetek';
    
    const SYSTEM_UNIVERSAL = 'universal';
    const TEMPLATE_UNIVERSAL = 'universal-sectional';
    const TEMPLATE_SILVERLIGHT = 'silverlight';
    const SILVERLIGHT_FORM = 'silverlight form'; // used for logs and messages
    const UNIVERSAL_FORM = 'universal form'; // used for logs and messages
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\FormAdapter
     */
    protected $adapterForm;

    /**
     * @var Base\Adapter\Db\ReadOnly\FormAdapter
     */
    protected $adapterReadOnlyForm;

    /**
     * @var Base\Adapter\Db\FormSystemAdapter
     */
    protected $adapterFormSystem;

    /**
     * @var Base\Service\DataTransformerService
     */
    protected $serviceDataTransformer;
    
    public function __construct(
        Array $config,
        Logger $logger,
        FormAdapter $adapterForm,
        ReadOnlyFormAdapter $adapterReadOnlyForm,
        FormSystemAdapter $adapterFormSystem,
        DataTransformerService $serviceDataTransformer)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterForm = $adapterForm;
        $this->adapterReadOnlyForm = $adapterReadOnlyForm;
        $this->adapterFormSystem = $adapterFormSystem;
        $this->serviceDataTransformer = $serviceDataTransformer;
    }
    
    public function getFormsWithReports()
    {
        return $this->adapterReadOnlyForm->fetchFormsWithReports();
    }

    public function getFormInfo($formId)
    {
        return $this->adapterForm->getFormInfo($formId);
    }
    
    /**
     * To get list of agency based on the selected state/agency
     * @param int $stateId
     * @param int $agencyId
     * @return array, List of agency
     */
    public function getFormIdNamePairs($stateId = null, $agencyId = null, $duration = null)
    {
        $result = $this->adapterForm->getFormIdNamePairs($stateId, $agencyId, $duration);
        
        $forms = [];
        foreach($result as $key => $row) {
            $forms[$row['formId']] = $row['formName'] . ' - ' . $row['formState'] . ' - ' .
                ($row['formAgency'] ? $row['formAgency'] . ' - ' : '') . $row['formType'];
        }
        
        return $forms;
    }
    
    public function getTemplateNameInternal($formId)
    {
        $result = $this->adapterForm->fetchTemplateNameInternal($formId);
        return $result['name_internal'];
    }
    
    public function getDataTransformerByFormId($formId, $rekeyPreferred = false)
    {
        if (!empty($formId)) {
            return $this->getDataTransformationObjectByFormId(
                $formId, 'getDataTransformer', $rekeyPreferred
            );
        }
    }

    public function getDataTransformerByFormSystemId($formSystemId)
    {
        if (empty($formSystemId)) {
            return;
        }
        $formSystemInfo = $this->adapterFormSystem->getFormSystemName($formSystemId);
        
        $methodName = 'getDataTransformer' . ucfirst($formSystemInfo['name_internal']);
        if (method_exists($this->serviceDataTransformer, $methodName)) {
            return $this->serviceDataTransformer->$methodName($this->logger);
        }
    }

    public function getAlternativeFormPairs($formId, $userId, $workTypeId)
    {
        if (empty($formId) || empty($userId) || empty($workTypeId)) {
            return;
        }
        $formInfo = $this->getFormInfo($formId);
        $allowedForms = $this->adapterForm->getAllowedFormPairs(
                $userId, $formInfo['stateId'], $workTypeId, !empty($formInfo['agencyId'])
        );
        // removing current form from the list of alternatives
        unset($allowedForms[$formId]);
        
        return $allowedForms;
    }

    public function updateAttributeGroup($formId, $attributeGroupId)
    {
        return $this->adapterForm->updateAttributeGroup($formId, $attributeGroupId);
    }
    
    protected function getDataTransformationObjectByFormId($formId, $factoryMethodBaseName, $rekeyPreferred = false)
    {
        if (empty($formId) || empty($factoryMethodBaseName)) {
            return;
        }

        $methodToCall = null;
        $formInfo = $this->getFormInfo($formId);

        $methodName = $factoryMethodBaseName . ucfirst(strtolower($formInfo['formSystem']));
        $methodNameStateSpecific = $methodName . ucfirst(strtolower($formInfo['stateAbbr']));
        $methodNameStateSpecificRekey = $methodNameStateSpecific . 'Rekey';

        if ($rekeyPreferred && method_exists($this->serviceDataTransformer, $methodNameStateSpecificRekey)) {
            $methodToCall = $methodNameStateSpecificRekey;
        } elseif (method_exists($this->serviceDataTransformer, $methodNameStateSpecific)) {
            $methodToCall = $methodNameStateSpecific;
        } elseif (method_exists($this->serviceDataTransformer, $methodName)) {
            $methodToCall = $methodName;
        }

        if ($methodToCall) {
            return $this->serviceDataTransformer->$methodToCall();
        }
    }

    /**
     * Create or update agency forms
     *
     * @param numeric|int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false) Create incident forms when true; else create crash form.
     * @param numeric|int $stateId 
     * @param [bool] $activateForms (false) if true will activate forms after creating new form records.
     * @return bool
     */
    public function createOrUpdateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId, $activateForms = false)
    {
        try {
            if (empty($mbsAgencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbsAgencyId is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $tf = $this->adapterForm->createOrUpdateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId, $activateForms);
            return $tf;
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] '
                    . 'while creating or updating incident forms';
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }
    }

    /**
     * Activate agency forms
     *
     * @param numeric|int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false) deactivate incident forms when true; else create crash form
     * @param numeric|int $stateId
     * @return bool
     */
    public function activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId)
    {
        try {
            if (empty($mbsAgencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbsAgencyId is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $tf = $this->adapterForm->activateFormsForAgency($mbsAgencyId, $isIncidentForms, $stateId);
            return $tf;
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }
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
            if (empty($mbsAgencyId)) {
                $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
                $errMsg = 'Origin: ' . $origin . '; mbsAgencyId is empty';
                throw new InvalidArgumentException($errMsg);
            }
            $tf = $this->adapterForm->deactivateFormsForAgency($mbsAgencyId, $isIncidentForms);
            return $tf;
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return false;
        }
    }

    /**
     * Retrieve all forms for mbs agency that are active or inactive excluding forms named '%TEST%'
     * @param int $mbsAgencyId MBS Agency ID
     * @param bool $isIncidentForms (true/false)
     * @param int $stateId
     * @param [bool|mixed] $excludeTestForms (false) if true, will exclude forms with name_external containing 'test'
     */
    public function findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId,
        $excludeTestForms = false)
    {
        try {
            return $this->adapterForm->findUniversalFormsForMbsAgencyIdToActivate($mbsAgencyId, $isIncidentForms, $stateId,
                $excludeTestForms
            );
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
     * @param int $stateId
     * @param bool $isIncidentForms (true/false)
     * @param [bool|mixed] $excludeTestForms (false) if true, will exclude forms with name_external containing 'test'
     * @param [bool|mixed] $isActive (true) 
     * @return array
     */
    public function findBaseUniversalFormByFormType($stateId, $isIncidentForms, $excludeTestForms = false, $isActive = true)
    {
        try {
            return $this->adapterForm->findBaseUniversalFormByFormType($stateId, $isIncidentForms, $excludeTestForms, $isActive);
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
		return $this->adapterForm->getAssocLists($formId);
	}
    
    public function getUnAssocLists($formId)
	{
		return $this->adapterForm->getUnAssocLists($formId);
	}
    
    public function getFormNamesRelatedByGroup($formId)
    {
        $rows = $this->adapterForm->getFormNamesRelatedByGroup($formId);

        $results = [];
        foreach ($rows as $row) {
            $results[] = $row['formName'] . " - " . $row['formState'] . " - " .
                    ($row['formAgency'] ? $row['formAgency'] . " - " : "") . $row['formType'];
        }

        return $results;
    }
    
    public function getFormNamesRelatedByList($formId, $listId)
	{
		return $this->adapterForm->getFormNamesRelatedByList($formId, $listId);
	}
    
    
    public function updateGroup($formId, $groupId)
	{
		return $this->adapterForm->updateGroup($formId, $groupId);
	}
    
}

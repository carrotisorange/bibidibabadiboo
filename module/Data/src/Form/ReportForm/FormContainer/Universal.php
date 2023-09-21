<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Data\Form\ReportForm\FormContainer;

use Data\Form\ReportForm\FormContainer;
use Data\Form\ReportForm\FormModifier;
use Data\Form\ReportForm\FieldContainer;
use Data\Form\ReportForm\Form;
use Data\Form\ReportForm\FormContext;
use Data\Form\ReportForm\ModeHandler\Render;
use Base\Service\FormFieldAttributeService;
use Base\Service\EntryStageService;

class Universal extends FormContainer
{
    protected $prePopValuesArray = [];
    
    public function __construct(
        $formId,
        $reportId,
        $reportEntryId,
        $entryStage,
        $isObsolete,
        $modelForm,
        $session,
        $serviceReport,
        $serviceEntryStage,
        $serviceFormFieldAttribute,
        $serviceAutoExtraction,
        $formModifier,
        $fieldContainer,
        $config,
        $serviceReportEntry,
        $dynamicVerification)
    {
        $formInfo = $modelForm->getFormInfo($formId);
        // Template file path. i.e: data/forms/universal-sectional/TX
        $formInfo['formTemplate'] = $formInfo['formTemplate'] . '/' . $formInfo['stateAbbr'];
        
        $this->session = $session;
        $this->serviceFormFieldAttribute = $serviceFormFieldAttribute;
        $this->serviceAutoExtraction = $serviceAutoExtraction;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->dynamicVerification = $dynamicVerification;
        
        parent::__construct(
            $reportId,
            $entryStage,
            $isObsolete,
            $formInfo['formTemplate'],
            $formInfo['nameExternal']
        );

        $this->prePopValuesArray = [
            'Incident[Loss_State_Abbr]' => $formInfo['stateAbbr'],
            'Incident[Report_Type_Id]' => $formInfo['formType'],
        ];

        // check whether autoextraction enabled for the state corresponding to the report
        $autoExtractionEnabledForState = $serviceReport->isAutoExtractionEnabledForState($reportId);
        
        if ($entryStage == EntryStageService::STAGE_ALL) {
            if ($config['autoExtractionEnabled'] == 1 && $autoExtractionEnabledForState == 1) {
                $getAutoExtractedData = $serviceAutoExtraction->getExtractedData($reportId);
                if (!empty($getAutoExtractedData['entryData'])) {
                    $getAutoExtractedData['entryData'] = $serviceReportEntry->decompressData($getAutoExtractedData['entryData']);
                    $this->prePopulatedAutoExtractedValues($formModifier, $getAutoExtractedData, $reportId, $entryStage, $config);
                }
            }
        } else if ($entryStage == EntryStageService::STAGE_DYNAMIC_VERIFICATION) {
            // check if the pass 1 data is keyed by using autoextracted value
            $checkIsAutoKeyed = $serviceReport->isAutoKeyed($reportId);
            $reportEntryData = $serviceReportEntry->fetchLastPassByReportId($reportId);
            
            if($checkIsAutoKeyed == 1 && !empty($reportEntryData)) {
                $this->prePopulatedAutoExtractedValues($formModifier, $reportEntryData, $reportId, $entryStage, $config);
            }
        }
        
        $formContext = new FormContext(
            new Form(
                $fieldContainer,
                new Render($fieldContainer, $formModifier)
            ),
            $fieldContainer,
            $formInfo['formTemplate']
        );
        
        $entryStageHandler = $serviceEntryStage->loadHandler($entryStage, $formModifier);

        $this->attachFormAttributes($formModifier, $formInfo['formFieldAttributeGroupId']);

        $this->addFormPrePopulatedValues($formModifier, $this->prePopValuesArray);
        
        $this->attachPreviousSaveValues($formModifier, $reportId);
        
        if ($isObsolete) {
            $formModifier->addGlobalFieldModifier('isAvailable', false);
        }

        $entryStageHandler->process($reportId, $reportEntryId);

        $pageData = [];
        if (!empty($this->session->reportData[$reportId])) {
            $pageData = $formContext->addPages($this->session->reportData[$reportId]['_pages']);
        } else {
            if ($formModifier->getPages()) {
                $pageData = $formContext->addPages($formModifier->getPages());
            } else {
                $pageData = $formContext->addBasePages();
            }
        }
        
        // Save the field container so we can take actions on it later (e.g. add page)
        $this->session->formContext = [];
        $this->session->formContext[$reportId] = $formContext;
        
        $this->setPageData($pageData);
        $this->setTabOrder($serviceFormFieldAttribute->getTabOrder($formInfo['formFieldAttributeGroupId']));
    }

    protected function getPageCount()
    {
        return count($this->pageData['pageContents']);
    }
    
    protected function attachFormAttributes(FormModifier $formModifier, $formFieldAttributeGroupId)
    {
        $fieldAttributes = $this->serviceFormFieldAttribute->fetchByGroupId($formFieldAttributeGroupId);

        foreach ($fieldAttributes as $row) {

            if (empty($row[FormFieldAttributeService::ATTRIBUTE_AVAILABLE])) {
                $formModifier->setFieldAttribute($row['fieldName'], FormFieldAttributeService::ATTRIBUTE_AVAILABLE, false);
            }
            if (!empty($row[FormFieldAttributeService::ATTRIBUTE_SKIPPED])) {
                $formModifier->setFieldAttribute($row['fieldName'], FormFieldAttributeService::ATTRIBUTE_SKIPPED, true);
            }
            if (!empty($row[FormFieldAttributeService::ATTRIBUTE_REQUIRED])) {
                $formModifier->setFieldAttribute($row['fieldName'], FormFieldAttributeService::ATTRIBUTE_REQUIRED, true);
            }
        }
    }
    
    protected function addFormPrePopulatedValues(FormModifier $formModifier, $prePopValuesArray)
    {
        // Pre-populate the values of the fields in the form
        // should be called BEFORE _attachPreviousSaveValues
        foreach ($prePopValuesArray as $field => $value) {
            if (strpos($field, 'Citations') !== false) {
                continue;
            }
            $formModifier->setFieldAttribute($field, 'value', $value);
        }
    }
    
    protected function attachPreviousSaveValues(FormModifier $formModifier, $reportId)
    {
        if (empty($this->session->reportData[$reportId])) {
            return;
        }

        // transform $reportData into a value array of $modifiers;
        foreach ($this->session->reportData[$reportId] as $field => $value) {
            $formModifier->setFieldAttribute($field, 'value', $value);
        }
    }
    
    protected function prePopulatedAutoExtractedValues(FormModifier $formModifier, $extractedData, $reportId, $entryStage, $config) {
        $reportData = $extractedData['entryData']['Report'];
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $this->dynamicVerification->codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);
        
        // flatten autoextraction data
        $this->prePopValuesArray = $this->dynamicVerification->flattenDataDynamicVerification($reportData);
        
        //increase memory limit if autoextracted report has more than 150 pages
        if (count($reportData['_pages']) > $config['reportPagesForIncreasedMemory']) {
          ini_set( 'memory_limit', $config['memoryLimitForBigReports'] );
        }  
        // set dynamic pages
        $formModifier->setPages($reportData['_pages']);
        
        $formModifier->addRawScript(
            'eCrash.data.previousValues = '
            . json_encode($this->prePopValuesArray) . ';'
        );

        if ($entryStage == EntryStageService::STAGE_ALL) {
            $formModifier->addRawScript(
                'eCrash.setCitationDetails();'
            );
        }
    }
}

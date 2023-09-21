<?php
namespace Base\Service\EntryStage\Handler;

use Exception;

use Base\Service\EntryStage\HandlerInterface;
use Base\Service\ReportEntryService;
use Data\Form\ReportForm\FormModifier;

class DynamicVerification implements HandlerInterface
{
    /**
     * @var Data\Form\ReportForm\FormModifier
     */
    protected $formModifier;

    public $codeDescriptionPairFields = [];

    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;

    public function __construct(
        FormModifier $formModifier,
        ReportEntryService $serviceReportEntry)
    {
        $this->formModifier = $formModifier;
        $this->serviceReportEntry = $serviceReportEntry;
    }

    public function process($reportId)
    {
        $reportEntryData = $this->serviceReportEntry->fetchLastPassByReportId($reportId);
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $this->codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);
        
        if (empty($reportEntryData)) {
            throw new Exception('Can\'t do dynamic-verification if there is no previous report entry data.');
        }

        /** @todo Determine priority fields */
        /** @todo If not priority field, set value and isSkipped to modifiers */
        /** @todo Else, add data to rawScript. */
        // For the Universal form everything is considered a priority field.
        // Once we get state or agency forms using this pass we'll need that priority field list.
        $reportEntryDataValue = $this->serviceReportEntry->sortGroupElements($reportEntryData['entryData']['Report']);
        
        $previousValues = $this->flattenDataDynamicVerification($reportEntryDataValue);
        $this->formModifier->setPages(array_filter($reportEntryDataValue['_pages']));
        $this->formModifier->addGlobalFunction('dynamicVerification');
        $this->formModifier->addRawScript(
            'eCrash.data.dynamicVerification.previousValues = ' . json_encode($previousValues)
            . '; eCrash.initDynamicVerification();'
        );
    }
    
    public function flattenDataDynamicVerification($inputData, $path = null)
    {
        $data = [];
        $originalDataFields = [
            'VIN',
            'Model_Year',
            'Model',
            'Make'
        ];
        
        if (is_array($inputData) || is_object($inputData)) {
            foreach ($inputData as $key => $value) {
                if (!is_array($value) && !is_object($value)) {
                    if ($path) {
                        $data[$path . '[' . $key . ']'] = $value;
                    } else {
                        $data[$path . $key] = $value;
                    }
                    
                    // pre-populating fields that are storing originally keyed values
                    foreach ($originalDataFields as $field) {
                        if (strpos($key, $field . '_Original') === 0) {
                            $fieldName = $path . '[' . $key . ']';
                            $this->formModifier->setFieldAttribute($fieldName, 'value', $value);
                        }
                    }
                } else {
                    if (is_numeric($key)) {
                        $key = '[' . $key . ']';
                    }
                    
                    $codePairKey = $this->serviceReportEntry->convertInputFieldName($key);
                    if (!empty($this->codeDescriptionPairFields)
                        && in_array($codePairKey, $this->codeDescriptionPairFields)) {
                        
                        /**
                         * The array_walk() used to form the codes array using code and description value in the below
                         * format as per entryData. i.e: key => value
                         * Weather_Condition: [{
                         *     "Code": "1",
                         *     "Description": "Clear"
                         * },
                         * {
                         *     "Code": "2",
                         *     "Description": "Cloudy"
                         * }]
                         * 
                         * Violation_Code: [{
                         *     "Code": "",
                         *     "Description": "OTHER"
                         * },
                         * {
                         *     "Code": "",
                         *     "Description": "UNKNOWN"
                         * }]
                         * Above array will be converted into codes array, it will be send to UI
                         * Weather_Condition => ["1", "2"]
                         * Violation_Code => ["OTHER", "UNKNOWN"]
                         */
                        if (isset($value['Code'])) {
                            $formCodes = [$value['Code']];
                            $formCodeDescriptions = [$value['Description']];
                        } else {
                            $formCodes = array_filter(array_column($value, 'Code'), 'strlen');
                            $formCodeDescriptions = array_filter(array_column($value, 'Description'), 'strlen');
                        }
                        
                        $formCodes = !empty($formCodes) ? $formCodes : $formCodeDescriptions;
                        
                        $data[$path . '[' . $key . ']'] = implode(';', $formCodes);
                    } else {
                        $data = array_merge($data, $this->flattenDataDynamicVerification($value, $path . $key));
                    }
                }
            }
        }
        
        return $data;
    }
}

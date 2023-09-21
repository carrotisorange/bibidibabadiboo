<?php
namespace Base\Service\EntryStage\Handler;

use Base\Service\EntryStage\HandlerInterface;
use Base\Service\ReportEntryService;
use Data\Form\ReportForm\FormModifier;
use Base\Service\EntryStage\Handler\DynamicVerification;

class Edit implements HandlerInterface
{
    /**
     * @var Data\Form\ReportForm\FormModifier
     */
    protected $formModifier;

    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;

    public function __construct(
        FormModifier $formModifier,
        ReportEntryService $serviceReportEntry,
        DynamicVerification $dynamicVerification)
    {
        $this->formModifier = $formModifier;
        $this->serviceReportEntry = $serviceReportEntry;
        $this->dynamicVerification = $dynamicVerification;
    }

    public function process($reportId)
    {
        $reportEntryData = $this->serviceReportEntry->fetchLastPassByReportId($reportId);
        $codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $this->dynamicVerification->codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);
        
        $reportData = null;
        if (!empty($reportEntryData)) {
            if (!empty($reportEntryData['entryData'])) {
                $reportData = $reportEntryData['entryData']['Report'];
                $reportEntryData = $this->dynamicVerification->flattenDataDynamicVerification($reportData);
            
                foreach ($reportEntryData as $key => $value) {
                    $this->formModifier->setFieldAttribute($key, 'value', $value);
                }

                $this->formModifier->setPages(array_filter($reportData['_pages']));
            }
        }
        
        $this->formModifier->addRawScript(
            'eCrash.data.previousValues = ' . json_encode($reportEntryData) . ';'
            . 'eCrash.setCitationDetails();'
        );
    }
}

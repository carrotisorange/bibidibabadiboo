<?php
namespace Base\Service\EntryStage\Handler;

use Base\Service\EntryStage\HandlerInterface;
use Data\Form\ReportForm\FormModifier;
use Base\Service\ReportEntryService;
use Base\Service\EntryStage\Handler\DynamicVerification;

class None implements HandlerInterface
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

    public function process($reportId, $reportEntryId=null)
    {
        if ($reportEntryId == null) {
            $reportEntryData = $this->serviceReportEntry->fetchLastPassByReportId($reportId);
        } else {
            $reportEntryData = $this->serviceReportEntry->fetchOnePassByReportId($reportId, $reportEntryId);
        }
		
		$codeDescriptionPairFields = $this->serviceReportEntry->getFormFieldsCodePair($reportId);
        $this->dynamicVerification->codeDescriptionPairFields = array_map('strtolower', $codeDescriptionPairFields);

        $reportData = $reportEntryData['entryData']['Report'];

        if (!empty($reportEntryData)) {
            $reportEntryData = $this->dynamicVerification->flattenDataDynamicVerification($reportData);

            foreach ($reportEntryData as $key => $value) {
                $this->formModifier->setFieldAttribute($key, 'value', $value);
            }
            $this->formModifier->setPages($reportData['_pages']);
        }

        $this->formModifier->addRawScript(
            'eCrash.data.previousValues = ' . json_encode($reportEntryData) . ';'
            . 'eCrash.setCitationDetails();'
        );
    }

}

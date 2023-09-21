<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */

namespace Base\Service;

use Exception;

use Base\Adapter\Db\EntryStageAdapter;
use Base\Service\EntryStage\Handler\All;
use Base\Service\EntryStage\Handler\DynamicVerification;
use Base\Service\EntryStage\Handler\Edit;
use Base\Service\EntryStage\Handler\None;
use Base\Service\ReportEntryService;
use Data\Form\ReportForm\FormModifier;

class EntryStageService extends BaseService
{
    /**
     * Verification is done as user types the second pass.
     */
    const STAGE_DYNAMIC_VERIFICATION = 'dynamic-verification';
    
    /**
     * Verification is done by differences of previous passes.
     */
    const STAGE_DIFFERENCE_VERIFICATION = 'difference-verification';
    
    /**
     * Stage where keying is done on all fields.
     */
    const STAGE_ALL = 'all';
    
    /**
     * Stage where keying is done on all fields with values from the last pass.
     */
    const STAGE_EDIT = 'edit';
    
    /**
     * Stage where no fields are editable, but previous values may be viewed if available.
     */
    const STAGE_NONE = 'none';
    
    /**
     * This stage is for when things are saved from the invalid vin queue
     */
    const STAGE_INVALID_VIN = 'invalid-vin-queue';
    
    /**
     * This stage is for rekey 3rd pass for universal forms only
     */
    const STAGE_REKEY = 'rekey';
    
    /**
     * This stage is for electronic additional keying only
     */
    const STAGE_ELECTRONIC_REKEY = 'electronic-keying';

    /**
     * Stage for report that was marked as bad image
     */
    const STAGE_BAD = 'bad-image';

    /**
     * Stage for report that was marked as bad image
     */
    const AUTO_EXTRACT = 'auto-extract';

    /**
     * Stage where keying is done on all fields.
     */
    const STAGE_ALL_INDEX = 1;
    /**
     * Verification is done as user types the second pass.
     */
    const STAGE_DYNAMIC_VERIFICATION_INDEX = 3;
    
    /**
     * @var Base\Adapter\Db\EntryStageAdapter
     */
    protected $adapterEntryStage;

    /**
     * @var Base\Service\ReportEntryService
     */
    protected $serviceReportEntry;
    
    public function __construct(
        EntryStageAdapter $adapterEntryStage,
        ReportEntryService $serviceReportEntry)
    {
        $this->adapterEntryStage = $adapterEntryStage;
        $this->serviceReportEntry = $serviceReportEntry;
    }
    
    /**
     * @return array array(entryStageId => internalName, ...)
     */
    public function getInternalNamePairs()
    {
        return $this->adapterEntryStage->getInternalNamePairs();
    }
    
    /**
     * Get allowed entry stages
     *
     * @param boolean $isPermissionableOnly
     * @return array    Allowed entry stages array(entry_stage_id => name_external, ...)
     */
    public function getExternalNamePairs($isPermissionableOnly = false)
    {
        return $this->adapterEntryStage->getExternalNamePairs($isPermissionableOnly);
    }

    /**
     * Loads handler functionality based on entryStage specified.
     *
     * @param string $entryStage
     * @param FormModifier $formModifier
     * @return Base\Service\EntryStage\HandlerInterface
     */
    public function loadHandler(
        $entryStage,
        FormModifier $formModifier)
    {
        switch ($entryStage) {
            case EntryStageService::STAGE_ALL:
                return new All(
                    $formModifier
                );
                break;
            
            // @TODO: Will be handled in future
            /*case EntryStageService::STAGE_DIFFERENCE_VERIFICATION:
                return new DifferenceVerification(
                    $formModifier,
                    $this->serviceReportEntry
                );
                break;*/
            
            case EntryStageService::STAGE_DYNAMIC_VERIFICATION:
                return new DynamicVerification(
                    $formModifier,
                    $this->serviceReportEntry
                );
                break;
            
            case EntryStageService::STAGE_EDIT:
            case EntryStageService::STAGE_BAD:
                return new Edit(
                    $formModifier,
                    $this->serviceReportEntry,
                    new DynamicVerification(
                        $formModifier,
                        $this->serviceReportEntry
                    )
                );
                break;
            
            case EntryStageService::STAGE_NONE:
                return new None(
                    $formModifier,
                    $this->serviceReportEntry,
                    new DynamicVerification(
                        $formModifier,
                        $this->serviceReportEntry
                    )
                );
            
            default:
                throw new Exception('Report is in unknown EntryStage');
        }
    }
    
    public function getIdByInternalName($internalName)
    {
        return $this->adapterEntryStage->getIdByInternalName($internalName);
    }
}

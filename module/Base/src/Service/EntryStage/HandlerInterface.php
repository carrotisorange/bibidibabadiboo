<?php
namespace Base\Service\EntryStage;

interface HandlerInterface
{
//  public function __construct(ReportForm_FormModifier $formModifier);

    public function process($reportId);
}

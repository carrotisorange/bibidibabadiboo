<?php
namespace Base\Service\EntryStage\Handler;

use Base\Service\EntryStage\HandlerInterface;
use Data\Form\ReportForm\FormModifier;

class All implements HandlerInterface
{
    /**
     * @var Data\Form\ReportForm\FormModifier
     */
    protected $formModifier;

    public function __construct(FormModifier $formModifier)
    {

    }

    public function process($reportId)
    {
        // Do nothing. Everything is as it should be.
    }
}

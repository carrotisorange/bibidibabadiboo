<?php

namespace Data\Form\ReportForm\ModeHandler;

use Data\Form\ReportForm\ModeHandlerInterface;
use Data\Form\ReportForm\FieldContainer;
use Data\Form\ReportForm\FieldInterface;
/**
 * Does absolutely nothing. No, really.
 *
 * @package Form
 * @subpackage ModeHandler
 */
class ModeHandler_Null implements ModeHandlerInterface
{
	public function __construct(FieldContainer $fieldContainer) { }
	public function addField(FieldInterface $field) { }
	public function preFormProcess() { }
	public function postFormProcess() { }
	public function getAdditionalScript() { }
}

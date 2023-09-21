<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AccidentCondition extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Accident_Condition');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Accident_Condition")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Accident_Condition'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

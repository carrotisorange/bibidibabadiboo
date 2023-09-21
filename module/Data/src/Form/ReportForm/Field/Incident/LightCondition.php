<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class LightCondition extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Light_Condition');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Light_Condition")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Light_Condition'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

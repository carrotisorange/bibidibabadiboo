<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class UnusualRoadCondition extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Unusual_Road_Condition');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Unusual_Road_Condition")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Unusual_Road_Condition'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

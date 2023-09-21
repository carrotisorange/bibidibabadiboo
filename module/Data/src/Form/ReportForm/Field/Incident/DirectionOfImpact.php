<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DirectionOfImpact extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Direction_Of_Impact');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Direction_Of_Impact")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Direction_Of_Impact'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

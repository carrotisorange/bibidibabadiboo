<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DispatchTime12h extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Dispatch_Time');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isTime12Hour()'),
			'validateForceImmediate' => array('byLengthMax(8)'),
			'validateSoft' => array(),
			'valueFormat' => array('asTime12Hr()'),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(8)'),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class OfficerArrivalTime extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Officer_Arrival_Time');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isTime24Hour()'),
			'validateForceImmediate' => array('byLengthMax(5)'),
			'validateSoft' => array(),
			'valueFormat' => array('asTime()'),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(5)'),
		);
	}
}

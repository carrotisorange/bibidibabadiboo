<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CrashDate extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Crash_Date');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isDateMDY()', 'isFutureDate()'),
			'validateForceImmediate' => array('isDatePartial()', 'byLengthMax(10)'),
			'validateSoft' => array(),
			'valueFormat' => array('asDateMDY()'),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(10)'),
			'customFunction' => array(),
		);
	}
}

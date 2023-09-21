<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CrashCity extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Crash_City');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialChars()'),
			'validateForceImmediate' => array('byLengthMax(35)', 'checkSpecialChars()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('dynamicFields'), 
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

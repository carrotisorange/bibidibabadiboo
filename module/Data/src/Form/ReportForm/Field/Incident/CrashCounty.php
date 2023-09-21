<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CrashCounty extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Crash_County');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(30)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Counties'),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(30)'),
		);
	}
}

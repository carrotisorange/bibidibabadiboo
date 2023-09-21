<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class StateReportNumber extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'State_Report_Number');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialChars()'),
			'validateForceImmediate' => array('byLengthMax(40)', 'checkSpecialChars()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

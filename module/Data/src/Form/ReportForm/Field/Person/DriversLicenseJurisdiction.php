<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DriversLicenseJurisdiction extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Drivers_License_Jurisdiction');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("States")','byLengthMax(2)', 'isAlpha()'),
			'validateForceImmediate' => array('byLengthMax(2)', 'isAlpha()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('States'),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

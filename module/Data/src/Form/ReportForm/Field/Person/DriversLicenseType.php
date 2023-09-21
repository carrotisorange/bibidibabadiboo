<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DriversLicenseType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Drivers_License_Type');
	}

	public function getInputType()
	{
		return 'checkboxes';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Missouri Driver License")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Missouri Driver License'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

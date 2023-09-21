<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class RegistrationState extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Registration_State');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('byLengthMax(2)', 'isAlpha()'),
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

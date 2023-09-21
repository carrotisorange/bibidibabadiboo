<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AlcoholTestResult extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Alcohol_Test_Result');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isNumericandDot()'),
			'validateForceImmediate' => array('isNumericandDot()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

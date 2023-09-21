<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class WeatherConditionOtherDescription2 extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Weather_Other_Description2');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(''),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

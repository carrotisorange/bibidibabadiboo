<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class WeatherCondition extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Weather_Condition');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Weather_Condition")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Weather_Condition'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

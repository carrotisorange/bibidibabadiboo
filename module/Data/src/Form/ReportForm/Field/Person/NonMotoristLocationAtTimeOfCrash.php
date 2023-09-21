<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class NonMotoristLocationAtTimeOfCrash extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Non_Motorist_Location_at_Time_of_Crash');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Pedestrian Involvement")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Pedestrian Involvement'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class PassengerActionsAtTimeOfCrash extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Passenger_Actions_At_Time_Of_Crash');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Passenger_Actions_At_Time_Of_Crash")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Passenger_Actions_At_Time_Of_Crash'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

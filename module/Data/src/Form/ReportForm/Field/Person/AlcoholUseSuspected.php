<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AlcoholUseSuspected extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Alcohol_Use_Suspected');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Alcohol_Use_Suspected")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array("Alcohol_Use_Suspected"),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

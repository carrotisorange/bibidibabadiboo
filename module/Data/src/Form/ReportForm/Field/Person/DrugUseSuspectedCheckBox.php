<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DrugUseSuspectedCheckBox extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Drug_Use_Suspected');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

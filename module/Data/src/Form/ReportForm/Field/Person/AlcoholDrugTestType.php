<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AlcoholDrugTestType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Alcohol_Drug_Test_Type');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Alcohol_Drug_Test_Type")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Alcohol_Drug_Test_Type'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

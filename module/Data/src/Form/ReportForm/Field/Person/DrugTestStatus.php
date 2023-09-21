<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DrugTestStatus extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Drug_Test_Status');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
		    'validateForce' => array('inValueMultiSelectList("Drug_Test_Status")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array("Drug_Test_Status"),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

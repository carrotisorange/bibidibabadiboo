<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class PriorNonmotoristAction extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Prior_Nonmotorist_Action');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Prior_Nonmotorist_Action")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Prior_Nonmotorist_Action'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class DlNumberClass extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'DL_Number_Class');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Missouri Driver License")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Missouri Driver License'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

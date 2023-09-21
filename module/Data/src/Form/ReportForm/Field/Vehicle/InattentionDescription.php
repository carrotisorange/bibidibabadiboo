<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class InattentionDescription extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Inattention_Description');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(30)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Inattention'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

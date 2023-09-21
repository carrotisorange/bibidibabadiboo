<?php
namespace Data\Form\ReportForm\Field\Citations;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class PartyId extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Citations[#]', 'Party_Id');
	}

	public function getInputType()
	{
		return 'hidden';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(4)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

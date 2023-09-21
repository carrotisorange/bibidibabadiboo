<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TransportedToList extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Transported_To');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Transported_To")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Transported_To'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

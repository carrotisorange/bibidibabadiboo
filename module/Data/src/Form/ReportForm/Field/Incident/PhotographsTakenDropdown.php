<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class PhotographsTakenDropdown extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Photographs_Taken');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Photographs_Taken")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Photographs_Taken'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}

}

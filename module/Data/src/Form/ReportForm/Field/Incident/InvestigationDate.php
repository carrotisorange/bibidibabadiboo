<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class InvestigationDate extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Investigation_Date');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('isDateMDY()'),
			'validateForceImmediate' => array('byLengthMax(255)', 'isDatePartial()'),
			'validateSoft' => array(),
			'valueFormat' => array('asDateMDY()'),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(255)'),
		);
	}
}

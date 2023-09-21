<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class IntersectionType extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Intersection_Type');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Intersection_Type")'),
			'validateForceImmediate' => array('byLengthMax(255)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array("Intersection_Type"),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(255)'),
		);
	}
}

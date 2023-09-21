<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class IntersectionRelated extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Intersection_Related');
	}

	public function getInputType()
	{
		return 'radio';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(255)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(255)'),
		);
	}
}

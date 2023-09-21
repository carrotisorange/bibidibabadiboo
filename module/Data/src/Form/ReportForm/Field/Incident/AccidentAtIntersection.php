<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AccidentAtIntersection extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Accident_At_Intersection');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
            'validateForce' => array(),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

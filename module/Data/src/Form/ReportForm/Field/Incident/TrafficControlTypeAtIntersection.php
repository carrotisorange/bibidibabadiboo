<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class TrafficControlTypeAtIntersection extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Traffic_Control_Type_At_Intersection');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Traffic_Control_Type_At_Intersection")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Traffic_Control_Type_At_Intersection'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

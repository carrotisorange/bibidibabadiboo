<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class RoadSurface extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Road_Surface');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Road Surface")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Road Surface'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

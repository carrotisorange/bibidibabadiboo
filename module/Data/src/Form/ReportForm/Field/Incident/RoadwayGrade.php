<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class RoadwayGrade extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Roadway_Grade');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Road Character – Profile")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Road Character – Profile'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

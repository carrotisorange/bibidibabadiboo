<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class RoadwayCurvature extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Roadway_Curvature');
	}

	public function getInputType()
	{
		return 'checkbox';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Road Character – Alignment")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Road Character – Alignment'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

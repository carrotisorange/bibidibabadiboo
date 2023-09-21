<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class IncidentLocationIndicator extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Incident_Location_Indicator');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Accident Type")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Accident Type'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class IncidentHitAndRun extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Incident_Hit_and_Run');
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

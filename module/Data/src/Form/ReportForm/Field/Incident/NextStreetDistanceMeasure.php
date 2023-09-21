<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class NextStreetDistanceMeasure extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Next_Street_Distance_Measure');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Next_Street_Distance_Measure")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Next_Street_Distance_Measure'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

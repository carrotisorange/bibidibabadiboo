<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MilepostNextStreetDistanceMeasure extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Milepost_Next_Street_Distance_Measure');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Milepost_Next_Street_Distance_Measure")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Milepost_Next_Street_Distance_Measure'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class GpsOther extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Gps_Other');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('checkSpecialCharsLongLat()'),
			'validateForceImmediate' => array('byLengthMax(35)','checkSpecialCharsLongLat()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

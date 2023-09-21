<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class StreetOrientation extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Street_Orientation');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(10)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Direction of Travel'),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(10)'),
		);
	}
}

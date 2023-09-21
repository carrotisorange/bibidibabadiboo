<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Model extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'Model');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(55)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array('saveOriginalData()'),
		);
	}
}

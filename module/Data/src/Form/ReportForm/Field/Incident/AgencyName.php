<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AgencyName extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Agency_Name');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(50)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Agencies'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

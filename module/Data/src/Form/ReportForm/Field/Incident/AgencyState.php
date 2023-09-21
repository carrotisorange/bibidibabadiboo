<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class AgencyState extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Agency_State');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('States'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

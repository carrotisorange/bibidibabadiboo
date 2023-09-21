<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class LossCrossStreetSpeedLimitDropDown extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Loss_Cross_Street_Speed_Limit');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Loss_Cross_Street_Speed_Limit")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Loss_Cross_Street_Speed_Limit'),
			'autoFill' => array(),
			'autoTab' => array()
		);
	}
}

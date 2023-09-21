<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class NextStreetDirection extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Next_Street_Direction');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Next_Street_Direction")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Next_Street_Direction'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

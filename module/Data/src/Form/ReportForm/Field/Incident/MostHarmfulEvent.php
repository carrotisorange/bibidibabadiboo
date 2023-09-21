<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MostHarmfulEvent extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Most_Harmful_Event');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Most_Harmful_Event")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Most_Harmful_Event'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class MannerCrashImpact extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Manner_Crash_Impact');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueMultiSelectList("Manner_Crash_Impact")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Manner_Crash_Impact'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

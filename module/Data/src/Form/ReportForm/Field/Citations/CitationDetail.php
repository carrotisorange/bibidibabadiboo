<?php
namespace Data\Form\ReportForm\Field\Citations;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CitationDetail extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Citations[#]', 'Citation_Detail');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(200)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => array(),
		);
	}
}

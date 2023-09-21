<?php
namespace Data\Form\ReportForm\Field\Citations;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CitationDetailDropdown extends Field implements FieldInterface
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
			'validateForce' => array('inValueList("Citation_Detail")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Citation_Detail'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

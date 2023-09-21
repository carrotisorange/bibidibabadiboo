<?php
namespace Data\Form\ReportForm\Field\Citations;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class CitationIssuedDropdown extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Citations[#]', 'Citation_Issued');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Citation_Issued")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Citation_Issued'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

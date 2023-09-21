<?php
namespace Data\Form\ReportForm\Field\Person;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class ProofOfInsurance extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('People[#]', 'Proof_of_Insurance');
	}

	public function getInputType()
	{
		return 'radio';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array(),
			'validateForceImmediate' => array('byLengthMax(7)'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array('byLengthMin(7)'),
		);
	}
}

<?php
namespace Data\Form\ReportForm\Field\Incident;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class RoadMaintainedBy extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Incident', 'Road_Maintained_By');
	}

	public function getInputType()
	{
		return 'radio';
	}

	public function getFunctionalityHooks()
	{
		return array(
			'validateForce' => array('inValueList("Road Maintained By")'),
			'validateForceImmediate' => array(),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array('Road Maintained By'),
			'autoFill' => array(),
			'autoTab' => array(),
		);
	}
}

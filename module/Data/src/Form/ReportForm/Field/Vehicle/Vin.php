<?php
namespace Data\Form\ReportForm\Field\Vehicle;

use Data\Form\ReportForm\Field;
use Data\Form\ReportForm\FieldInterface;

class Vin extends Field implements FieldInterface
{
	public function getDbFieldName()
	{
		return array('Vehicles[#]', 'VIN');
	}

	public function getInputType()
	{
		return 'text';
	}

	public function getFunctionalityHooks()
	{
        // @TODO: Should be removed when pushing code to uat or live
        if (APPLICATION_ENV == 'local') {
            $validation = array('saveOriginalData()');
        } else {
            $validation = array('saveOriginalData()', 'vinValidation()');
        }
        
		return array(
			'validateForce' => array('byLengthMax(25)', 'isAlphaNumeric()'),
			'validateForceImmediate' => array('byLengthMax(25)', 'isAlphaNumeric()'),
			'validateSoft' => array(),
			'valueFormat' => array(),
			'valueList' => array(),
			'autoFill' => array(),
			'autoTab' => array(),
			'customFunction' => $validation,
		);
	}
}

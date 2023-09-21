<?php
/**
 * @copyright (c) 2020 LexisNexis Company. All rights reserved.
 */
namespace Base\Service\Cdi;

use Zend\Log\Logger;
use Zend\Db\Sql\Select;
use Exception;
use InvalidArgumentException;

use Base\Adapter\Db\Extract\EnumerationValueAdapter;
use Base\Adapter\Db\Extract\EnumerationMapAdapter;
use Base\Adapter\Db\Extract\EnumerationFieldAdapter;
use Base\Service\BaseService;

class EnumeratorService extends BaseService
{
	
	const EXTRACT_TO_HPCC_ENUMERATION_DELIMITER = '|';
    
    /*
     * Recommendation: do not change the COMMON_ENUM_VALS_FIELDS const as it is used by:
     * 1. MbsPullAgencies job (sets up new enumerations for new forms added)
     * 2. AnalyzeRepairFormData job (enumeration repair tool)
     */
    const COMMON_ENUM_VALS_FIELDS = 'enumeration_field_id, enumeration_value, enumeration_value_vendor, field_name, additional_info_field_name';
    
    /**
     * @var Array
     */
    private $config;
    
    /**
     * @var Zend\Log\Logger
     */
    protected $logger;
    
    /**
     * @var Base\Adapter\Db\Mbs\EnumerationValueAdapter
     */
    protected $adapterEnumerationValue;
    /**
     * @var Base\Adapter\Db\Mbs\EnumerationMapAdapter
     */
    protected $adapterEnumerationMap;
    /**
     * @var Base\Adapter\Db\Mbs\EnumerationFieldAdapter
     */
    protected $adapterEnumerationField;
    
    public function __construct(
        Array $config,
        Logger $logger,
        EnumerationValueAdapter $adapterEnumerationValue,
        EnumerationMapAdapter $adapterEnumerationMap,
        EnumerationFieldAdapter $adapterEnumerationField)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->adapterEnumerationField = $adapterEnumerationField;
        $this->adapterEnumerationValue = $adapterEnumerationValue;
        $this->adapterEnumerationMap = $adapterEnumerationMap;
    }

    /**
     * Gets enumerations by given form id.
     *
     * @param int $formId
     * @return array
     */
    public function getAllEnumeratedValuePairs($formId)
    {
        $pairs = $this->adapterEnumerationValue->getEnumerationPairs($formId);
        $result = [];
        foreach ($pairs as $pair) {
            $fieldName = $this->trimAndLowerCase($pair['fieldName']);
            $enumerationValueVendor = $this->trimAndLowerCase($pair['enumerationValueVendor']);
            $enumerationValue = $pair['enumerationValueId'];
            if (isset($pair['additionalInfoFieldName'])) {
                $enumerationValue = [
                    'enumerationValueId' => $pair['enumerationValueId'],
                    'additionalInfoFieldName' => $pair['additionalInfoFieldName']
                ];
            }
            $result[strtolower($pair['relatedTable'])][$fieldName][$enumerationValueVendor] = $enumerationValue;
        }

        return $result;
    }

    //@TODO: would be nice not to pass $extractionTablesStructure here, but get it directly from method in ECrashExtractService,
    // but we have circular dependency here, because ECrashExtractService includes EnumeratorService and we will have to include
    // ECrashExtractService into EnumeratorService in order to access method that we need.
    /**
     * Gets enumeration list and filters out db fields that are enumerations.
     *
     * @param array $fieldGroup - fields to process (incident or vehicle , etc. fields)
     * @param string $fieldGroupName - group name (i.e. incident, vehicle, etc.)
     * @param int $formId - form id
     * @param array $extractionTablesStructure - associative array that stores extraction tables and field names
     * @return array - 1st element is extraction tables fields, 2nd element is enumerations
     */
    public function getEnumerationExtract($fieldGroup, $fieldGroupName, $formId, $extractionTablesStructure)
    {
        $enumerationValuePairs = $this->getAllEnumeratedValuePairs($formId);
        $enumeration = [];
        $fieldGroup = array_combine(
            array_map('strtolower', array_keys($fieldGroup)),
            $fieldGroup);

        $fieldGroupName = strtolower($fieldGroupName);

        if (!empty($enumerationValuePairs[$fieldGroupName])) {

            foreach ($enumerationValuePairs[$fieldGroupName] as $fieldName => $fieldEnumerationValues) {
                $fieldNameLowerCased = strtolower($fieldName);

                if (isset($fieldGroup[$fieldNameLowerCased])) {
                    $fieldSubValues = explode('|', $fieldGroup[$fieldNameLowerCased]);

                    foreach ($fieldSubValues as $fieldSubValue) {

                        $fieldSubValueLowerCased = $this->trimAndLowerCase($fieldSubValue);
                        if ($fieldSubValueLowerCased != '') {
                            $additionalInfoFieldName = null;

                            if (isset($fieldEnumerationValues[$fieldSubValueLowerCased])
                                && is_array($fieldEnumerationValues[$fieldSubValueLowerCased])
                                && isset($fieldEnumerationValues[$fieldSubValueLowerCased]['additionalInfoFieldName'])) {
                                $additionalInfoFieldName = $fieldEnumerationValues[$fieldSubValueLowerCased]['additionalInfoFieldName'];
                            }

                            if (!isset($fieldEnumerationValues[$fieldSubValueLowerCased])
                                && isset($fieldEnumerationValues['free_form'])) {

                                $enumeration[] = $this->prepareEnumRow($fieldEnumerationValues['free_form'], trim($fieldSubValue));
                            } elseif (isset($additionalInfoFieldName)) {

                                $additionalInfoFieldValue = isset($fieldGroup[strtolower($additionalInfoFieldName)]) ?
                                    $fieldGroup[strtolower($additionalInfoFieldName)] : null;
                                $enumeration[] = $this->prepareEnumRow(
                                    $fieldEnumerationValues[$fieldSubValueLowerCased]['enumerationValueId'],
                                    null,
                                    trim($additionalInfoFieldValue)
                                );
                            } elseif (isset($fieldEnumerationValues[$fieldSubValueLowerCased])) {
                                $enumeration[] = $this->prepareEnumRow($fieldEnumerationValues[$fieldSubValueLowerCased]);
                            }
                        }
                    }
                    unset($fieldGroup[$fieldNameLowerCased]);
                }

                foreach ($fieldEnumerationValues as $enumerationValue) {
                    if (isset($enumerationValue['additionalInfoFieldName'])) {
                        // preventing saving additional info field into tables like incident, vehicle, etc.
                        // It should be saved only in enumeration_map table
                        unset($fieldGroup[strtolower($enumerationValue['additionalInfoFieldName'])]);
                    }
                }
            }
        }
        $this->checkValidFields($extractionTablesStructure[$fieldGroupName], array_keys($fieldGroup));

        return [
            $this->fixFieldsCasing(
                $extractionTablesStructure[$fieldGroupName],
                $fieldGroup),
            $enumeration
        ];
    }

    /**
     * Checks if field names exist in actual db tables.
     *
     * @param array $fieldsCheckAgainst - proper extraction table fields names
     * @param array $fieldsToCheck - set of field names we want to check against proper field names
     * @throws Exception
     */
    protected function checkValidFields($fieldsCheckAgainst, $fieldsToCheck)
    {
        if (is_array($fieldsCheckAgainst) && is_array($fieldsToCheck)) {
            $nonExistentFields = array_diff(array_map('strtolower', $fieldsToCheck), array_map('strtolower', $fieldsCheckAgainst));
            if (!empty($nonExistentFields)) {
                throw new Exception("Attempt to extract fields that don't exist: " . implode(', ', $nonExistentFields));
            }
        }
    }

    /**
     * Gets proper fields casing using actual db tables fields names.
     *
     * @param array $properFields
     * @param array $fieldsToFix
     * @return array
     */
    protected function fixFieldsCasing($properFields, $fieldsToFix)
    {
        if (empty($properFields) || empty($fieldsToFix)) {
            return;
        }
        $lookupFields = array_combine(array_map('strtolower', $properFields), $properFields);
        foreach ($fieldsToFix as $field => $value) {
            if (!empty($lookupFields[strtolower($field)])) {
                if (!isset($fieldsToFix[$lookupFields[strtolower($field)]])) {
                    $fieldsToFix[$lookupFields[strtolower($field)]] = $value;
                    unset($fieldsToFix[$field]);
                }
            }
            else {
                unset($fieldsToFix[$field]);
            }
        }

        return $fieldsToFix;
    }

    /**
     * Prepares row for inserting into enumeration table.
     *
     * @param int $enumerationValueId - enumeration_value_id column
     * @param int|string $free_form_value - free_form_value column
     * @param int|string $additionalInfo additional_info column
     * @return array
     */
    public function prepareEnumRow($enumerationValueId, $freeFormValue = null, $additionalInfo = null)
    {
        return [
            'enumeration_value_id' => $enumerationValueId,
            'free_form_value' => $freeFormValue,
            'additional_info' => $additionalInfo
        ];
    }

    /**
     * Takes spaces out of the string and also convert every letter to lower case.
     *
     * @param string $string
     * @return string
     */
    public function trimAndLowerCase($string)
    {
        return trim(strtolower($string));
    }

    /**
     * Trims and converts for example "weather_condition" to "Weather_Condition".
     *
     * @param string $string
     * @return string
     */
    public function trimAndUppercaseUnderscores($string)
    {
        return implode('_', array_map('ucfirst', explode('_', trim($string))));
    }

    /**
     * Inserts enumerations.
     *
     * @param array $enumArray
     * @param int $incidentId
     * @param int $vehicleId
     * @param int $personId
     * @return null
     */
    public function insertEnumMaps($enumArray, $incidentId, $vehicleId = null, $personId = null)
    {
        if (!is_numeric($incidentId)) {
            return;
        }
        foreach ($enumArray as $row) {
            $insert = array_merge($row, ['incident_id' => $incidentId, 'vehicle_id' => $vehicleId, 'person_id' => $personId]);
            $this->adapterEnumerationMap->insert($insert);
        }
    }

    /**
     * Return aggregate set of enumeration value records for 1 to N form ids
     * @param string $formIdsCsv - CSV string of form ids to aggregate enum values for.
     * @param [string] $fields (*) the fields to retrieve.
     * @param [string] $orderBy ('enumeration_value_vendor') the column to order by from enumeration values table
     * @return array
     */
    public function fetchEnumerationValues( $formIdsCsv, $fields = '*', $orderBy = 'enumeration_value_vendor')
    {
        try
        {
            return $this->adapterEnumerationValue->fetchEnumerationValues($formIdsCsv, $fields, $orderBy);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return null;
        }// @codeCoverageIgnoreEnd
    }
    
    /**
     * Insert a record into the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows inserted (should be 1). 0 on error or exception.
     */
    public function insertEnumerationValue( $fieldAndVals )
    {
        try {
            return $this->adapterEnumerationValue->insertEnumerationValue( $fieldAndVals );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return 0;
        }// @codeCoverageIgnoreEnd
    }
    
    /**
     * Insert or Update a record into the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows inserted (should be 1). 0 on error or exception.
     * 
     * Note: This differs slightly from insert so that enum repair tool (AnalyzeRepairFormData job) can update 
     * additional_info_field_name in special cases where the read query fails to find the record where the
     * additional_info_field_name field may be empty for target form id. Can also use for general insert update ops.
     */
    public function insertUpdateEnumerationValue( $fieldAndVals )
    {
        try {
            return $this->adapterEnumerationValue->insertUpdateEnumerationValue( $fieldAndVals );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return 0;
        }// @codeCoverageIgnoreEnd
    }
    
    /**
     * Delete a record into the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to insert
     * @return int number of rows deleted (should be 1). 0 on error or exception.
     */
    public function deleteEnumerationValue( $fieldAndVals )
    {
        try {
            return $this->adapterEnumerationValue->deleteEnumerationValue( $fieldAndVals );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return 0;
        }// @codeCoverageIgnoreEnd
    }
    
    /**
     * Retrieve a record from the enumeration value table.
     * @param array $fieldAndVals kv pairs array of strings for fields and values to query against
     * @return array
     */
    public function readEnumerationValue( $fieldAndVals )
    {
        try {
            return $this->adapterEnumerationValue->readEnumerationValue( $fieldAndVals );
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $origin = __CLASS__ . '::' . __FUNCTION__ . ' L: ' . __LINE__;
            $errMsg = 'Origin: ' . $origin . '; Exception [ ' . $e->getMessage() . ' ] at exception line: ' . $e->getLine();
            $this->logger->log(Logger::ERR, $errMsg);
            return [];
        }// @codeCoverageIgnoreEnd
    }
    

}

<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Service\DataTransformer\Universal\Handler;

use InvalidArgumentException;

use Base\Service\DataTransformer\Handler;
use Base\Service\DataTransformer\CommonData;
use Base\Service\FormService;
use Base\Adapter\Db\FormFieldAdapter;

class ToCommon extends Handler
{
    /**
     * @var Base\Adapter\Db\FormFieldAdapter
     */
    protected $modelFormField;

    protected $internalFieldNames = ['_pages', 'Incident', 'People', 'Vehicles', 'Citations'];

    public function __construct(FormFieldAdapter $modelFormField)
    {
        $this->modelFormField = $modelFormField;
    }

    /**
     * Transforms data in universal format to the common data structure.
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     */
    public function transform($data)
    {
        $transformedData = [];

        if (empty($data)) {
            throw new InvalidArgumentException('Empty data given');
        }

        $metadata = [
            '_pages' => $data['_pages'],
            'path' => [],
            'formSystemName' => FormService::SYSTEM_UNIVERSAL
        ];

        $associativeData = $this->arrayFilterRecursive($data);
        $flatData = $this->flattenData($associativeData);
        $fieldMappings = $this->cleanPath(
            $this->modelFormField->getCommonMapByFormSystemName(FormService::SYSTEM_UNIVERSAL, false)
        );

        foreach ($flatData as $path => $value) {
            $pathForSearch = preg_replace(['/\/\d+/', '/[\-_]/'], '', strtolower($path));

            if (empty($fieldMappings[$pathForSearch])) {
                continue;
            }

            $fullPathCommon = $fieldMappings[$pathForSearch]['fullPathCommon'];
            $commonFieldPath = $this->transformPath(
                $path,
                $fieldMappings[$pathForSearch]['fullPathVendor'],
                $fullPathCommon,
                $associativeData
            );

            if (!empty($fieldMappings[$pathForSearch]['isIncludedInMetadata'])) {
                $metadata['path'][$commonFieldPath] = $path;
            }

            $commonFieldPath = $this->postPathTransform($commonFieldPath);
            $this->buildTransformedData($transformedData, $commonFieldPath, $value);
        }

        return new CommonData($transformedData, $metadata);
    }

    /**
     * Additional transformation layer.
     * @param string $path
     * @return array
     */
    protected function postPathTransform($path)
    {
        return explode('/', $path);
    }

    protected function isInternalField($fieldName)
    {
        return in_array($fieldName, $this->internalFieldNames);
    }

    /**
     * Checks if field is empty.
     * @param string $fieldValue
     * @return boolean
     */
    protected function isFieldEmpty($fieldValue)
    {
        $result = trim($fieldValue) == '';

        return $result;
    }

    /**
     * @TODO: Will be removed in future and it's related function.
     * Transforms data structure in universal format to associative array.
     * @param type $data
     * @return array
     * @throws InvalidArgumentException
     */
    protected function transformToAssociativeArray($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('data has to be an array, ' . gettype($data) . ' is given');
        }
        $transformedData = [];

        foreach ($data as $field => $value) {
            if (!$this->isInternalField($field) && (is_array($value) || is_object($value))) {
                throw new InvalidArgumentException("Invalid data field $field");
            }
            $fieldInfo = $this->parseField($field);

            if (!$this->isInternalField($field) && (empty($fieldInfo) || $this->isFieldEmpty($value))) {
                continue;
            }

            $instances = $fieldInfo['instances'];
            $field = $fieldInfo['field'];
            $group = $fieldInfo['group'];

            if (!empty($instances['f'])) {
                $field = $field . $instances['f'];
            }

            if (empty($group)) {
                $transformedData[$field] = $value;
            } else {
                if (empty($instances['t'])) {
                    $transformedData[$group][$field] = $value;
                } else {
                    $transformedData[$group][$instances['t'] - 1][$field] = $value;
                }
            }
        }
        $transformedData = $this->sortGroupElements($transformedData);

        return $transformedData;
    }
    
    /**
     * Sorts elements within group.
     * @param type $data
     * @return array
     */
    protected function sortGroupElements($data)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $group => $value) {
            // if group have multiple instances - sort them out
            if (is_array($data[$group]) && isset($value[0])) {
                ksort($data[$group]);
            }
        }

        return $data;
    }

    /**
     * Retrieves group from the given field.
     * @param string $field
     * @return string
     */
    protected function getFieldGroup($field)
    {
        if (empty($field)) {
            return;
        }

        $group = null;
        $fieldParts = explode('_', $field);
        if (count($fieldParts) > 1) {
            $group = $fieldParts[0];
        }

        return $group;
    }

    /**
     * Retrieves field instances from the given field.
     * @param string $field
     * @return array
     */
    protected function getFieldInstances($field)
    {
        if (empty($field)) {
            return;
        }

        $fieldInstances = null;
        $fieldParts = explode('-', $field);
        if (count($fieldParts) > 1) {
            unset($fieldParts[0]);
            foreach ($fieldParts as $instance) {
                $instanceName = substr($instance, 0, 1);
                $instanceValue = substr($instance, 1);
                $fieldInstances[$instanceName] = $instanceValue;
            }
        }

        return $fieldInstances;
    }

    /**
     * Retrieves field name from the given field.
     * @param string $field
     * @return string
     */
    protected function getFieldName($field)
    {
        if (empty($field)) {
            return;
        }

        $fieldName = $field;
        $fieldParts = explode('-', $field);
        $fieldSubParts = explode('_', $fieldParts[0]);
        // checking $fieldSubParts[0] in order to filter out stuff like '_pages'
        if (!empty($fieldSubParts[0]) && count($fieldSubParts) > 1) {
            unset($fieldSubParts[0]);
            $fieldName = implode('_', $fieldSubParts);
        }

        return $fieldName;
    }

    /**
     * Parses field and returns its parts.
     * @param string $field
     * @return array
     */
    protected function parseField($field)
    {
        if (empty($field)) {
            return;
        }

        $fieldInfo = [
            'group' => $this->getFieldGroup($field),
            'field' => $this->getFieldName($field),
            'instances' => $this->getFieldInstances($field)
        ];

        return $fieldInfo;
    }

    /**
     * Remove variables from pathes.
     *
     * @param array $path
     * @return array
     */
    protected function cleanPath($path)
    {
        if (empty($path)) {
            return;
        }

        foreach ($path as $key => $value) {
            $cleanKey = preg_replace('/\[.*?\]\/?/', '', $key);
            $value['fullPathCommon'] = $this->stripPathConstructions($value['fullPathCommon']);
            $path[$cleanKey] = $value;
            if ($key != $cleanKey) {
                unset($path[$key]);
            }
        }

        return $path;
    }
    
    /**
     * Remove empty values from data.
     *
     * @param array $path
     * @return array
     */
    protected function arrayFilterRecursive($data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->arrayFilterRecursive($value);
            }
        }
       
        return array_filter($data, function($a) {return  !empty($a) && !is_null($a) && $a !== '';});
    }
}

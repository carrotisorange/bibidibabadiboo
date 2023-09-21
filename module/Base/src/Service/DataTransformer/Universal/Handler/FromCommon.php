<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Service\DataTransformer\Universal\Handler;

use InvalidArgumentException;

use Base\Service\DataTransformer\Handler;
use Base\Service\FormService;
use Base\Adapter\Db\FormFieldAdapter;

class FromCommon extends Handler
{
    /**
     * @var Base\Adapter\Db\FormFieldAdapter
     */
    protected $modelFormField;

    public function __construct(FormFieldAdapter $modelFormField)
    {
        $this->modelFormField = $modelFormField;
    }

    /**
     * Converts common format to universal.
     * @param Models_DataTransformer_CommonData $data
     * @return array
     * @throws InvalidArgumentException
     */
    public function transform($data)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Empty data given');
        }

        $associativeData = [];
        $metadata = $data->getMetadata();
        //working with array is faster then with an object
        $data = $data->getArrayCopy();
        $flatData = $this->flattenData($data);
        $fieldMappings = $this->cleanPath(
            $this->modelFormField->getCommonMapByFormSystemName(FormService::SYSTEM_UNIVERSAL, true, true)
        );

        foreach ($flatData as $path => $value) {
            $pathForSearch = preg_replace('/\/\d+/', '', strtolower($path));

            if (empty($fieldMappings[$pathForSearch])) {
                continue;
            }

            foreach ($fieldMappings[$pathForSearch] as $mapping) {
                $fullPathVendor = $mapping['fullPathVendor'];

                if (empty($metadata['path'][$path]) || $metadata['formSystemName'] != FormService::SYSTEM_UNIVERSAL) {
                    $vendorFieldPath = $this->transformPath(
                        $path,
                        $mapping['fullPathCommon'],
                        $fullPathVendor,
                        $data
                    );
                } else {
                    $vendorFieldPath = $metadata['path'][$path];
                }

                $this->buildTransformedData($associativeData, explode('/', $vendorFieldPath), $value);

                if (!empty($metadata['path'][$path])) {
                    break;
                }
            }
        }
        $associativeData = $this->transformToUniversalFormat($associativeData);
        $associativeData['_pages'] = $metadata['_pages'];

        return $associativeData;
    }

    protected function transformToUniversalFormat($data)
    {
        if (empty($data)) {
            return;
        }

        $result = [];
        foreach($data as $group => $elements) {
            foreach ($elements as $elementKey => $elementValue) {
                if (is_array($elementValue) && strtolower($group) != 'incident') {
                    // looping thru elements in group that contains multiple instances (for example person)
                    foreach ($elementValue as $field => $value) {
                        $field = $this->createUniversalField($group, $field, $elementKey + 1);
                        $result[$field] = $value;
                    }
                } else {
                    $field = $this->createUniversalField($group, $elementKey);
                    $result[$field] = $elementValue;
                }
            }
        }
        
        return $result;
    }

    protected function createUniversalField($group, $field, $instanceNumber = false)
    {
        $fieldModified = $group . '_' . $field;
        $fieldInstance = substr($field, -1);
        $fieldInstancePostfix = '';
        $instanceNumberPostfix = '';

        if (is_numeric($fieldInstance)) {
            $fieldModified = rtrim($fieldModified, $fieldInstance);
            $fieldInstancePostfix = "-f$fieldInstance";
        }
        
        if ($instanceNumber) {
            $instanceNumberPostfix = "-t$instanceNumber";
        }
        
        $fieldModified .= $instanceNumberPostfix . $fieldInstancePostfix;

        return $fieldModified;
    }


    protected function cleanPath($paths)
    {
        if (empty($paths)) {
            return;
        }

        foreach ($paths as $key => $path) {
            $cleanKey = preg_replace('/\[.*?\]\/?/', '', $key);
            foreach ($path as $pathKey => $value) {
                $value['fullPathVendor'] = $this->stripPathConstructions($value['fullPathVendor']);
                $paths[$cleanKey][$pathKey] = $value;
            }
            if ($key != $cleanKey) {
                unset($paths[$key]);
            }
        }

        return $paths;
    }

    public function transformFlatDataToVendorNames(array $flatData, array $metadata)
    {
        if (empty($flatData) || empty($metadata)) {
            return;
        }
        $result = [];

        $fieldMappings = $this->cleanPath(
            $this->modelFormField->getCommonMapByFormSystemName(FormService::SYSTEM_UNIVERSAL, true, true)
        );

        foreach ($flatData as $path) {
            $pathForSearch = preg_replace('/\/\d+/', '', strtolower($path));

            if (empty($fieldMappings[$pathForSearch])) {
                continue;
            }

            foreach ($fieldMappings[$pathForSearch] as $mapping) {
                $fullPathVendor = $mapping['fullPathVendor'];

                if (empty($metadata['path'][$path])) {
                    $vendorFieldPath = $this->transformPath(
                        $path,
                        $mapping['fullPathCommon'],
                        $fullPathVendor
                    );
                } else {
                    $vendorFieldPath = $metadata['path'][$path];
                }

                $associativeData = [];
                $this->buildTransformedData($associativeData, explode('/', $vendorFieldPath));
                $vendorFieldName = $this->transformToUniversalFormat($associativeData);
                $result[$path] = key($vendorFieldName);

                if (!empty($metadata['path'][$path])) {
                    break;
                }
            }
        }

        return $result;
    }
}

<?php
/**
 * @copyright (c) 2012 LexisNexis. All rights reserved.
 */
namespace Base\Service\DataTransformer;

abstract class Handler
{
    /**
     * Performs transformation (mapping) from one structoure to another.
     */
    abstract public function transform($data);

    /**
     * Transforms one path to another using source path. It populates variables in pathFrom using
     * source path. Then it maps pathFrom to pathTo using those variables.
     *
     * Example of sorce path: Vehicles/1/HazmatPlackard
     * Example of pathFrom: Vehicles/[a]/HazmatPlackard
     * Example of pathTo: vehicle/[a]/Commercial/HazmatPlackard
     * Example of output: vehicle/1/Commercial/HazmatPlackard
     *
     * @param string $source
     * @param string $pathFrom
     * @param string $pathTo
     * @param array $data flatten data
     * @return string
     */
    protected function transformPath($source, $pathFrom, $pathTo, $data = null)
    {
        $variableSet = $this->defineVariable($source, $pathFrom, $data);
        $pathFrom = $this->stripPathConstructions($pathFrom);
        $pathTo = $this->stripPathConstructions($pathTo);
        $sourceItems = explode('/', $source);
        $pathFromItems = explode('/', $pathFrom);
        $pathTransformed = '';

        foreach ($pathFromItems as $key => $item) {
            // checking if current item is variable
            if ((empty($variableSet) || !in_array($item, array_keys($variableSet)))
                && (substr($item, 0, 1) == '[' && substr($item, -1) == ']')) {
                // getting variable's value from source
                $associatedItem = $pathFromItems[$key - 1];
                $variableSet[$item] = $sourceItems[array_search($associatedItem, $sourceItems) + 1];
            }
        }
        if (!empty($variableSet)) {
            foreach ($variableSet as $variable => $value) {
                $pathTransformed = str_replace($variable, $value, $pathTo);
            }
        } else {
            $pathTransformed = $pathTo;
        }

        return $pathTransformed;
    }

    /**
     * Parses path and defines variable.
     * Example of path:
     * [a:=person/vehicleUnitNumber=vehicle/unitNumber WHERE personType=Driver]vehicle/[a]
     * @param string $source
     * @param string $pathFrom
     * @param array $data
     * @return type
     */
    protected function defineVariable($source, $pathFrom, $data)
    {
        $variableSet = [];
        $pathFrom = str_replace(' ', '', $pathFrom);
        preg_match('/\[:(.*:=[^\]]*)/', $pathFrom, $matches);
        if (empty($matches)) {
            return;
        }
        list($varName, $definition) = explode(':=', $matches[1]);

        $definition = explode('WHERE', $definition);
        $equationParts = explode('=', $definition[0]);
        $equationLeftPart = explode('/', $equationParts[0]);
        $equationRightPart = explode('/', $equationParts[1]);

        preg_match('/\/(\d)\//', $source, $matches);
        $searchKey = $matches[1];
        $where = (empty($definition[1])) ? null : explode('=', $definition[1]);

        foreach ($data[$equationLeftPart[0]] as $key => $item) {
            if (!empty($item[$equationLeftPart[1]])
                && !empty($data[$equationRightPart[0]][$searchKey][$equationRightPart[1]])
                && $item[$equationLeftPart[1]] == $data[$equationRightPart[0]][$searchKey][$equationRightPart[1]]) {

                if (empty($where)
                    || (!empty($where)
                        && ((is_array($item[$where[0]]) && in_array($where[1], $item[$where[0]]))
                            || (!is_array($item[$where[0]]) && strcasecmp($item[$where[0]], $where[1]) == 0)))) {
                    $variableSet["[$varName]"] = $key;
                    break;
                }
            }
        }

        return $variableSet;
    }

    /**
     * Recursive function that builds an associative array (that is passing by reference)
     * using element's path and value.
     *
     * @param array &$data
     * @param string $path
     * @param string|int|float $value
     */
    protected function buildTransformedData(&$data, $path, $value = null)
    {
        $key = array_shift($path);
        if (empty($path)) {
            if ($key == '[#]') {
                $data[] = $value;
            } else {
                if (isset($data[$key])) {
                    if (!is_array($data[$key])) {
                        $data[$key] = [$data[$key], $value];
                    } else {
                        $data[$key][] = $value;
                    }
                } else {
                    $data[$key] = $value;
                }
            }
        } else {
            if ($key != '[#]' && (!isset($data[$key]) || !is_array($data[$key]))) {
                $data[$key] = [];
            }
            if ($key == '[#]') {
                $this->buildTransformedData($data[], $path, $value);
            } else {
                $this->buildTransformedData($data[$key], $path, $value);
            }
        }
    }

    /**
     * Recursive function that converts multi-dimensional array to
     * one-dimentional array (where key = path to element and value = value of element)
     * and returns it.
     * @param array $inputData
     * @param string $path
     * @return array
     */
    public function flattenData($inputData, $path = null)
    {
        $data = [];
        if (!is_null($path)) {
            $path = $path . '/';
        }
        if (is_array($inputData) || is_object($inputData) || $inputData instanceof CommonData) {
            foreach ($inputData as $key => $value) {
                if ((is_array($value) && empty($value))) {
                    $value = '';
                }
                if (!is_array($value) && !is_object($value)) {
                    $data[$path . $key] = $value;
                } else {
                    $data = array_merge($data, $this->flattenData($value, $path . $key, '/'));
                }
            }
        }

        return $data;
    }

    protected function stripPathConstructions($path)
    {
        return preg_replace('/\[:[^\]]*\]/', '', $path);
    }
}
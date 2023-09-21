<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
 namespace Base\Service\DataTransformer;

abstract class DataTransformer
{

    protected $handlerToCommon;

    protected $handlerFromCommon;

    public function __construct(
        Handler $handlerToCommon = null,
        Handler $handlerFromCommon)
    {
        $this->handlerToCommon = $handlerToCommon;
        $this->handlerFromCommon = $handlerFromCommon;
    }

    public function toCommon($data)
    {
        return $this->handlerToCommon->transform($data);
    }

    public function fromCommon($data)
    {
        return $this->handlerFromCommon->transform($data);
    }


    public function transformFlatDataToVendorNames($flatData, $metadata)
    {
        return $this->handlerFromCommon->transformFlatDataToVendorNames($flatData, $metadata);
    }

    /**
     * Convert an array (common or native data) in to a flat representation
     *
     * array('a' => array('b' => array(array('c' => 1), array('d' => 2))));
     * turns into
     *
     * array(
     *  'a/b/0/c' => 1,
     *  'a/b/1/d' => 2
     * );
     *
     * @param array|Base\Service\DataTransformer\CommonData $data
     * @return array
     */
    public function toFlat($data) 
    {
        /**
         * There is no particular reason why toCommom was used here instead of fromCommon.
         * It is assumed that flattenData will be exactly the same for either of them.
         */
        return $this->handlerToCommon->flattenData($data);
    }
    
    public function toCamelCase($oldKey) 
    {
        //converts Abc_Def to abcDef
        $camelCased = explode("_", strtolower($oldKey));
        $len = count($camelCased);
        for($i = 1; $i < $len; ++$i) {
            $camelCased[$i] = ucwords($camelCased[$i]);
        }

        return implode("", $camelCased);
    }

}

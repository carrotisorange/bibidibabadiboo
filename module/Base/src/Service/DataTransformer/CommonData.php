<?php
/**
 * @copyright (c) 2020 LexisNexis. All rights reserved.
 */
namespace Base\Service\DataTransformer;

use Zend\Stdlib\ArrayObject;
use ArrayIterator;

class CommonData extends ArrayObject
{
    /**
     * Stores the actual common data.
     * @var array
     */
    protected $commonData;

    /**
     * Stores metadata that is used during conversions.
     * @var array
     */
    protected $metadata;

    protected $position = 0;

    public function __construct(array $commonData, array $metadata = null)
    {
        $this->commonData = $commonData;
        $this->metadata = $metadata;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->commonData[] = $value;
        } else {
            $this->commonData[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->commonData[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->commonData[$offset]);
    }

    public function &__get($name)
    {
        return $this->commonData[$name];
    }

    public function &offsetGet($offset)
    {
        $result = null;
        if (isset($this->commonData[$offset])) {
            $result = $this->commonData[$offset];
        }
        
        return $result;
    }

    public function rewind()
    {
        $this->position = 0;
    }
    public function current()
    {
        return $this->commonData[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->commonData[$this->position]);
    }

    public function count()
    {
        return count($this->commonData);
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->commonData);
    }

    public function getArrayCopy()
    {
        return $this->commonData;
    }
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Util;

/**
 * A class that flattens or unflattens an array, based on a given array key delimiter.
 * @author Randall Betta
 *
 */
class ArrayTransformer
{
    
    /**
     * The array to transform.
     * @var array
     */
    private array $array;

    /**
     * Construct an instance of this class.
     * @param array $array The array to transform.
     */
    public function __construct(array $array = [])
    {
        $this->setArray($array);
    }
    
    /**
     * Set the array to transform.
     * @param array $array
     * @return $this
     */
    public function setArray(array $array) : self
    {
        $this->array = $array;
        return $this;
    }
    
    /**
     * Get the array to transform.
     * @return array
     */
    public function getArray() : array
    {
        return $this->array;
    }
    
    /**
     * Parse the array to a nested array based on the array's keys,
     * creating a new level for each array key substring separated by a
     * given delimiter.
     * @param string $delimiter The delimiter to use when parsing each array
     * key into a nested array structure.
     * @return array The resulting nested array.
     */
    public function toNestedArray(string $delimiter) : array
    {
        $results = [];
        $array = $this->getArray();
        foreach ($array as $key => $value) {
            
            // Create a nested array of delimited array key parts.
            $resultLevel    = & $results;
            $parts          = explode($delimiter, $key);
            $partsCount     = count($parts);
            
            // Iteratively initialize each nonexistent level of the
            // results array.
            for ($i = 0; $i < $partsCount; $i++) {
                $part = $parts[$i];
                if ( ! array_key_exists($part, $resultLevel) ) {
                    $resultLevel[$part] = [];
                }
                $resultLevel = & $resultLevel[$part];
            }
            
            // Add the messages to the depest nested level of the results array.
            $resultLevel = $value;
            
        }
        return $results;
    }
    
    /**
     * Convert a nested array into a flat array, combining the various
     * levels' keys using a given delimiter.
     * @param string $delimiter The dellimiter to use when joining array
     * keys from different levels into a single level.
     * @return array The flattened array.
     */
    public function toFlatArray(string $delimiter) : array
    {
        $results = [];
        $array = $this->getArray();
        foreach ($array as $key => $value) {
            
            if (is_array($value)) {
                
                $prefix = $key . $delimiter;
                $nextValues = new ArrayTransformer($value);
                foreach ($nextValues->toFlatArray($delimiter) as $nextKey => $nextValue) {
                    $results[$prefix . $nextKey] = $nextValue;
                }
                
            } else {
                $results[$key] = $value;
            }
            
        }
        return $results;
    }

}

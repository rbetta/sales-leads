<?php
declare(strict_types = 1);
namespace Carcosa\Core\Json;

/**
 * A class to recursively transform decoded JSON objects into arrays.
 */
class DecodedJsonTransformer
{

    /**
     * Convert all objects in a decoded JSON value into arrays. Other data
     * types are left unmodified.
     * @param mixed $data
     * @return mixed
     */
    public function convertObjectsToArrays($data)
    {
        
        // Convert the data from an object to an array.
        if (is_object($data)) {
            $data = (array) $data;
        }
        
        // Recursively convert all child elements in the data (if any).
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertObjectsToArrays($value);
            }
        }
        
        return $data;
        
    }

}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Traits;

/**
 * A trait that is sufficient to implement the iSensitiveKeyValueCollection
 * interface.
 * @author Randall Betta
 *
 */
trait tSensitiveKeyValueCollection
{
    
    use tKeyValueCollection;
    
    /**
     * Prevent sensitive data from appearing in the output of PHP's
     * var_dump() function.
     * @return array 
     * @throws \RuntimeException If the key-value data storage property
     * cannot be found.
     */
    public function __debugInfo() : array
    {
        // Define the property where key-value data are stored.
        $dataProperty = 'data';
        
        // Retrieve the contents of this instance.
        $debugData = get_object_vars($this);
        
        // Ensure the property where key-value data are stored is present.
        // If it is not, then this may indicate that the property value was
        // changed in the underlying trait, and therefore that this code must
        // also be updated in order to maintain security.
        if (! array_key_exists($dataProperty, $debugData)) {
            throw new \RuntimeException(
                "The expected data storage property $dataProperty was" .
                "not found by " . __METHOD__
            );
        }
        
        // Redact all sensitive data.
        $debugData[$dataProperty] = array_map(
            fn($value) => ("*** SENSITIVE DATA ***"),
            $debugData[$dataProperty]
        );
        
        return $debugData;
    }
    
}

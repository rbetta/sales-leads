<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Interfaces;

/**
 * An interface for a collection of sensitive key-value pairs. These values
 * should not appear in the output of PHP's var_dump() function.
 * @author Randall Betta
 *
 */
interface iSensitiveKeyValueCollection extends iKeyValueCollection
{
    
    /**
     * Prevent sensitive data from appearing in the output of PHP's
     * var_dump() function.
     * @return array 
     */
    public function __debugInfo() : array;
    
}

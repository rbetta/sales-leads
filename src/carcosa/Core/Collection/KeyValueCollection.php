<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection;

use Carcosa\Core\Collection\Interfaces\iKeyValueCollection;
use Carcosa\Core\Collection\Traits\tKeyValueCollection;

/**
 * A key-value collection.
 * @author Randall Betta
 *
 */
class KeyValueCollection implements iKeyValueCollection, \JsonSerializable
{
    
    use tKeyValueCollection;
    
    /**
     * Convert this instance into a format suitable for conversion to JSON.
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        return (object) $this->getValues();
    }
    
}

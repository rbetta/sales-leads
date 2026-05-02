<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\Json\DecodedJsonTransformer;
use Illuminate\Validation\Validator;

/**
 * An interface that defines a factory for creating data objects.
 */
interface iDataObjectFactory
{
    
    /**
     * Create a new, empty instance of the class this factory handles.
     * @return iDataObject
     */
    public function create() : iDataObject;
    
    /**
     * Attempt to create an instance of this data object from its
     * JSON-decoded data representation.
     * @param \stdObject $jsonData The decoded JSON representation of a
     * data object instance (which must implement the iDataObject interface).
     * @return iDataObject
     */
    public function createFromDecodedJson(\stdClass $jsonData) : iDataObject;
    
}

<?php
declare(strict_types = 1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\DataObjects\iDataObject;

/**
 * An interface that allows an Eloquent Model instance or similar class
 * to be converted into a pure data representation, suitable for use in
 * an API request or response.
 * @author Randall Betta
 *
 */
interface iToDataObject
{
    
    /**
     * Return a class instance that implements the iDataObject interface,
     * which represents this instance's data in a manner suitable for use
     * in an API request or response.
     * @return iDataObject
     */
    public function toDataObject() : iDataObject;
    
}

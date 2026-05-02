<?php
declare(strict_types = 1);

namespace Carcosa\Core\Service;

use Carcosa\Core\Messages\MessageCollection;

/**
 * An interface that represents a service result.
 * @author Randall Betta
 */
interface iServiceResult
{
    
    /**
     * Get a value.
     * @param string $name The data name.
     * @return mixed Any non-resource value.
     * @throws \LogicException If a nonexistent data name is supplied.
     */
    public function getValue(string $name);
    
    /**
     * Get whether a value exists
     * @param string $name The data name.
     * @return bool
     */
    public function getHasValue(string $name) : bool;
    
    /**
     * Get all values.
     * @return array An array whose keys are value names as strings, and
     * whose values are their corresponding non-resource data values.
     */
    public function getValues() : array;
    
    /**
     * Get whether this instance contains at least one error message.
     * @return bool
     */
    public function getHasError() : bool;
    
    /**
     * Get a copy of this instance's data as an associative array.
     * @return array An array whose keys are data names as strings,
     * and whose values are their corresponding non-resource data values.
     */
    public function toArray() : array;
    
	/**
	 * Get the MessageCollection that contains this instance's messages.
	 * @return MessageCollection
	 */
	public function getMessages() : MessageCollection;
    
}

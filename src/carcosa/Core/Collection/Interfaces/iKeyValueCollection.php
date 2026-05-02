<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Interfaces;

/**
 * An interface for a key-value collection.
 * @author Randall Betta
 *
 */
interface iKeyValueCollection extends \Countable, \IteratorAggregate
{
    
    /**
     * Set whether to throw an exception if a nonexistent key is accessed.
     * @param bool $throw
     * @return $this
     */
    public function setThrowExceptionOnNonexistentKey(bool $throw) : self;
    
    /**
     * Get whether to throw an exception if a nonexistent key is accessed.
     * @return bool
     */
    public function getThrowExceptionOnNonexistentKey() : bool;
    
    /**
     * Set the default value to return when a nonexistent key is accessed
     * (if this instance is not configured to throw an exception when this
     * occurs).
     * @param mixed $value
     * @return $this
     */
    public function setDefaultValue($value) : self;
    
    /**
     * Get the default value to return when a nonexistent key is accessed
     * (if this instance is not configured to throw an exception when this
     * occurs).
     * @return mixed
     */
    public function getDefaultValue();
    
    /**
     * Set a datum.
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $key, $value) : self;
    
    /**
     * Get a datum.
     * @param string $key
     * @return mixed The value, if the key exists. If the key does not exist,
     * then either an exception will be thrown, or the default value will
     * be returned (depending on the instance's configuration). 
     * @throws \RuntimeException If a nonexistent key is provided, and this
     * instance is configured to throw an exception when this occurs.
     */
    public function getValue(string $key);
    
    /**
     * Set multiple keys and values in this collection.
     * @param mixed[] $data An array of keys and values to store in this
     * instance. Keys must be strings or integers.
     * @return $this
     */
    public function setValues(array $data) : self;
    
    /**
     * Get all values in this collection.
     * @return array An associative arrays of all keys and values in
     * this instance.
     */
    public function getValues() : array;
    
    /**
     * Get whether a key exists.
     * @param string|int $key
     * @return bool
     */
    public function hasKey(string $key) : bool;
    
    /**
     * Delete the value with a given key.
     * @param string|int $key
     * @return $this
     */
    public function delete(string $key) : self;
    
}

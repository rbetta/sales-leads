<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Traits;

/**
 * A trait that implements a key-value collection.
 * @author Randall Betta
 *
 */
trait tKeyValueCollection
{
    
    /**
     * Whether to throw an exception when a nonexistent key is accessed.
     * @var bool
     */
    private bool $throwExceptionOnNonexistentKey = false;
    
    /**
     * The default value to return if a nonexistent key is accessed (only if
     * an exception is not thrown when this happens).
     * @var mixed
     */
    private $defaultValue = null;
    
    /**
     * The data in this instance.
     * @var array An associative array of keys and values.
     */
    private array $data = [];
    
    /**
     * Set whether to throw an exception if a nonexistent key is accessed.
     * @param bool $throw
     * @return $this
     */
    public function setThrowExceptionOnNonexistentKey(bool $throw) : self
    {
        $this->throwExceptionOnNonexistentKey = $throw;
        return $this;
    }
    
    /**
     * Get whether to throw an exception if a nonexistent key is accessed.
     * @return bool
     */
    public function getThrowExceptionOnNonexistentKey() : bool
    {
        return $this->throwExceptionOnNonexistentKey;
    }
    
    /**
     * Set the default value to return when a nonexistent key is accessed
     * (if this instance is not configured to throw an exception when this
     * occurs).
     * @param mixed $value
     * @return $this
     */
    public function setDefaultValue($value) : self
    {
        $this->defaultValue = $value;
        return $this;
    }
    
    /**
     * Get the default value to return when a nonexistent key is accessed
     * (if this instance is not configured to throw an exception when this
     * occurs).
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set a datum.
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setValue(string $key, $value) : self
    {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Get a datum.
     * @param string $key
     * @return mixed The value, if the key exists. If the key does not exist,
     * then either an exception will be thrown, or the default value will
     * be returned (depending on the instance's configuration). 
     * @throws \RuntimeException If a nonexistent key is provided, and this
     * instance is configured to throw an exception when this occurs.
     */
    public function getValue(string $key)
    {
        if ($this->hasKey($key)) {
            
            // The key exists. Return its value.
            return $this->data[$key];
            
        } elseif ($this->getThrowExceptionOnNonexistentKey()) {
            
            // The key does not exist, and this instance is configured to
            // throw an exception when this occurs.
            throw new \RuntimeException(
                "The nonexistent key \"$key\" was supplied to " . __METHOD__
            );
            
        } else {
            
            // The key does not exist, and this instance is configured to
            // return a default value when this occurs.
            return $this->getDefaultValue();
            
        }
    }
    
    /**
     * Set multiple keys and values in this collection.
     * @param mixed[] $data An array of keys and values to store in this
     * instance. Keys must be strings or integers.
     * @return $this
     */
    public function setValues(array $data) : self
    {
        foreach ($data as $key => $value) {
            $this->setValue($key, $value);
        }
        return $this;
    }
    
    /**
     * Get all values in this collection.
     * @return array An associative arrays of all keys and values in
     * this instance.
     */
    public function getValues() : array
    {
        return $this->data;
    }
    
    /**
     * Get whether a key exists.
     * @param string $key
     * @return bool
     */
    public function hasKey(string $key) : bool
    {
        return array_key_exists($key, $this->data);
    }
    
    /**
     * Delete the value with a given key.
     * @param string $key
     * @return $this
     */
    public function delete(string $key) : self
    {
        
        if ($this->hasKey($key)) {
            
            // The key exists. Delete it.
            unset($this->data[$key]);
            
        } elseif ($this->getThrowExceptionOnNonexistentKey()) {
            
            // The key does not exist, and this instance is configured
            // to generate an exception when this occurs.
            throw new \RuntimeException(
                "The nonexistent key \"$key\" was supplied to " .
                __METHOD__
            );
            
        }
        
        return $this;
        
    }
    
    /**
     * Implement the \Countable interface.
     * @return int
     */
    public function count() : int
    {
        return count($this->data);
    }
    
    /**
     * Implement the \IteratorAggregate interface, so this instance can
     * be used in a foreach loop.
     * @return \ArrayIterator
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->data);
    }

}

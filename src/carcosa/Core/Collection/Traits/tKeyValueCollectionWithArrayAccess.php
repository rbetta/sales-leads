<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Traits;

/**
 * A trait that extends the KeyValueCollectionTrait trait to support all
 * necessary methods from the SPL \ArrayAccess interface.
 * @author Randall Betta
 *
 */
trait tKeyValueCollectionWithArrayAccess
{
    
    use tKeyValueCollection;
    
    /**
     * Set a datum.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value) : void
    {
        $this->setValue($key, $value);
        return;
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
    public function offsetGet($key)
    {
        return $this->getValue($key);
    }
    
    /**
     * Get whether a key exists.
     * @param string $key
     * @return bool
     */
    public function offsetExists($key) : bool
    {
        return $this->hasKey($key);
    }
    
    /**
     * Delete the value with a given key.
     * @param string $key
     * @return void
     */
    public function offsetUnset($key) : void
    {
        $this->delete($key);
        return;   
    }
    
}

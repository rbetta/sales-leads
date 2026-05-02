<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Illuminate\Contracts\Validation\Validator;

/**
 * An abstract class that represents a relationship between a data object and
 * one or more other data objects.
 */
abstract class AbstractDataObjectRelationship  implements \JsonSerializable, \Countable
{
    
    /**
     * Whether this relationship allows multiple associated
     * data objects.
     * @var bool
     */
    private bool $allowsMultiple;
    
    /**
     * Whether the relationship's related instances have been loaded.
     * @var bool
     */
    private bool $areRelatedInstancesLoaded;
    
    /**
     * Set whether multiple related instances are allowed in this
     * relationship.
     * @param bool $allowsMultiple
     * @return $this
     */
    protected function setAllowsMultiple(bool $allowsMultiple) : self
    {
        $this->allowsMultiple = $allowsMultiple;
        return $this;
    }
    
    /**
     * Get whether multiple related instances are allowed in this
     * relationship.
     * @return bool
     */
    public function getAllowsMultiple() : bool
    {
        return $this->allowsMultiple;
    }

    /**
     * Set whether the relationship's related instances have been loaded.
     * @param bool $loaded
     * @return $this
     */
    protected function setAreRelatedInstancesLoaded(bool $loaded) : self
    {
        $this->areRelatedInstancesLoaded = $loaded;
        return $this;
    }
    
    /**
     * Get whether the relationship's related instances have been loaded.
     * @return bool
     */
    protected function getAreRelatedInstancesLoaded() : bool
    {
        return $this->areRelatedInstancesLoaded;
    }
    
}

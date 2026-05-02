<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents a relationship between a data object and
 * up to one other data object.
 */
class DataObjectRelationshipManyToOne
extends AbstractDataObjectRelationship
{
    
    /**
     * The related data object instances.
     * @var iDataObject|null
     */
    private iDataObject|null $relatedInstance = null;
    
    /**
     * Construct an instance of this class.
     * @param iDataObject|null $relatedInstance The initial related instance
     * in this relationship (if any).
     */
    public function __construct()
    {
        $this
            ->setAllowsMultiple(false)
            ->setAreRelatedInstancesLoaded(false);
    }
    
    /**
     * Set the related instance in this relationship (if any).
     * @param iDataObject|null $instance
     * @return $this
     */
    public function setRelatedInstance(iDataObject|null $instance) : self
    {
        $this->relatedInstance = $instance;
        $this->setAreRelatedInstancesLoaded(true);
        return $this;
    }
    
    /**
     * Get the related instance in this relationship (if any).
     * @throws \RuntimeException If related instances are not yet loaded.
     * @return iDataObject|null
     */
    public function getRelatedInstance() : iDataObject|null
    {
        if (! $this->getAreRelatedInstancesLoaded()) {
            throw new \RuntimeException(
                "Attempted to invoke " . __METHOD__ . " before " .
                "related instances were loaded."
            );
        }
        return $this->relatedInstance;
    }

    /**
     * Convert this class into a representation suitable for serializing
     * to JSON.
     * @return mixed
     */
    public function jsonSerialize()
    {
        return (object) [
            "allowsMultiple"    => $this->getAllowsMultiple(),
            "relatedInstance"   => $this->getRelatedInstance(),
        ];
        
    }

    /**
     * Return the current number of related instances in this relationship.
     * @return int
     */
    public function count() : int
    {
        return (null === $this->getRelatedInstance()) ? 0 : 1;
    }
    
}

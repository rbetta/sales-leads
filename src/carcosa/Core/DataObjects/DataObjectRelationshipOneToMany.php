<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents a relationship between a data object and
 * any number of other data objects.
 */
class DataObjectRelationshipOneToMany
extends AbstractDataObjectRelationship
{
    
    /**
     * The related data object instances.
     * @var iDataObject[]
     */
    private array $relatedInstances = [];
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this
            ->setAllowsMultiple(true)
            ->setAreRelatedInstancesLoaded(false);
    }
    
    /**
     * Add a related instance to this relationship.
     * @param iDataObject $instance
     * @return $this
     * @throws \RuntimeException If the same instance is added more than once
     * (ascertained by direct comparison of memory addresses).
     * @throws \RuntimeException If the same instance is added more than once
     * (ascertained by identical, non-null IDs).
     * @throws \RuntimeException If an attempt is made to add more than one
     * related instance to a relationship when multiple related instances
     * are not permitted. 
     */
    public function addRelatedInstance(iDataObject $instance) : self
    {
        
        // Prevent adding more than one related instance if the
        // relationship does not allow it.
        if (! $this->getAllowsMultiple() && count($this) >= 1) {
            throw new \RuntimeException(
                "Attempted to add more than one related instance via " .
                __METHOD__ . " to a relationship that only allows one."
            );
        }
        
        // We are loading this relationship's related instances.
        // Set the flag indicating that the related instances
        // can now be read.
        $this->setAreRelatedInstancesLoaded(true);
        
        // Prevent duplicates in this relationship.
        $newUsesIds     = ($instance instanceof iUuid);
        $newId          = ($newUsesIds ? $instance->getId() : null);
        foreach ($this->getRelatedInstances() as $old) {
            
            $oldUsesIds = ($old instanceof iUuid);
            $oldId      = ($oldUsesIds ? $old->getId() : null);
            
            // Try to compare by ID first, irrespectively of
            // the instances' properties. Then try to compare by
            // memory location if neither instance has an ID.
            //
            // Note: we try this first before comparing memory
            // addresses because this provides a more useful
            // error message for debugging (since we can identify
            // the exact affected record by its ID).
            //
            if ($newId !== null && $oldId !== null) {
                
                // Both instances use IDs, and both have non-null IDs.
                // Compare their IDs to ensure they are not duplicates.
                if ($newId === $oldId) {
                    throw new \RuntimeException(
                        "Attempted to invoke " . __METHOD__ . ", but a " .
                        "related instance with ID $oldId is already " .
                        "present in the relationship."
                    );
                }
                
            } elseif ($newId === null && $oldId === $null) {
            
                // Try to compare by memory address.
                if ($instance === $old) {
                    $class = get_class($instance);
                    throw new \RuntimeException(
                        "Attempted to invoke " . __METHOD__ . " with " .
                        "an instance of type $class that was already " .
                        "present in the relationship (note: the " .
                        "duplicate instance has no ID)."
                    );
                }
                
            }
        }
        
        // Record the new related instance.
        $this->relatedInstances[] = $instance;
        return $this;
    }
    
    /**
     * Add related instances to this relationship.
     * @param iDataObject[] $instance
     * @return $this
     * @throws \RuntimeException If the same instance is added more than once
     * (ascertained by direct comparison of memory addresses).
     * @throws \RuntimeException If the same instance is added more than once
     * (ascertained by identical, non-null IDs).
     * @throws \RuntimeException If an attempt is made to add more than one
     * related instance to a relationship when multiple related instances
     * are not permitted. 
     */
    public function addRelatedInstances(array $instances) : self
    {
        foreach ($instances as $instance) {
            $this->addRelatedInstance($instance);
        }
        $this->setAreRelatedInstancesLoaded(true);
        return $this;
    }
    
    /**
     * Get all related instances in this relationship.
     * @throws \RuntimeException If related instances are not yet loaded.
     * @return iDataObject[]
     */
    public function getRelatedInstances() : array
    {
        if (! $this->getAreRelatedInstancesLoaded()) {
            throw new \RuntimeException(
                "Attempted to invoke " . __METHOD__ . " before " .
                "related instances were loaded."
            );
        }
        return $this->relatedInstances;
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
            "relatedInstances"  => $this->getRelatedInstances(),
        ];
        
    }

    /**
     * Return the current number of related instances in this relationship.
     * @return int
     */
    public function count() : int
    {
        return count($this->getRelatedInstances());
    }
    
}

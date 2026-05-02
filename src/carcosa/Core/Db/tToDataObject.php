<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Carcosa\Core\DataObjects\AbstractDataObject;
use Carcosa\Core\DataObjects\AbstractDataObjectFactory;
use Carcosa\Core\DataObjects\DataObjectRelationshipFactory;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\DataObjects\iSoftDeletableTimestampedUuid;
use Carcosa\Core\DataObjects\iTimestampedUuid;
use Carcosa\Core\DataObjects\iToDataObject;
use Carcosa\Core\DataObjects\iUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * A trait that attempts to implement the iToDataObject interface,
 * centrally handling relationships and standardized fields. This
 * trait should only be used on Eloquent Model subclasses.
 * @author Randall Betta
 *
 */
trait tToDataObject
{
    
    /**
     * Get an instance of the factory class used to create this
     * model instance's associated data object (which must
     * implement the iDataObject interface).
     * @return AbstractDataObjectFactory
     */
    protected abstract function getDataObjectFactory() : AbstractDataObjectFactory;
    
    /**
     * Return a class instance that implements the iDataObject interface,
     * which represents this instance's data in a manner suitable for use
     * in an API request or response.
     * @return iDataObject
     * @throws \RuntimeException If this trait method is invoked on an
     * instance that is not an Eloquent Model subclass.
     */
    public function toDataObject() : AbstractDataObject
    {
        
        // Ensure this method is only invoked on Eloquent Model subclasses.
        if (! $this instanceof Model) {
            throw new \RuntimeException(
                "Attempted to execute " . __METHOD__ . " from trait " .
                __TRAIT__ . " on an instance that is not a subclass of " .
                Model::class
            );
        }
        
        // Create a new data object to return.
        $dataObjectFactory  = $this->getDataObjectFactory();
        $dataObject         = $dataObjectFactory->create();
        
        // Assign all standardized properties.
        if ($dataObject instanceof iUuid) {
            $dataObject->setProperty("id", $this->id);
        }
        if ($dataObject instanceof iTimestampedUuid) {
            $dataObject->setProperty("createdAt", $this->created_at);
            $dataObject->setProperty("updatedAt", $this->updated_at);
        }
        if ($dataObject instanceof iSoftDeletableTimestampedUuid) {
            $dataObject->setProperty("deletedAt", $this->deleted_at);
        }
        
        // Assign all the custom properties to the new data object.
        $this->assignDataObjectProperties($dataObject);
        
        // Iterate through all relationships from the Eloquent Model
        // that is using this trait.
        $relationshipFactory = \App::make(DataObjectRelationshipFactory::class);
        foreach ($this->relations as $eloquentRelationshipName => $eloquentRelationship) {
            
            // Instantiate a data object relationship instance that
            // corresponds to this current Eloquent relationship.
            $relationshipAllowsMultiple = ($eloquentRelationship instanceof Collection);
            $relationship = $relationshipFactory->create($relationshipAllowsMultiple);
            
            // Assign this current relationship to the data object.
            $dataObject->addRelationship($eloquentRelationshipName, $relationship);
            
            // Recursively add any related instances to this current relationship.
            if ($relationshipAllowsMultiple) {
                
                // The Eloquent relationship data is a collection of
                // Eloquent model instances.
                //
                // Note: this logic must set the flag indicating that
                // associated instances have been loaded even if there
                // are no associated instances. This is why we don't
                // iteratively invoke the addRelatedModel() instance
                // in a foreach (in case the foreach is empty).
                //
                $relatedModels = [];
                foreach ($eloquentRelationship as $relatedModel) {
                    $relatedModels[] = $relatedModel->toDataObject();
                }
                $relationship->addRelatedInstances($relatedModels);
                
            } else {
                
                // The Eloquent relationship data is either an Eloquent model
                // instance, or it is null.
                $relationship->setRelatedInstance($eloquentRelationship?->toDataObject());
                
            }
            
        }
        
        return $dataObject;
        
    }
    
    /**
     * Assign custom properties to the data object created using the
     * toDataObject() method. Subclasses should override this method
     * if they have properties to assign.
     * @param iDataObject $dataObject
     * @return self
     */
    protected abstract function assignDataObjectProperties(iDataObject $dataObject) : self;
    
}

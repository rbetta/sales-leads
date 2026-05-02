<?php
declare(strict_types = 1);
namespace Carcosa\Core\Service;

use Carcosa\Core\Db\UuidModel;
use Carcosa\Core\Service\AbstractUuidModelCriteria;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * An abstract base class for managing UUID models.
 * @author Randall Betta
 *
 */
abstract class AbstractUuidModelService extends AbstractModelService
{
    
    /**
     * Apply an AbstractUuidModelCriteria instance's list of IDs
     * to a query builder instance.
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param AbstractUuidModelCriteria $criteria 
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected function applyIdsToQueryBuilder(
        EloquentBuilder $query,
        AbstractUuidModelCriteria $criteria
    ) : EloquentBuilder
    {
        
        // Obtain the IDs to use when limiting query builder results.
        $ids = $criteria->getIds();
        
        // Apply the primary key values to the query builder.
        if ($ids) {
            
            // Obtain the primary key field name for the query builder's
            // associated model.
            $primaryKeyField = $query->getModel()->getKeyName();
            
            // Modify the query.
            if (1 === count($ids)) {
                $query->where($primaryKeyField, $ids[0]);
            } else {
                $query->whereIn($primaryKeyField, $ids);
            }
            
        }
        
        return $query;
        
    }
    
    /**
     * Get the UUID primary keys from an set of UuidModel instances.
     * @param iterable $instances A set of UuidModel subclass instances.
     * @return string[] The UUID primary keys of the supplied models.
     * Duplicate values are silently discarded.
     * @throws \RuntimeException If any element of the supplied iterable
     * is not a UuidModel instance.
     * @throws \RuntimeException If any supplied UuidModel instance is not
     * yet stored in the database.
     */
    protected function getIdsFromModels(iterable $instances) : array
    {
        
        $ids = [];
        foreach ($instances as $instance) {
            
            // Ensure the array value is of the correct type.
            if (! $instance instanceof UuidModel) {
                $type = get_debug_type($instance);
                throw new \RuntimeException(
                    "An invalid value of type $type was " .
                    "encountered in the array supplied to " .
                    __METHOD__ . " (expected: array of UuidModel " .
                    "instances)."
                );
            }
            
            // Get the instance's primary key.
            $idFieldName    = $instance->getKeyName();
            $id             = $instance->{$idFieldName};
            
            // Ensure the model instance is actually stored
            // in the database already.
            if (null === $id) {
                $type = get_debug_type($instance);
                throw new \RuntimeException(
                    "A model instance of type $type supplied to " .
                    __METHOD__ . " is not yet stored in the database."
                );
            }
            
            // The instance is valid. Record its ID.
            $ids[$id] = $id;
            
        }
        
        // Return all IDs.
        return array_values($ids);
        
    }
    
}

<?php
declare(strict_types = 1);
namespace Carcosa\Core\Service;

use Carcosa\Core\Db\BaseModel;
use Carcosa\Core\Service\AbstractModelCriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * An abstract base class for managing models.
 * @author Randall Betta
 *
 */
abstract class AbstractModelService
{
    
    /**
     * Ensure the number of BaseModel instances in a set of zero or more
     * instances matches a minimum and maximum value.
     * @param iterable|BaseModel|null $instances The instances whose cardinality
     * must be tested.
     * @param int $min|null The minimum number of instances allowed.
     * Null indicates no limit.
     * @param int $mas|null The maximum number of instances allowed.
     * Null indicates no limit.
     * @throws \InvalidArgumentException If the minimum number of instances
     * allowed is negative.
     * @throws \InvalidArgumentException If the minimum number of instances
     * allowed is greater than the maximum number of instances allowed.
     * @throws \RuntimeException If the count of supplied instances is not
     * between the minimum and maximum allowed values.
     * @throws \RuntimeException If the instances are supplied as a datatype
     * that is not countable (except null).
     * @return $this;
     */
    private function validateCount(iterable|BaseModel|null $instances, int|null $min, int|null $max) : self
    {
        
        // Sanity checks.
        if ($min !== null && $min < 0) {
            throw new \InvalidArgumentException(
                "The negative minimum value $min was supplied to " . __METHOD__
            );
        } elseif ($min !== null && $max !== null && $min > $max) {
            throw new \InvalidArgumentException(
                "The minimum value $min is greater than the maximum value $max " .
                "supplied to " . __METHOD__
            );
        }
        
        // Normalize edge cases so all inputs are countable.
        if ($instances instanceof BaseModel) {
            $instances = [$instance];
        } elseif (null === $instances) {
            $instances = [];
        }
        
        // Validate countable instances. This should be the typical codepath.
        if (is_countable($instances)) {
            
            // Validate the supplied instances by invoking count().
            $count = count($instances);
            if (($min !== null && $count < $min) || ($max !== null && $count > $max)) {
                $minLabel = $min ?? "any";
                $maxLabel = $max ?? "any";
                throw new \RuntimeException(
                    "An invalid number of model instances was supplied to " .
                    __METHOD__ . " (expected: [min: $minLabel, max: $maxLabel], " .
                    "but received $count)."
                );
            }
            
        } else {
            
            // The results were not countable.
            $type = debug_get_type($instances);
            throw new \RuntimeException(
                "An unexpected $type value was supplied to " .
                __METHOD__ . " (expected: \\Countable, array, or null)."
            );
            
        }
        
        return $this;
        
    }
    
    /**
     * Return exactly one model instance from search results.
     * @param iterable|BaseModel $instances
     * @return BaseModel
     * @throws \RuntimeException If there are no results.
     * @throws \RuntimeException If there are more than one result.
     */
    protected function handleFindOne(iterable|BaseModel $instances) : BaseModel
    {
        
        // Retrieve exactly zero or one instance.
        // 
        // This avoids code duplication; we will just reject a result
        // of none immediately after.
        $instance = $this->handleFindOneOrNone($instances);
        
        // Handle the case where no model instance was supplied.
        if (null === $instance) {
            throw new \RuntimeException(
                "No model instances were supplied to " .
                __METHOD__ . " (expected: exactly 1)." 
            );
        }
        
        // One model instance was supplied. Return it.
        return $instance;
        
    }
    
    /**
     * Return exactly one model instance from search results, or
     * return null if there are none.
     * @param iterable|BaseModel|null $instances
     * @return BaseModel|null
     * @throws \RuntimeException If there are no results.
     * @throws \RuntimeException If there are more than one result.
     */
    protected function handleFindOneOrNone(iterable|BaseModel|null $instances) : BaseModel|null
    {
        // Ensure there is exactly zero or one model in the supplied instance(s).
        $this->validateCount($instances, 0, 1);
        
        // Return the only model instance, or null if there is none.
        if (null === $instances) {
            
            // No model instance was supplied.
            return null;
            
        } elseif ($instances instanceof BaseModel) {
            
            // The results are already a single model instance.
            return $instances;
            
        } else {
            
            // The results are an iterable set. Return the first one, or null if
            // none are present.
            //
            //  WARNING:
            //
            //      DO NOT just return the first indexed value. The set of
            //      model instances is not guaranteed to implement the
            //      \ArrayAccess interface, nor is it guaranteed to use
            //      an initial index of 0 even if it does.
            //
            foreach ($instances as $instance) {
                return $instance;
            }
            return null;    // No instance was present.
            
        }
    }
    
    /**
     * Validate data provided to a service and return a ServiceResult (with any error
     * messages prepopulated, if the validation fails).
     * @param array $data The provided data to validate.
     * @param Validator|null $validator A validator to apply to the supplied data.
     * If null, then no validation occurs.
     * @return ServiceResult
     */
    protected function createServiceResult(
        array $data,
        Validator|null $validator = null
    ) : ServiceResult
    {
        
        // Create the ServiceResult to return.
        $serviceResultFactory = \App::make(ServiceResultFactory::class);
        $serviceResult = $serviceResultFactory->create([]);
        
        // Handle any validation errors.
        if ($validator && $validator->fails()) {
            $serviceResult->addMessagesFromValidator($validator, true);
        }
        
        return $serviceResult;
        
    }
    
    /**
     * Handle a request to set a model's sort order value. The surrounding
     * siblings' sort orders will be adjusted in the database, but the
     * supplied instance's sort order will only be changed in memory.
     * The instance must still be saved to the database after this code runs.
     * @param Builder $queryBuilderForAllSiblings A query builder that retrieves
     * all siblings of the supplied model instance that share the same set of
     * sort order values.
     * @param BaseModel $instance The instance whose sort index is being set.
     * @param int|null $newSortOrder The instance's new sort order (or null if
     * the instance must be appended to the end of all siblings).
     * @return void
     */
    protected function handleModelSortOrderChange(
        Builder $queryBuilderForAllSiblings,
        BaseModel $instance,
        int|null $newSortOrder,
    ) : void
    {
        
        DB::transaction(function () use ($instance, $queryBuilderForAllSiblings, $newSortOrder) {
            
            //
            // CAUTION:
            //
            //      Make sure to clone the supplied query builder each time
            //      we use it, to prevent one query from polluting the next.
            //
            
            // Identify the maximum sort order currently used by any of
            // the instance's siblings.
            $maxExistingSortOrder = (clone $queryBuilderForAllSiblings)
                ->orderBy('sort_order', 'desc')
                ->limit(1)
                ->get()
                ->first()
                ?->sort_order;
            
            // If there are no siblings, then set the max existing sort
            // order to -1 (so the next value to insert is zero).
            $maxExistingSortOrder ??= -1;
            
            // Determine if we are adding a new instance or updating an
            // existing one.
            $isNew = (! $instance->exists);
            
            // Determine the instance's old and new sort order.
            $oldSortOrder = ($instance->exists) ? $instance->sort_order : null;
            $newSortOrder = $newSortOrder ?? ($maxExistingSortOrder + 1);
            
            // If the instance exists and no sort order change was requested,
            // then do nothing.
            if (null !== $oldSortOrder && $oldSortOrder === $newSortOrder) {
                return;
            }
            
            // If the new sort order would create a gap, then prevent it.
            $newSortOrder = ($isNew)
                ? min($maxExistingSortOrder + 1, $newSortOrder)
                : min($maxExistingSortOrder, $newSortOrder);
            
            // If the instance has an existing sort order in the database,
            // then move it to the last index among all of its siblings and
            // close the resulting gap.
            //
            // WARNING:
            //      This operation MUST respect any unique index on the
            //      sort_order field, so it is forward-compatible with
            //      future MySQL support for deferred indices. We choose
            //      our instance's temporary sort_order value accordingly.
            //
            if (! $isNew) {
                
                // Move the instance to the last sort index amongst all
                // of its siblings.
                (clone $queryBuilderForAllSiblings)
                    ->where('id', $instance->id)
                    ->update(['sort_order' => $maxExistingSortOrder + 1]);
                
                // Collapse the gap in the sort order left by this
                // instance's removal.
                //
                // WARNING:
                //      We MUST use an SQL ORDER BY statement here to avoid
                //      temporarily violating the unique constraint on the
                //      sort order, since MySQL does not support deferred
                //      constraints (i.e. constraints checked only at the
                //      end of the transaction, instead of immediately
                //      row-by-row).
                //
                (clone $queryBuilderForAllSiblings)
                    ->where('sort_order', '>', $oldSortOrder)
                    ->orderBy('sort_order', 'ASC')
                    ->update(['sort_order' => DB::raw('sort_order - 1')]);
                
            }
            
            // Create a gap in the siblings' sort order for this 
            // instance's new index.
            (clone $queryBuilderForAllSiblings)
                ->where('sort_order', '>=', $newSortOrder)
                ->orderBy('sort_order', 'DESC')
                ->update(['sort_order' => DB::raw('sort_order + 1')]);
            
            // Set this instance's new sort order index.
            $instance->sort_order = $newSortOrder;
            
        });
        
    }
    
}

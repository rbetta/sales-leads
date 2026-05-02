<?php
declare(strict_types = 1);
namespace App\Http\Controllers;

use App\Models\Db\User;
use Carcosa\Core\Auth\LoginManagerFactory;
use Carcosa\Core\DataObjects\iToDataObject;
use Carcosa\Core\Db\UuidModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    
    /**
     * Get the logged-in user.
     * @return User|null
     */
    protected function getLoggedInUser() : User|null
    {
        $loginManagerFactory    = \App::make(LoginManagerFactory::class);
        $loginManager           = $loginManagerFactory->create();
        $user                   = $loginManager->getLoggedInUser();
        return $user;
    }
    
    /**
     * Convert an iterable set of UuidModel subclass instances into
     * an array of values keyed by their IDs. The specific values
     * will be determined by a Closure executed on each instance.
     * @param iterable $instances An iterable set of UuidModel
     * subclass instances.
     * @param \Closure $valueTransformer The Closure that will
     * accept a model instance as its single argument, and output
     * an appropriate value of any type.
     * @return string[] An array of values, keyed to the ID of the
     * model used to generate each one.
     * @throws \RuntimeException If a value supplied in the iterable
     * argument is not a UuidModel subclass instance.
     */
    protected function getValuesByIdFromModels(iterable $instances, \Closure $valueTransformer) : array
    {
        
        $results = [];
        
        // Iterate through each supplied model instance.
        foreach ($instances as $instance) {
            
            // Validate the instance.
            if ( ! $instance instanceof UuidModel ) {
                $type       = get_debug_type($instance);
                $expected   = UuidModel::class;
                throw new \RuntimeException(
                    "An unexpected value of type $type was supplied to " .
                    __METHOD__ . " (expected: instance of $expected)."
                );
            }
            
            // Retrieve the associated value for the instance.
            $value = $valueTransformer($instance);
            
            // Record the instance's value in the results.
            $results[$instance->id] = $value;
            
        }
        
        return $results;
        
    }
    
    /**
     * Sort an array of values by the logged-in user's locale,
     * maintaining associative indices. Values will be sorted in-place.
     * @param array $values An array of values to sort according
     * to the logged-in user's locale.
     * @param \Closure|null $stringTransformer A closure that accepts an
     * arbitrary value to be sorted, and returns a string to use
     * during locale-aware comparison. If not specified, then the
     * value will be used as-is.
     * @return void
     * @throws \RuntimeException If locale-aware sorting fails.
     * @throws \RuntimeException If a value to be sorted is not a string,
     * but no string transformer closure was supplied.
     * @throws \RuntimeException If a value returned from the string
     * transformer closure was not a string. 
     */
    protected function asortByLoggedInUserLocale(array $values, \Closure|null $stringTransformer = null) : void
    {
        
        // Sort the values alphabetically, according to the user's locale.
        $user       = $this->getLoggedInUser();
        $locale     = $user->getLocale();
        $collator   = $locale->getCollator();
        uasort($values, function ($v1, $v2) use ($collator, $stringTransformer) {
            
            // Apply the string transformer, if one was supplied.
            if (null !== $stringTransformer) {
                $v1 = $stringTransformer($v1);
                $v2 = $stringTransformer($v2);
            }
            
            // Ensure that both values to compare are now actually strings.
            if (! (is_string($v1) && is_string($v2))) {
                $type1 = debug_data_type($v1);
                $type2 = debug_data_type($v2);
                throw new \RuntimeException(
                    "An unexpected array value was encountered by " .
                    __METHOD__ . " (expected: \{string, string\} for " .
                    "comparison, received: \{$v1, $v2\})"
                );
            }
            
            // Compare the two values.
            $comparisonResult = $collator->compare($v1, $v2);
            if (false === $comparisonResult) {
                //Locale-aware comparison failed.
                throw new \RuntimeException(
                    "Locale-aware sort failed for locale $locale in " . __METHOD__
                );
            }
            return $comparisonResult;
        });

    }
    
    /**
     * Get DataObject instances from an iterable set of object instances that
     * implement the iToDataObject interface. Keys are preserved.
     * @param iterable $instances Object instances that implement the
     * iToDataObject interface.
     * @return DataObject[]
     * @throws \RuntimeException If any instance in the supplied iterable
     * argument does not implement the iToDataObject interface.
     */
    protected function getDataObjectsFromModels(iterable $instances) : array
    {
        $results = [];
        foreach ($instances as $key => $instance) {
            
            // Validate this instance.
            if (! $instance instanceof iToDataObject) {
                $type = get_debug_type($instance);
                throw new \RuntimeException(
                    "An invalid value of type $type was supplied to " .
                    __METHOD__ . " (expected: object instance " .
                    "implementing the iToDataObject interface)."
                );
            }
            
            // Convert the instance to a DataObject.
            $results[$key] = $instance->toDataObject();
            
        }
        return $results;
        
    }
    
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Service;

use Carcosa\Core\Messages\MessageCollection;

/**
 * A class for creating ServiceResult instances.
 * @author Randall Betta
 */
class ServiceResultFactory
{
    
    /**
     * Create a ServiceResult instance.
     * @param array $values An associative array whose keys are field names
     * in the result, and whose values are their corresponding
     * non-resource values.
     * @param MessageCollection|null $messages Messages to include in the
     * result (typically error messages), if any.
     * @return ServiceResult
     */
    public function create(array $values, MessageCollection|null $messages = null) : ServiceResult
    {
        $serviceResult = \App::make(ServiceResult::class);
        
        // Set the data values in the Result.
        $serviceResult->setValues($values);
        
        // Add the messages into the Result, if any were supplied.
        if ($messages) {
            $serviceResult->merge($messages);
        }
        
        return $serviceResult;
    }
    
}

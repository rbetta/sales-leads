<?php
declare(strict_types = 1);

namespace Carcosa\Core\Api;

use Carcosa\Core\Messages\MessageCollection;
use Carcosa\Core\Service\ServiceResult;

/**
 * An API response class suitable for serialization to JSON.
 * @author Randall Betta
 *
 */
class ApiResponseFactory
{
    
    /**
     * Create an ApiResponse instance.
     * @param array $data An associative array of data.
     * @param MessageCollection $messages = null The messages to return
     * as part of this API response; null indicates no messages.
     * @return ApiResponse
     */
    public function create(array $data, MessageCollection|null $messages = null) : ApiResponse
    {
        return new ApiResponse($data, $messages);
    }
    
    /**
     * Create an ApiResponse instance from a ServiceResult instance.
     * @param ServiceResult $result
     * @return ApiResponse
     */
    public function createFromServiceResult(ServiceResult $result) : ApiResponse
    {
        return $this->create(
            $result->getValues(),
            $result->getMessages()
        );
    }

}

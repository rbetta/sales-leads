<?php
declare(strict_types = 1);

namespace Carcosa\Core\Api;

use Carcosa\Core\Collection\Interfaces\iKeyValueCollection;
use Carcosa\Core\Collection\Traits\tKeyValueCollection;
use Carcosa\Core\Messages\iHasMessages;
use Carcosa\Core\Messages\MessageCollection;
use Carcosa\Core\Messages\MessageCollectionFactory;
use Carcosa\Core\Messages\tHasMessages;
use Carcosa\Core\Service\ServiceResult;

/**
 * An API response class suitable for serialization to JSON.
 * @author Randall Betta
 *
 */
class ApiResponse implements \JsonSerializable, iHasMessages, iKeyValueCollection
{
    
    use tHasMessages, tKeyValueCollection;
    
    /**
     * Create an instance of this class.
     * @param array $data An associative array of data.
     * @param MessageCollection $messages = null The messages to return
     * as part of this API response; null indicates no messages.
     */
    public function __construct(array $data, MessageCollection|null $messages = null)
    {
        
        // Record the initial data.
        $this->setValues($data);
        
        // Initialize the collection of messages, and add any supplied ones
        // into the new collection.
        $this->initializeMessages();
        if (null !== $messages) {
            $this->addMessages($messages);
        }
        
    }
    
    /**
     * Convert this instance to a value suitable for use in JSON.
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        return (object) [
            'data'      => $this->getValues(),
            'messages'  => $this->getMessages(),
        ];
    }
    
}

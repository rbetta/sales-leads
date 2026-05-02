<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

use Carcosa\Core\DiscreteSet;
use Carcosa\Core\Json\JsonDecoder;
use Carcosa\Core\Util\ListFormatter;
use Carcosa\Core\Messages\Types\Error;
use Carcosa\Core\Messages\Types\Info;
use Carcosa\Core\Messages\Types\Warning;
use Illuminate\Validation\Validator;

/**
 * A factory for creating Messages instances (which contain Message instances).
 */
class MessageCollectionFactory
{

    /**
     * Create a new, empty MessageCollection instance.
     * @return MessageCollection
     */
    public function create() : MessageCollection
    {
        return new MessageCollection();
    }
    
    /**
     * Create a MessageCollection instance from its JSON-decoded representation.
     * @param \stdClass $data The JSON-decoded data that represent
     * a collection of messages.
     * @return MessageCollection
     * @throws \RuntimeException If conversion into a MessageCollection instance
     * fails.
     * @throws \RuntimeException If conversion of any individual message's data
     * into a Message instance fails.
     */
    public function createFromDecodedJson(\stdClass $data) : MessageCollection
    {
        
        // Create a MessageFactory instance to parse individual messages.
        $messageFactory = \App::make(MessageFactory::class);
        
        // Create a container for the Message instances we will return.
        $messages = \App::make(MessageCollection::class);
        
        // Iterate through all associated field names.
        foreach (get_object_vars($data) as $field => $messagesForField) {
            
            // Validate that the field name is a string.
            if (! is_string($field)) {
                throw new \RuntimeException(
                    "A field name of type " . get_debug_type($field) . " was " .
                    "encountered in " . __METHOD__ . " (expected: string)."
                );
            }
            
            // Validate that the field's associated value is an array (which
            // will contain the messages for the field).
            if (! is_array($messagesForField)) {
                $type = get_debug_type($messagesForField);
                throw new \RuntimeException(
                    "The value for the \"$field\" JSON attribute encountered " .
                    "by " . __METHOD__ . " was of type $type (expected: array)."
                );
            }
            
            // Iterate through the messages associatedc with this field.
            foreach ($messagesForField as $messageData) {
                
                // Decode the next message for this field.
                $message = $messageFactory->createFromDecodedJson($messageData);
                
                // Record the message.
                $messages->add($message, $field);
                
            }
            
        }
        
        return $messages;

    }
    
    /**
     * Create a MessageCollection instance from the validation errors
     * inside a Validator instance (typically converting them to error
     * messages, though this is configurable).
     * @param Validator $validator
     * @param bool $displayToUser Whether the Validator instance's
     * error messages should be displayable to the end user.
     * @param MessageType $messageType The type of message to add.
     * If unspecified, this defaults to error messages.
     * @return MessageCollection
     */
    public function createFromValidator(
        Validator $validator,
        bool $displayToUser,
        MessageType $type = MessageType::Error
    ) : MessageCollection
    {
        
        $messageCollection  = \App::make(MessageCollection::class);
        $messageFactory     = \App::make(MessageFactory::class);
        
        // Add every validation error message from the Validator into this
        // collection as a new Message instance of the "error" type.
        foreach ($validator->errors()->toArray() as $field => $errorsForField) {
            foreach ($errorsForField as $errorForField) {
                $message = $messageFactory->create($errorForField, $type, $displayToUser);
                $messageCollection->add($message, $field);
            }
        }
        
        return $messageCollection;
    }

}

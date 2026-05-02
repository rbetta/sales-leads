<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * A factory for creating MessageType enumeration instances.
 */
class MessageTypeFactory
{

    /**
     * Create an error message type.
     * @return MessageType;
     */
    public function createErrorType() : MessageType
    {
        return MessageType::Error;
    }

    /**
     * Create an informational message type.
     * @return MessageType;
     */
    public function createInfoType() : MessageType
    {
        return MessageType::Info;
    }

    /**
     * Create a warning message type.
     * @return MessageType;
     */
    public function createWarningType() : MessageType
    {
        return MessageType::Warning;
    }
    
    /**
     * Create a warning message type.
     * @return MessageType;
     */
    public function createDebugType() : MessageType
    {
        return MessageType::Debug;
    }

    /**
     * Create a message type from its case-sensitive value.
     * @param string $value
     * @return MessageType
     * @throws \RuntimeException If an unknown message type value is supplied.
     */
    public function createFromValue(string $value) : MessageType
    {
        $enumValue = MessageType::tryFrom($value);
        if (null === $enumValue) {

            // No message type with the specified name was found.
            throw new \RuntimeException(
                "The unknown message type value \"$value\" was supplied to " .
                __METHOD__
            );
            
        }
        return $enumValue;
    }
    
}

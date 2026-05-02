<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * An interface that represents a message type (e.g. a warning, error, or
 * informational note).
 */
interface MessageTypeInterface
{

    /**
     * Get the message type name.
     * @return string
     */
    public function getName() : string;

    /**
     * Get the message type enumeration value.
     * @return MessageType
     */
    public function getType() : MessageType;
    
    /**
     * Get whether this message type represents an error.
     * @return bool
     */
    public function getIsError() : bool;

    /**
     * Get whether this message type indicates a warning.
     * @return bool
     */
    public function getIsWarning() : bool;

    /**
     * Get whether this message type indicates simple information.
     * @return bool
     */
    public function getIsInfo() : bool;

    /**
     * Get whether this message type indicates debugging information.
     * @return bool
     */
    public function getIsDebug() : bool;

}

<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * An enumeration that lists all message types.
 */
enum MessageType : string implements \JsonSerializable
{
    case Error   = 'error';
    case Warning = 'warning';
    case Info    = 'info';
    case Debug   = 'debug';
    
    /**
     * Serialize this enumeration to JSON.
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
    
}

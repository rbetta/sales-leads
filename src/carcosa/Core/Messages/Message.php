<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

/**
 * A class that represents a message (e.g. a warning, error, or informational
 * note).
 */
class Message implements \Stringable, \JsonSerializable
{

    /**
     * The message text.
     * @var string
     */
    private string $text;

    /**
     * Whether this message is suitable for display to the end user.
     * @var bool
     */
    private bool $displayToUser;
    
    /**
     * The message type.
     * @var MessageType $type
     */
    private MessageType $type;

    /**
     * Construct the message.
     * @param string $text The message text.
     * @param MessageTypeInterface $type The message type.
     */
    public function __construct (string $text, MessageType $type, bool $displayToUser)
    {
        $this
            ->setText($text)
            ->setType($type)
            ->setDisplayToUser($displayToUser);
    }

    /**
     * Set the message text.
     * @param string $text
     * @return $this
     */
    private function setText(string $text) : self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get the message text.
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * Set the message type.
     * @param MessageType $type
     * @return $this
     */
    private function setType(MessageType $type) : self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the message type.
     * @return MessageType
     */
    public function getType() : MessageType
    {
        return $this->type;
    }

    /**
     * Set whether this message is suitable for display to the end user.
     * @param bool $displaytoUser
     * @return $this
     */
    private function setDisplayToUser(bool $displayToUser) : self
    {
        $this->displayToUser = $displayToUser;
        return $this;
    }
    
    /**
     * Get whether this message is suitable for display to the end user.
     * @return bool
     */
    private function getDisplayToUser() : bool
    {
        return $this->displayToUser;
    }
    
    /**
     * Get this instance as a string (retrieving the message text).
     * @return string
     */
    public function __toString() : string
    {
        return $this->getText();
    }

    /**
     * Get a data structure suitable for representing this instance as JSON.
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        return (object) [
            'type'          => $this->getType()->value,
            'text'          => $this->getText(),
            'displayToUser' => $this->getDisplayToUser(),
        ];
    }

}

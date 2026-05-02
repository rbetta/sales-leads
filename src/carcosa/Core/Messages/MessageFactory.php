<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

use Carcosa\Core\DiscreteSet;
use Carcosa\Core\Json\JsonDecoder;
use Carcosa\Core\Util\ListFormatter;
use Carcosa\Core\Messages\Types\Error;
use Carcosa\Core\Messages\Types\Info;
use Carcosa\Core\Messages\Types\Warning;

/**
 * A factory for creating Message instances.
 */
class MessageFactory
{

    /**
     * Create a message.
     * @param string $text The message text.
     * @param MessageType $type The type of message.
     * @param bool $displayToUser Whether this message is suitable for
     * display to the end user.
     * @return Message
     */
    public function create(
        string $text,
        MessageType $type,
        bool $displayToUser
    ) : Message
    {
        return new Message($text, $type, $displayToUser);
    }

    /**
     * Create an error message.
     * @param string $text The message text.
     * @param bool $displayToUser Whether this message is suitable for
     * display to the end user.
     * @return Message;
     */
    public function createError(string $text, bool $displayToUser) : Message
    {
        return new Message($text, MessageType::Error, $displayToUser);
    }

    /**
     * Create an informational message.
     * @param string $text The message text.
     * @param bool $displayToUser Whether this message is suitable for
     * display to the end user.
     * @return Message;
     */
    public function createInfo(string $text, bool $displayToUser) : Message
    {
        return new Message($text, MessageType::Info, $displayToUser);
    }
    
    /**
     * Create a debugging message.
     * @param string $text The message text.
     * @param bool $displayToUser Whether this message is suitable for
     * display to the end user.
     * @return Message;
     */
    public function createDebug(string $text, bool $displayToUser) : Message
    {
        return new Message($text, MessageType::Debug, $displayToUser);
    }

    /**
     * Create a warning message.
     * @param string $text The message text.
     * @param bool $displayToUser Whether this message is suitable for
     * display to the end user.
     * @return Message;
     */
    public function createWarning(string $text, bool $displayToUser) : Message
    {
        return new Message($text, MessageType::Warning, $displayToUser);
    }

    /**
     * Create a Message instance from its JSON-decoded representation.
     * @param \stdClass $data The JSON-decoded data that represent
     * an individual message.
     * @return Message
     * @throws \JsonException If JSON decoding fails.
     * @throws \RuntimeException If the top-level JSON data is not an object.
     * @throws \RuntimeException If there are missing top-level attributes
     * in the supplied JSON.
     * @throws \RuntimeException If there are extra, unrecognized top-level
     * attributes in the supplied JSON.
     * @throws \RuntimeException If the message text in the supplied JSON is
     * not a string.
     * @throws \RuntimeException If the message type in the supplied JSON is
     * invalid or unrecognized.
     */
    public function createFromDecodedJson(\stdClass $data) : Message
    {
        // Validate that the data are encoded as an associative object.
        if (! is_object($data)) {
            $type = get_debug_type($data);
            throw new \RuntimeException(
                "The top-level JSON-encoded entity extracted by " .
                __METHOD__ . " is of the invalid type $type (expected: object)"
            );
        }

        // Validate the top-level JSON attributes.
        $attributes = array_keys(get_object_vars($data));
        $expected = new DiscreteSet(['type', 'text', 'displayToUser']);
        $listFormatter = \App::make(ListFormatter::class);
        if ($missing = $expected->getMissingValues($attributes)) {

            // At least one attribute was missing.
            $list = $listFormatter->format($missing);
            throw new \RuntimeException(
                "The following top-level JSON attributes are missing " .
                "from the data supplied to " . __METHOD__ . ": $list"
            );

        }
        if ($extra = $expected->getExtraValues($attributes)) {

            // There are extra, unrecognized values.
            $list = $listFormatter->format($extra);
            throw new \RuntimeException(
                "The extra following top-level JSON attributes are present " .
                "in the data supplied to " . __METHOD__ . ": $list"
            );

        }

        // Validate that the message text is a string.
        $text = $data->text;
        if (! is_string($text)) {
            $type = get_debug_type($text);
            throw new \RuntimeException(
                "The message text decoded by " . __METHOD__ . " was of the " .
                "invalid type $type (expected: string)"
            );
        }
        
        // Validate that the "display to user" setting is a Boolean value.
        $displayToUser = $data->displayToUser;
        if (! is_bool($displayToUser)) {
            $type = get_debug_type($displayToUser);
            throw new \RuntimeException(
                "The \"display to user\" setting decoded by " .
                __METHOD__ . " was of the invalid type $type (expected: Boolean)"
            );
        }

        // Create the message type.
        $typeValue = $data->type;
        $messageTypeFactory = \App::make(MessageTypeFactory::class);
        $type = $messageTypeFactory->createFromValue($typeValue);
        
        // Create the message.
        return $this->create($text, $type, $displayToUser);

    }

}

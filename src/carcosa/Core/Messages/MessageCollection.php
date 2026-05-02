<?php
declare(strict_types = 1);
namespace Carcosa\Core\Messages;

use Carcosa\Core\Util\ArrayTransformer;
use Carcosa\Core\Messages\Message;
use Illuminate\Support\MessageBag;

/**
 * A class that contains messages (e.g. warnings, errors, or informational
 * notes).
 */
class MessageCollection implements \JsonSerializable, \Countable
{
    /**
     * An array whose keys are field names, and whose values are
     * arrays of Message instances.
     * @var array
     */
    private array $messages = [];

    /**
     * Get a count of all Message instances in this collection.
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ($this->messages as $messagesForField) {
            $count += count($messagesForField);
        }
        return $count;
    }
    
    /**
     * Get all fields that messages in this collection are associated with.
     * @return string[]
     */
    public function getFields() : array
    {
        return array_keys($this->messages);
    }

    /**
     * Add a message to this collection.
     * @param Message $message
     * @param string $field The field name this message is associated with.
     * An empty string is semantically equivalent to "no specific field."
     * @return $this
     */
    public function add(Message $message, string $field = '') : self
    {
        if (! array_key_exists($field, $this->messages)) {
            $this->messages[$field] = [];
        }
        $this->messages[$field][] = $message;
        return $this;
    }

    /**
     * Add messages from another MessageCollection instance into this instance.
     * @param MessageCollection $messages
     * @return $this
     */
    public function merge(MessageCollection $messages)
    {
        foreach ($messages->toArray() as $field => $messagesForField) {
            foreach ($messagesForField as $messageForField) {
                $this->add($messageForField, $field);
            }
        }
        return $this;
    }
    
    /**
     * Add messages from a Validator instance into this instance
     * (typically error messages, though this is configurable).
     * @param Validator $validator
     * @param bool $displayToUser Whether the Validator instance's
     * error messages should be displayable to the end user.
     * @param MessageType $messageType The type of message to add.
     * If unspecified, this defaults to error messages.
     * @return $this
     */
    public function mergeFromValidator(
        Validator $validator,
        bool $displayToUser,
        MessageType $type = MessageType::Error
    ) : self
    {
        
        // Add every validation error message from the Validator into this
        // collection as a new Message instance of the "error" type.
        $factory    = \App::make(MessageFactory::class);
        $messages   = $factory->createFromValidator($validator, $displayToUser, $type);
        $this->merge($messages);
        
        return $this;
    }
    
    /**
     * Return a copy of this instance containing only the messages that
     * match the given filtration function.
     * @param \Closure $filter A closure that accepts a string field name
     * and a Message instance. It must return true if the message should be
     * retained, or false if it should be discarded.
     * @return MessageCollection
     * @throws \RuntimeException If the supplied filtration closure returns
     * a value that is not strictly a Boolean type.
     */
    protected function filterByClosure(\Closure $filter) : MessageCollection
    {
        // Create a new, empty instance of this class.
        $messageCollectionFactory = \App::make(MessageCollectionFactory::class);
        $new = $messageCollectionFactory->create();
        
        // Iterate through all fields in this instance.
        $messagesByField = $this->toArray();
        foreach ($messagesByField as $field => $messages) {
            
            // Obtain all messages associated with the current field.
            foreach ($messages as $message) {
                
                // Determine if this message should be retained in the copy.
                $retain = $filter($field, $message);
                
                if (true === $retain) {
                    
                    // This message passed the fitler. Keep it in the copy.
                    $new->add($message, $field);
                    
                } elseif (false !== $retain) {
                    
                    // The filter function returned an unexpected data type.
                    $type = get_debug_type($retain);
                    throw new \RuntimeException(
                        "An invalid value of type $type was returned by " .
                        "the closure supplied to " . __METHOD__ . " " .
                        "(expected: Boolean)"
                    );
                    
                }
                
            }
            
        }
        
        return $new;
    }
    
    /**
     * Get a copy of this instance containing only the messages associated
     * with the given fields. If a nonempty string delimiter is specified,
     * then fields are considered to match it their field names match exactly
     * up until the specified delimiter (for example, if the delimiter "."
     * is supplied, then the fields "level1.level2" and "level1.level2.level3"
     * will both match the desired field "level1.level2" (but would not match
     * the desired field "level1.lev" since the delimited field names "lev"
     * and "level2" are not identical).
     * @param string[] $fields The names of the desired fields.
     * @param string $delimiter The delimiter to use when matching field
     * names. An empty string means only a full, exact match is allowed.
     * @return MessageCollection
     */
    public function filterByFields(array $fields, string $delimiter = '') : MessageCollection
    {
        if ('' === $delimiter) {
            
            // Only an exact match on field name is allowed.
            return $this->filterByClosure(
                function (string $field, Message $message) use ($fields) {
                    return in_array($field, $fields, true);
                }
            );
            
        } else {
            
            // A left-anchored match of delimited field names is allowed.
            return $this->filterByClosure(
                function (string $field, Message $message) use ($fields, $delimiter) {
                    
                    foreach ($fields as $desiredField) {
                        if (
                            $field === $desiredField ||
                            str_starts_with($field, $desiredField . $delimiter)
                        ) {
                            return true;
                        }
                    }
                    return false;
                    
                }
            );
            
        }
    }
    
    /**
     * Get a copy of this instance containing only the messages associated
     * with the given field. If a nonempty string delimiter is specified,
     * then fields are considered to match it their field names match exactly
     * up until the specified delimiter (for example, if the delimiter "."
     * is supplied, then the fields "level1.level2" and "level1.level2.level3"
     * will both match the desired field "level1.level2" (but would not match
     * the desired field "level1.lev" since the delimited field names "lev"
     * and "level2" are not identical).
     * @param string $field The name of the desired field.
     * @param string $delimiter The delimiter to use when matching field
     * names. An empty string means only a full, exact match is allowed.
     * @return MessageCollection
     */
    public function filterByField(string $field, string $delimiter = '') : MessageCollection
    {
        return $this->filterByFields([$field], $delimiter);
    }
    
    /**
     * Get a copy of this instance containing only the messages of the
     * given types.
     * MessageType[] $types
     * @return MessageCollection
     */
    public function filterByTypes(array $types) : MessageCollection
    {
        
        // Validate that the supplied array contains only MessageType
        // enumeration values.
        foreach ($types as $type) {
            if (! $type instanceof MessageType) {
                $type = get_debug_type($type);
                throw new \RuntimeException(
                    "An invalid value of type $type was present in the " .
                    "array supplied to " . __METHOD__ . " (expected: " .
                    MessageType::class . ")"
                );
            }
        }
        
        return $this->filterByClosure(
            function (string $field, Message $message) use ($types) {
                $type = $message->getType();
                return in_array($type, $types, true);
            }
        );
    }
    
    /**
     * Get a copy of this instance containing only the messages of the
     * given type.
     * MessageType $type
     * @return MessageCollection
     */
    public function filterByType(MessageType $type) : MessageCollection
    {
        return $this->filterByTypes([$type]);;
    }
    
    /**
     * Get a copy of this instance containing only the messages that are
     * displayable to the end user.
     * @return MessageCollection
     */
    public function filterByDisplayableToUser() : MessageCollection
    {
        return $this->filterByClosure(
            function (string $field, Message $message) {
                return ($message->getDisplayToUser());
            }
        );
    }

    /**
     * Get a copy of this instance with all associated field names modified
     * to strip a delimited prefix from them. Note that this may cause some
     * messages for different fields to be merged.
     * @param string $prefix The prefix to strip.
     * @param string $delimiter The delimiter between prefix levels.
     * @return MessageCollection
     */
    public function stripPrefixFromFields(string $prefix, string $delimiter) : MessageCollection
    {
        // Create a new, empty instance of this class.
        $messageCollectionFactory = \App::make(MessageCollectionFactory::class);
        $new = $messageCollectionFactory->create();
        
        // Iterate through all fields in this instance.
        $messagesByField = $this->toArray();
        foreach ($messagesByField as $field => $messages) {
            
            // Strip the prefix from the field, if it is present. Note that
            // this may result in an empty field name, if the field name is
            // equal to the prefix and nothing more.
            if ($prefix === $field) {
                $newField = '';
            } elseif (str_starts_with($field, $prefix . $delimiter)) {
                $newField = substr($field, strlen($prefix . $delimiter));
            } else {
                // This field name does not start with the prefix we are
                // stripping.
                $newField = $field;
            }
            
            // Obtain all messages associated with the current field.
            foreach ($messages as $message) {
                
                // Add this message to the new message collection copy,
                // using the new, prefixed field name.
                $new->add($message, $newField);
                
            }
            
        }
        
        return $new;
    }
    
    /**
     * Get a copy of this instance with all nonempty string field names
     * prefixed with the given string prefix and delimiter. 
     * @param string $prefix
     * @param string $delimiter
     * @return MessageCollection
     */
    public function withPrefixForNonempyFields(string $prefix, string $delimiter) : MessageCollection
    {
        // Create a new, empty instance of this class.
        $messageCollectionFactory = \App::make(MessageCollectionFactory::class);
        $new = $messageCollectionFactory->create();
        
        // Iterate through all fields in this instance.
        $messagesByField = $this->toArray();
        foreach ($messagesByField as $field => $messages) {
            
            // Prefix the field name, if it is not empty.
            $newField = ("" !== $field) ? ($prefix . $delimiter . $field) : $field;
            
            // Obtain all messages for the current field.
            foreach ($messages as $message) {
                
                // Add this message to the new message collection copy,
                // using the new, prefixed field name.
                $new->add($message, $newField);
                
            }
            
        }
        
        return $new;
    }
    
    /**
     * Get a copy of this instance with all string field names prefixed
     * with the given string prefix and delimiter. Empty field names
     * will be changed to just the prefix (omitting the delimiter, since
     * there is nothing to delimit).
     * @param string $prefix
     * @param string $delimiter
     * @return MessageCollection
     */
    public function withPrefixForFields(string $prefix, string $delimiter) : MessageCollection
    {
        // Create a new, empty instance of this class.
        $messageCollectionFactory = \App::make(MessageCollectionFactory::class);
        $new = $messageCollectionFactory->create();
        
        // Iterate through all fields in this instance.
        $messagesByField = $this->toArray();
        foreach ($messagesByField as $field => $messages) {
            
            // Determine the new, prefixed field name.
            if ("" === $field) {
                $newField = $prefix;
            } else {
                $newField = $prefix . $delimiter . $field;
            }
            
            // Obtain all messages associated with the current field.
            foreach ($messages as $message) {
                
                // Add this message to the new message collection copy,
                // using the new, prefixed field name.
                $new->add($message, $newField);
                
            }
            
        }
        
        return $new;
    }

    /**
     * Output the collection to an array whose keys are field names, and
     * whose values are arrays of Message instances belonging to those fields.
     * @return array
     */
    public function toArray() : array
    {
        return $this->messages;
    }
    
    /**
     * Output the collection to a nested array whose keys are field names
     * delimited into multiple levels by a specified delimiter, and whose
     * values are arrays of Message instances belonging to those nested
     * fields.
     * @param string $delimiter The delimiter to split array levels by.
     * @return array A nested array, whose values are arrays of Message
     * instances.
     */
    public function toNestedArray(string $delimiter) : array
    {
        $transformer = \App::make(FlatArray::class);
        $transformer->setArray($this->toArray());
        return $transformer->toNestedArray($delimiter);
    }

    /**
     * Output the collection to a MessageBag instance whose keys are field
     * names, and whose values are arrays of message texts.
     * @return MessageBag
     */
    public function toMessageBag() : MessageBag
    {
        $messageBag = \App::make(MessageBag::class);
        foreach ($this->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                $messageBag->add($field, $message->getText());
            }
        }
        return $messageBag;
    }

    /**
     * Clear all messages from this collection.
     * @return $this
     */
    public function clear() : self
    {
        $this->messages = [];
        return $this;
    }

    /**
     * Clear all messages belonging to a set of fields from this collection.
     * @param string[] $fields The field names to clear of all associated
     * messages.
     * @return $this
     */
    public function clearFields(array $fields) : self
    {
        foreach ($fields as $field) {
            $this->clearField($field);
        }
        return $this;
    }
    
    /**
     * Clear all messages belonging to a single field from this collection.
     * @param string $field The field name to clear of all associated
     * messages.
     * @return $this
     */
    public function clearField(string $field) : self
    {
        if (array_key_exists($field, $this->messages)) {
            unset($this->messages[$field]);
        }
        return $this;
    }

    /**
     * Get how many messages of a given type this instance contains.
     * @param MessageType $type
     * @return int
     */
    protected function getCountByType(MessageType $type) : int
    {
        return count($this->filterByType($type));
    }

    /**
     * Get whether this collection contains error messages.
     * @return bool
     */
    public function getHasError() : bool
    {
        return ($this->getCountByType(MessageType::Error) > 0);
    }

    /**
     * Get whether this collection contains debug messages.
     * @return bool
     */
    public function getHasDebug() : bool
    {
        return ($this->getCountByType(MessageType::Debug) > 0);
    }
    
    /**
     * Get whether this collection contains info messages.
     * @return bool
     */
    public function getHasInfo() : bool
    {
        return ($this->getCountByType(MessageType::Info) > 0);
    }
    
    /**
     * Get whether this collection contains error messages.
     * @return bool
     */
    public function getHasWarning() : bool
    {
        return ($this->getCountByType(MessageType::Warning) > 0);
    }
    
    /**
     * Return a data structure that represents this instance in a manner
     * suitable for JSON encoding.
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        return (object) $this->toArray();
    }

}

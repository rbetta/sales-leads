<?php
declare(strict_types = 1);

namespace Carcosa\Core\Service;

use Carcosa\Core\Messages\iHasMessages;
use Carcosa\Core\Messages\MessageCollectionFactory;
use Carcosa\Core\Messages\MessageType;
use Carcosa\Core\Messages\tHasMessages;
use Illuminate\Validation\Validator;


/**
 * A class that represents a result from an operation performed by a service.
 * @author Randall Betta
 * @todo The message collection managed by this instance is still mutable
 * (messages can be added to it).
 */
class ServiceResult implements iServiceResult, iHasMessages
{
    
    use tHasMessages;
    
    /**
     * An array of data to send to the service.
     * @var array An array whose keys are strings, and whose values are
     * arbitrary non-resource values.
     */
    private array $data = [];
    
    /**
     * Validate a data value.
     * @param mixed $value
     * @return $this
     * @throws \RuntimeException If any supplied value is a resource.
     */
    private function validateValue($value) : self
    {
        
        if (is_resource($value)) {
            
            // Resources are not permitted.
            $type = get_debug_type($value);
            throw new \RuntimeException(
                "An invalid value of type $type was found by " .
                __METHOD__ . " (expected: non-resource)."
            );
            
        }
        
        return $this;
    }
    
    
    /**
     * Set a value.
     * @param string $name The data name.
     * @param mixed $value The data value.
     * @throws \RuntimeException If an empty string data name is supplied.
     * @throws \RuntimeException If any supplied value is a resource.
     * @return $this 
     */
    public function setValue(string $name, $value) : self
    {
        
        // Sanity check on name.
        if ('' === $name) {
            throw new \RuntimeException(
                "An empty string data name was supplied to " . __METHOD__
            );
        }
        
        $this->validateValue($value);
        $this->data[$name] = $value;
        return $this;
        
    }
    
    /**
     * Set multiple values.
     * @param array An array whose keys are data names, and whose values
     * are the data's corresponding non-resource values.
     * @throws \RuntimeException If an empty string data name is supplied.
     * @throws \RuntimeException If any supplied value is a resource.
     * @return $this
     */
    public function setValues(array $values) : self
    {
        foreach ($values as $name => $value) {
            $this->setValue($name, $value);
        }
        return $this;
    }

    /**
     * Get a value.
     * @param string $name The data name.
     * @return mixed Any non-resource value.
     * @throws \LogicException If a nonexistent data name is supplied.
     */
    public function getValue(string $name)
    {
        if (! $this->getHasValue($name)) {
            throw new \LogicException(
                "The nonexistent datum name \"$name\" was supplied to " .
                __METHOD__
            );
        }
        return $this->data[$name] ?? null;
    }
    
    /**
     * Get whether a value exists
     * @param string $name The data name.
     * @return bool
     */
    public function getHasValue(string $name) : bool
    {
        return array_key_exists($name, $this->data);
    }
    
    /**
     * Get all values.
     * @return array An array whose keys are value names as strings, and
     * whose values are any non-resource data types.
     */
    public function getValues() : array
    {
        return $this->data;
    }
    
    /**
     * Get whether this instance contains at least one error message.
     * @return bool
     */
    public function getHasError() : bool
    {
        return $this->getMessages()->getHasError();
    }
    
    /**
     * Get a copy of this instance's data as an associative array.
     * @return array An array whose keys are data names as strings,
     * and whose values are their corresponding non-resource data values.
     */
    public function toArray() : array
    {
        return $this->data;
    }

    /**
     * Convert this instance into a ServiceResultImmutable instance.
     * @return ServiceResultImmutable
     */
    public function toImmutable() : ServiceResultImmutable
    {
        return new ServiceResultImmutable($this);
    }
    
    /**
     * Add messages from a Validator instance into this instance.
     * These will be error messages by default.
     * @param Validator $validator
     * @param bool $displayToUser Whether the Validator instance's
     * error messages should be displayable to the end user.
     * @param MessageType $type The type of each message to
     * add from the supplied validator. If unspecified, this will
     * default to the error message type.
     * @return $this
     */
    public function addMessagesFromValidator(
        Validator $validator,
        bool $displayToUser,
        MessageType $type = MessageType::Error
    ) : self
    {
        
        // Convert the validator's error messages to a MessageCollection.
        $factory    = \App::make(MessageCollectionFactory::class);
        $messages   = $factory->createFromValidator($validator, $displayToUser, $type);
        
        // Merge the validator's resultant MessageCollection contents with
        // this instance's existing messages.
        $this->addMessages($messages);
        
        return $this;
    }
    
}

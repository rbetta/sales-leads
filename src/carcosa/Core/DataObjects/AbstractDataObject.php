<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * An abstract base class for all data objects.
 */
abstract class AbstractDataObject implements iDataObject
{
    
    /**
     * Property values belonging to this instance.
     * @var array An associative array whose keys are property names,
     * and whose values are their associated properties.
     */
    private array $properties = [];
    
    /**
     * Relationships with other AbstractDataObject subclass instances. 
     * @var DataObjectRelationship[]
     */
    private $relationships = [];
    
    /**
     * Get the type of this object.
     * 
     * This is used to determine how to decode this instance's
     * JSON output back into a class instance.
     * @return string
     */
    public function getType() : string
    {
        return get_class($this);
    }
    
    /**
     * Set a property value on the record represented by this instance.
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws \RuntimeException If an empty property name is supplied.
     * @throws \RuntimeException if a resource is supplied as the value.
     */
    public function setProperty(string $name, $value) : self
    {
        
        // Ensure the name is nonempty.
        if ("" === $name) {
            throw new \RuntimeException(
                "An empty string property name was supplied to " . __METHOD__
            );
        }
        
        // Ensure the value is not a resource.
        if (is_resource($value)) {
            $type = get_debug_type($value);
            throw new \RuntimeException(
                "An invalid value of type "
            );
        }
        
        // Set the property.
        $this->properties[$name] = $value;
        return $this;
        
    }
    
    /**
     * Set multiple property values on the record represented by this instance.
     * @param array $data An associative array whose keys are property names
     * as strings, and whose values are their corresponding values.
     * @return $this
     * @throws \RuntimeException if a resource is supplied as a value.
     * @throws \RuntimeException If an empty property name is supplied
     * for any value.
     */
    public function setProperties(array $data) : self
    {
        foreach ($data as $name => $value) {
            $this->setProperty($name, $value);
        }
        return $this;
    }
    
    /**
     * Get a property value on the record represented by this instance.
     * @param string $name
     * @return mixed
     * @throws \RuntimeException If a nonexistent property name is supplied.
     */
    public function getProperty(string $name)
    {
        
        // Ensure the property name exists.
        if (! array_key_exists($name, $this->properties)) {
            throw new \RuntimeException(
                "The nonexistent property name \"$name\" was supplied to " .
                __METHOD__
            );
        }
        
        // Retrieve the value.
        return $this->properties[$name];
        
    }
    
    /**
     * Get all property values on the record represented by this instance.
     * @return array An associative array whose keys are property names
     * as strings, and whose values are their corresponding values.
     */
    public function getProperties() : array
    {
        return $this->properties;
    }
    
    /**
     * Validate a relationship name.
     * @param string $name The name of a relationship between this
     * instance and other AbstractDataObject subclass instances.
     * @return $this
     * @throws \InvalidArgumentException If the specified name is
     * an empty string.
     */
    private function validateRelationshipName(string $name) : self
    {
        if ("" === $name) {
            throw new \InvalidArgumentException(
                "An empty relationship name was supplied to " . __METHOD__
            );
        }
        return $this;
    }
    
    /**
     * Define a relationship between this instance and another
     * AbstractDataObject subclass instance.
     * @param string $name The relationship name.
     * @param AbstractDataObjectRelationship The relationship to other
     * AbstractDataObject subclass instances.
     * @throws \InvalidArgumentException If the specified relationship
     * name is an empty string.
     * @throws \RuntimeException If a relationship with the same name
     * has already been defined on this instance.
     * @return $this
     */
    public function addRelationship(
        string $name,
        AbstractDataObjectRelationship $relationship,
    ) : self
    {
        
        // Validate the input arguments.
        if ($this->getHasRelationship($name)) {
            throw new \RuntimeException(
                "The relationship name \"$name\" supplied to " .
                __METHOD__ . " is already defined."
            );
        }
        
        // Define the relationship.
        $this->relationships[$name] = $relationship;
        
        return $this;
    }
    
    /**
     * Get whether a relationship is defined on this instance.
     * @param string $name The relationship name to check.
     * @return bool
     * @throws \InvalidArgumentException If the specified relationship
     * name is an empty string.
     */
    public function getHasRelationship(string $name) : bool
    {
        // Validate the name.
        $this->validateRelationshipName($name);
        
        // Determine if the relationship is defined.
        return array_key_exists($name, $this->getRelationships());
    }
    
    /**
     * Get one of the named relationships to other AbstractDataObject
     * subclass instances defined on this class.
     * @param string $name The relationship name.
     * @return AbstractDataObjectRelationship The relationship with the
     * given name.
     * @throws \InvalidArgumentException If an empty string relationship
     * name was supplied.
     * @throws \RuntimeException If no relationship with the supplied name
     * is defined on this instance.
     */
    public function getRelationship(string $name) : AbstractDataObjectRelationship
    {
        if ($this->getHasRelationship($name)) {
            return ($this->getRelationships())[$name];
        }
        else {
            throw new \RuntimeException(
                "The nonexistent relationship name \"$name\" was " .
                "supplied to " . __METHOD__
            );
        }
    }
    
    /**
     * Get all named relationships to other AbstractDataObject
     * subclass instances defined on this class.
     * @return AbstractDataObjectRelationship[] An array whose keys are the
     * relationship names, and whose values are their corresponding
     * AbstractDataObjectRelationship instances.
     */
    public function getRelationships() : array
    {
        return $this->relationships;
    }
    
    /**
     * Get a related data object or null, or an array of related data objects
     * (depending on the cardinality of the relationship).
     * @param string $name The relationship name.
     * @return AbstractDataObject|AbstractDataObject[]
     * @throws \InvalidArgumentException If an empty string relationship
     * name was supplied.
     * @throws \RuntimeException If no relationship with the supplied name
     * is defined on this instance.
     * @throws \RuntimeException If an internal error occurs (specifically
     * if this method encounters an unknown relationship type). This should
     * never happen.
     */
    public function getRelated(string $relationshipName) : AbstractDataObject|null|array
    {
        
        // Obtain the specified relationship by its name.
        $relationship = $this->getRelationship($relationshipName);
        
        // Return the related object or objects in the specified
        // reltionship (if any).
        if ($relationship instanceof DataObjectRelationshipManyToOne) {
            
            // Return exactly one instance, or null if there
            // is no related instance.
            return $relationship->getRelatedInstance();
            
        } elseif ($relationship instanceof DataObjectRelationshipOneToMany) {
            
            // Return an array of related instances. This array
            // will be empty if there are no related instances.
            return $relationship->getRelatedInstances();
            
        } else {
            
            // The relationship type is not recognized. This should
            // not happen, and indicates an internal error.
            get_debug_type($relationship);
            throw new \RuntimeException(
                "An unknown relationship of type $type was encountered by " .
                __METHOD__
            );
            
        }
        
    }

    /**
     * Convert this class into a representation suitable for serializing
     * to JSON.
     * @return mixed
     */
    public function jsonSerialize()
    {
        
        // Obtain a transformer to convert Carbon instances to and from
        // JSON-compatible values.
        $carbonAdapter = \App::make(CarbonJsonAdapter::class);
        
        // Perform any necessary property transformations for
        // conversion to JSON.
        $modifiedProperties = [];
        foreach ($this->getProperties() as $name => $value) {
            
            // Convert Carbon instances to ISO-8601 formatted strings.
            if ($value instanceof Carbon) {
                $value = $carbonAdapter->toJsonValue($value);
            }
            
            // Record this property, whether it was modified or not.
            $modifiedProperties[$name] = $value;
            
        }
        
        // Return this instance's JSON representation.
        return (object) [
            "type"          => $this->getType(),
            "properties"    => (object) $modifiedProperties,
            "relationships" => (object) $this->getRelationships(),
        ];
        
    }

}

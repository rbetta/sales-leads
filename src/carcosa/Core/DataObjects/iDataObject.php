<?php
declare(strict_types = 1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\DataObjects\AbstractDataObject;
use Illuminate\Contracts\Validation\Validator;

/**
 * An interface for data objects that are suitable for transmission as
 * part of an API call. Often, these will map one-to-one with Eloquent
 * ORM model classes.
 * @author Randall Betta
 *
 */
interface iDataObject extends \JsonSerializable
{
    
    /**
     * Get the type of this object.
     * 
     * This is used to determine how to decode this instance's
     * JSON output back into a class instance.
     * @return string
     */
    public function getType() : string;
    
    /**
     * Set a property value on the record represented by this instance.
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws \RuntimeException If an empty property name is supplied.
     * @throws \RuntimeException if a resource is supplied as the value.
     */
    public function setProperty(string $name, $value) : self;
    
    /**
     * Set multiple property values on the record represented by this instance.
     * @param array $data An associative array whose keys are property names
     * as strings, and whose values are their corresponding values.
     * @return $this
     * @throws \RuntimeException if a resource is supplied as a value.
     * @throws \RuntimeException If an empty property name is supplied
     * for any value.
     */
    public function setProperties(array $data) : self;
    
    /**
     * Get a property value on the record represented by this instance.
     * @param string $name
     * @return mixed
     * @throws \RuntimeException If a nonexistent property name is supplied.
     */
    public function getProperty(string $name);
    
    /**
     * Get all property values on the record represented by this instance.
     * @return array An associative array whose keys are property names
     * as strings, and whose values are their corresponding values.
     */
    public function getProperties() : array;

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
    ) : self;
    
    /**
     * Get whether a relationship is defined on this instance.
     * @param string $name The relationship name to check.
     * @return bool
     * @throws \InvalidArgumentException If the specified relationship
     * name is an empty string.
     */
    public function getHasRelationship(string $name) : bool;
    
    /**
     * Get one of the named relationships to other AbstractDataObject
     * subclass instances defined on this class.
     * @param string $name The relationship name.
     * @return AbstractDataObjectRelationship The relationship with the given name.
     * @throws \InvalidArgumentException If an empty string relationship
     * name was supplied.
     * @throws \RuntimeException If no relationship with the supplied name
     * is defined on this instance.
     */
    public function getRelationship(string $name) : AbstractDataObjectRelationship;
    
    /**
     * Get all named relationships to other AbstractDataObject
     * subclass instances defined on this class.
     * @return AbstractDataObjectRelationship[] An array whose keys are the
     * relationship names, and whose values are their corresponding
     * AbstractDataObjectRelationship instances.
     */
    public function getRelationships() : array;
    
}

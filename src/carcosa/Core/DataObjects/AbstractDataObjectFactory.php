<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\Messages\MessageCollectionFactory;
use Carcosa\Core\Util\ListFormatter;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * An abstract base class for all factory classes that can
 * create class instances that implement the iDataObject interface.
 */
abstract class AbstractDataObjectFactory implements iDataObjectFactory
{
    
    /**
     * Handle subclass-specific property assignment for an instance of
     * a class that implements the iDataObject interface, using the
     * decoded JSON that was used to instantiate it as a source for
     * these properties and their values.
     * @param \stdClass $properties The JSON-decoded properties.
     * @param iDataObject $instance The instance whose custom properties
     * will be assigned by this method.
     * @return $this
     */
    protected abstract function assignCustomProperties(\stdClass $properties, iDataObject $instance) : self;
    
    /**
     * Validate a JSON representation of a class that implements
     * the iDataObject interface.
     * @param \stdClass $jsonData The decoded JSON data to validate.
     * @return $this
     * @throws \RuntimeException If the JSON data are malformed.
     */
    private function validateDecodedJson(\stdClass $jsonData) : self
    {
        
        // Create a validator for the decoded JSON data.
        $validator = ValidatorFacade::make(
            (array) $jsonData,
            [
                "type"          => "required|string",
                "properties"    => "required|array",
            ],
            []
        );
        
        // Validate the supplied decoded JSON data.
        if ($validator->fails()) {
            
            // The data are invalid. Obtain a MessageCollection containing
            // all validation errors.
            $messageCollectionFactory = \App::make(MessageCollectionFactory::class);
            $messages = $messageCollectionFactory->createFromValidator($validator, true);
            
            // Format the list of error messages.
            $formatter = (\App::create(ListFormatter::class))
                ->setDelimiter(', ')
                ->setEnclosureStart('{')
                ->setEnclosureEnd('}');
            $errorsArray = [];
            foreach ($messages->toArray() as $field => $errors) {
                foreach ($errors as $error) {
                    $errorsArray[] = "$field: $error";
                }
            }
            $errorsText = $formatter->format($errorsArray);
            
            // Throw the descriptive exception.
            throw new \RuntimeException(
                "The following errors occurred while validating " .
                "the decoded JSON data supplied to " .
                __METHOD__ . ": $errorsText"
            );
            
        }
        
        return $this;
        
    }
    
    /**
     * Create an instance of a class that implements the iDataObject interface
     * from its validated, JSON-decoded data representation.
     * @param \stdObject $jsonData The validated, decoded JSON representation
     * of a concrete iDataObject subclass.
     * @return iDataObject
     * @throws \RuntimeException If the supplied decoded JSON data are
     * malformed.
     */
    public function createFromDecodedJson(\stdClass $jsonData) : iDataObject
    {
        
        // Validate the supplied decoded JSON.
        $this->validateDecodedJson($jsonData);
        
        // Obtain all JSON-encoded properties.
        $properties = $jsonData->properties;
        
        // Obtain all relationships to other iDataObject-implementing classes.
        $relationships = $jsonData->relationships;
        
        // Create a new class instance that implements the iDataObject
        // interface. This will be populated from the decoded JSON data.
        $instance = $this->create();
        
        // Define all relationships.
        $relationshipFactory = \App::make(DataObjectRelationshipFactory::class);
        foreach ($relationships as $relationshipJsonData) {
            
            $relationship = $relationshipFactory->createFromJson($relationshipJsonData);
            $this->addRelationship($relationship);
            
        }
        
        // Assign all standardized properties.
        $carbonAdapter = \App::make(CarbonJsonAdapter::class);
        if ($instance instanceof iUuid) {
            $instance->setId($properties->id);
        }
        if ($instance instanceof iTimestampedUuid) {
            $instance->setCreatedAt($carbonAdapter->fromJsonValue($properties->createdAt));
            $instance->setUpdatedAt($carbonAdapter->fromJsonValue($properties->updatedAt));
        }
        if ($instance instanceof iSoftDeletableTimestampedUuid) {
            $instance->setDeletedAt($carbonAdapter->fromJsonValue($properties->deletedAt));
        }
        
        // Assign all properties that are custom to the
        // iDataObject-implementing object we are instantiating.
        $this->assignCustomProperties($properties, $instance);
        
        // Return the fully populated instance.
        return $instance;
        
    }

}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\Exceptions\NotImplementedException;
use Carcosa\Core\Messages\MessageCollectionFactory;
use Carcosa\Core\Util\ListFormatter;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * A factory class for creating DataObjectRelationship instances.
 */
class DataObjectRelationshipFactory
{
    
    /**
     * Validate a JSON representation of a DataObjectRelationship instance.
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
                "allowsMultiple"            => "required|boolean",
                "areRelatedInstancesLoaded" => "required|boolean",
                "relatedInstances"          => "required_if:allowsMultiple,1|array",
                "relatedInstances.*"        => "array",
                "relatedInstance"           => "required_if:allowsMultiple,0|string|nullable",
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
     * Create a DataObjectRelationship instance.
     * @param bool $allowsMultiple Whether multiple related instances are
     * allowed in this relationship.
     * @return AbstractDataObjectRelationship
     */
    public function create(bool $allowsMultiple) {
        if ($allowsMultiple) {
            return \App::make(DataObjectRelationshipOneToMany::class);
        } else {
            return \App::make(DataObjectRelationshipManyToOne::class);
        }
    }
    
    /**
     * Create a DataObjectRelationship instance from its validated, JSON-decoded
     * data representation.
     * @param \stdObject $jsonData The validated, decoded JSON representation
     * of a concrete AbstractDataObject subclass.
     * @return DataObjectRelationship
     * @throws \RuntimeException If the supplied decoded JSON data are
     * malformed.
     */
    public function createFromDecodedJson(\stdClass $jsonData) : AbstractDataObject
    {
        
        // Validate the supplied decoded JSON.
        $this->validateDecodedJson($jsonData);
        
        // Create the new instance.
        $allowsMultiple     = $jsonData->allowsMultiple;
        $relationship       = $this->create($allowsMultiple);
        
        // If related instances are loaded, then record them.
        if ($jsonData->areRelatedInstancesLoaded) {
            
            // Create a factory class for creating the correct factory class
            // for each related instance.
            $factoryFactory = \App::make(DataObjectFactoryFactory::class);
            
            // Instantiate all related instances from their decoded JSON
            // data, and associate them to this relationship.
            if ($allowsMultiple) {
                
                // Decode and instantiate any number of related instances.
                $relatedInstances = [];
                foreach ($jsonData->relatedInstances as $relatedJsonData) {
                    $factory = $factoryFactory->createFromDecodedJson($relatedJsonData);
                    $relatedInstances[] = $factory->createFromDecodedJson($relatedJsonData);
                }
                
                // Record the associated instances.
                $relationship->addRelatedInstances($jsonData->relatedInstances);
                
            } else {
                
                // Decode and instantiate up to a single related instance.
                $relatedInstance    = null;
                $relatedJsonData    = $jsonData->relatedInstance;
                if (null !== $relatedJsonData) {
                    $factory = $factoryFactory->createFromDecodedJson($relatedJsonData);
                    $relatedInstance = $factory->createFromDecodedJson($relatedJsonData);
                }
                
                // Record the associated instance (or null, if there is none).
                $relationship->setRelatedInstance($relatedInstance);
                
            }
            
        }
        
        return $relationship;

    }
    
}

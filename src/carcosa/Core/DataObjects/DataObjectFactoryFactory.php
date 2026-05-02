<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carcosa\Core\Messages\MessageCollectionFactory;
use Carcosa\Core\Util\ListFormatter;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * A class that creates AbstractDataObjectFactory subclass instances.
 */
class DataObjectFactoryFactory
{
    
    /**
     * Get a factory for the JSON representation of an
     * AbstractDataObject subclass.
     * @param \stdClass $jsonData The decoded JSON data
     * that represent an AbstractDataObject subclass.
     * @return AbstractDataObjectFactory The correct concrete
     * subclass of AbstractDataObjectFactory for the given JSON.
     * @throws \RuntimeException If the decoded JSON data are malformed.
     * @throws \RuntimeException If the decoded JSON data does not
     * represent a valid AbstractDataObject subclass.
     * @throws \RuntimeException If the correct factory class for
     * the decoded JSON data cannot be identified and instantiated.  
     */
    private function getDataObjectFactoryForJsonData(\stdClass $jsonData) : AbstractDataObjectFactory
    {
        
        // Perform basic validation of the supplied data.
        $this->validateDecodedJson($decodedJson);
        
        // Determine what class the JSON-decoded data represent.
        $className = $jsonData->type;
        
        // Validate the class that the JSON-decoded data represent.
        if (! ($className instanceof iDataObject)) {
            $expectedInterface = iDataObject::class;
            throw new \RuntimeException(
                "The data object type \"$className\" retrieved by " .
                __METHOD__ . " is not a valid class name (expected: " .
                "implementation of $expectedInterface interface)."
            );
        }
        
        // Instantiate the corresponding factory class.
        $factoryClassName = $className . "Factory";
        if (!( $factoryClassName instanceof AbstractDataObjectFactory)) {
            $expectedClass = AbstractDataObjectFactory::class;
            throw new \RuntimeException(
                "The factory class for the data object type $className " .
                "retrieved by " . __METHOD__ . " could not be " .
                "instantiated (expected: $expectedClass subclass)."
            );
        }
        
        // Return the correct factory class for the decoded JSON data.
        return \App::make($factoryClassName);
        
    }
    
    /**
     * Validate the minimum necessary subset of the JSON representation
     * of an AbstractDataObject subclass instance.
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
                "type" => "required|string|min:1",
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
    
}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Organization;

use Carcosa\Core\DataObjects\AbstractDataObjectFactory;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Carcosa\Core\Json\DecodedJsonTransformer;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


/**
 * A factory for creating OrganizationData instances.
 */
class OrganizationDataFactory extends AbstractDataObjectFactory
{
    
    /**
     * Create a new, empty instance of the class this factory handles.
     * @return OrganizationData
     */
    public function create() : OrganizationData
    {
        return \App::make(OrganizationData::class);
    }
    
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
    protected function assignCustomProperties(\stdClass $properties, iDataObject $instance) : self
    {
        $instance
            ->setLabel(     $properties->label      )
            ->setClientId(  $properties->clientId   )
            ;
    }
    
}

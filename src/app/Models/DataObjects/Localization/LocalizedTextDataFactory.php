<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Localization;

use Carcosa\Core\DataObjects\AbstractDataObjectFactory;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\Json\DecodedJsonTransformer;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


/**
 * A factory for creating LocalizedTextData instances.
 */
class LocalizedTextDataFactory extends AbstractDataObjectFactory
{
    
    /**
     * Create a new, empty instance of the class this factory handles.
     * @return LocalizedTextData
     */
    public function create() : LocalizedTextData
    {
        return \App::make(LocalizedTextData::class);
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
        ;   // NO-OP.
        return $this;
    }

}

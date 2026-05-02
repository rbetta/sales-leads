<?php
declare(strict_types=1);
namespace App\Models\DataObjects\User;

use Carcosa\Core\DataObjects\AbstractDataObjectFactory;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\I18n\LocaleFactory;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Carcosa\Core\Json\Adapters\LocaleJsonAdapter;
use Carcosa\Core\Json\Adapters\TimezoneJsonAdapter;
use Carcosa\Core\Json\DecodedJsonTransformer;

/**
 * A factory for creating UserData instances.
 */
class UserDataFactory extends AbstractDataObjectFactory
{

    /**
     * Create a new, empty instance of the class this factory handles.
     * @return UserData
     */
    public function create() : UserData
    {
        return \App::make(UserData::class);
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
        
        $carbonAdapter      = \App::make(CarbonJsonAdapter::class);
        $localeAdapter      = \App::make(LocaleJsonAdapter::class);
        $timezoneAdapter    = \App::make(TimezoneJsonAdapter::class);
        $instance
            ->setOrganizationId(    $properties->organizationId )
            ->setUsername(          $properties->username       )
            ->setEmail(             $properties->email          )
            ->setLabel(             $properties->label          )
            ->setIsSeller(          $properties->isSeller       )
            ->setIsCustomer(        $properties->isCustomer     )
            ->setIsSystemAdmin(     $properties->isSystemAdmin  )
            ->setIsActive(          $properties->isActive       )
            ->setDeactivatedAt(     $carbonAdapter->fromJsonValue($properties->deactivatedAt)   )
            ->setLocale(            $localeAdapter->fromJsonValue($properties->locale)          )
            ->setTimezone(          $timezoneAdapter->fromJsonValue($properties->timezone)      )
            ;
    }

}

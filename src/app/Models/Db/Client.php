<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Models\DataObjects\Client\ClientDataFactory;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends TimestampedSoftDeletableUuidModel
{
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'client';
    
    
    /**
     * Get the attributes that should be cast.
     *
     * @return string[] An array whose keys are field names, and whose
     * values are casting instructions for the Eloquent ORM framework.
     */
    protected function casts() : array
    {
        return array_merge(
            parent::casts(),
            [
                'is_test'       => 'boolean',
                'is_internal'   => 'boolean',
            ]
        );
    }
    
    /**
     * Get an instance of the factory class used to create this
     * model instance's associated data object (which must
     * implement the iDataObject interface).
     * @return ClientDataFactory
     */
    protected function getDataObjectFactory() : ClientDataFactory
    {
        return \App::make(ClientDataFactory::class);
    }
    
    /**
     * Assign custom properties to the data object created using the
     * toDataObject() method.
     * @param iDataObject $dataObject
     * @return self
     */
    protected function assignDataObjectProperties(iDataObject $dataObject) : self
    {
        
        // Instantiate a locale object for this user record.
        $localeFactory  = \App::make(LocaleFactory::class);
        $locale         = $localeFactory->create($this->locale);
        
        $dataObject
            ->setLabel(             $this->label            )
            ->setIsTest(            $this->is_test          )
            ->setIsInternal(        $this->is_internal      )
            ->setIsActive(          $this->is_active        )
            ->setDeactivatedAt(     $this->deactivated_at   )
            ;
        
        return $this;
        
    }
    
    /**
     * Get the relationship to the organizations belonging to this client.
     * @return HasMany
     */
    public function organizations() : HasMany
    {
        return $this->hasMany(Organization::class, 'organization_id');
    }
    
    /**
     * Get the relationship to the users belonging to this client.
     * @return HasMany
     */
    public function users() : HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
    
    /**
     * Get the relationship to the entity definitions belonging to this client.
     * @return HasMany
     */
    public function entityDefs() : HasMany
    {
        return $this->hasMany(EntityDef::class, 'client_id');
    }
    
}

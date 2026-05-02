<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Models\DataObjects\User\UserDataFactory;
use App\Services\Application\ApplicationCriteria;
use App\Services\Application\ApplicationService;
use App\Services\Client\ClientService;
use App\Services\Group\GroupCriteria;
use App\Services\Group\GroupService;
use App\Services\Organization\OrganizationCriteria;
use App\Services\Organization\OrganizationService;
use Carbon\CarbonImmutable;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\DataObjects\iToDataObject;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Carcosa\Core\Db\tToDataObject;
use Carcosa\Core\I18n\iLocale;
use Carcosa\Core\I18n\LocaleFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends TimestampedSoftDeletableUuidModel implements iToDataObject
{
    
    use tToDataObject;
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'user';
    
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
                'is_customer'       => 'bool',
                'is_seller'         => 'bool',
                'is_system_admin'   => 'bool',
                'is_active'         => 'bool',
                'deactivated_at'    => 'datetime',
            ]
        );
    }
    
    /**
     * Get an instance of the factory class used to create this
     * model instance's associated data object (which must
     * implement the iDataObject interface).
     * @return UserDataFactory
     */
    protected function getDataObjectFactory() : UserDataFactory
    {
        return \App::make(UserDataFactory::class);
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
            ->setOrganizationId(    $this->organization_id  )
            ->setUsername(          $this->username         )
            ->setEmail(             $this->email            )
            ->setLabel(             $this->label            )
            ->setLocale(            $locale                 )
            ->setIsActive(          $this->is_active        )
            ->setDeactivatedAt(     $this->deactivated_at   )
            ;
        
        return $this;
        
    }
    
    /**
     * Get the client this user belongs to.
     * @return BelongsTo
     */
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    /**
     * Get the user's locale as an object that implements the iLocale interface.
     * @return iLocale
     * @throws \InvalidArgumentException If the locale is not understood.
     */
    public function getLocale() : iLocale
    {
        $localeFactory = \App::make(LocaleFactory::class);
        return $localeFactory->create($this->locale);
    }
    
    /**
     * Get the associated Client instance.
     * @return Client
     */
    public function getClient() : Client
    {
        
        // Get the client ID from this model instance.
        $clientId = $this->client_id;
            
        // Retrieve the Client instance by its ID.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $clientService = \App::make(ClientService::class);
        return $clientService->findOneById($clientId);
        
    }
    
    /**
     * Get the organizations associated with this user.
     * @return Organization[]
     */
    public function getOrganizations() : array
    {
        // Set up criteria for finding organizations belonging to
        // this user's associated client.
        $criteria = \App::make(OrganizationCriteria::class);
        $criteria->setClient($this->getClient());
        
        // Return the related organizations.
        $orgService = \App::make(OrganizationService::class);
        return $orgService->find($criteria);
    }
    
    /**
     * Handle post-booting logic for instances of this model.
     */
    protected static function booted()
    {
        
        // Hook into the event that fires just before saving
        // an instance to the database.
        static::saving(function (User $user) {
            
            // If the record is being deactivated, then set its
            // deactivation timestamp to the current UTC time.
            if (
                    $user->exists
                &&  $user->isDirty('is_active')
                &&  ! $user->is_active
            ) {
                $user->deactivated_at = CarbonImmutable::now('UTC');
            }
            
            // If the record is being activated, then set its
            // deactivation timestamp to null.
            if ($user->is_active) {
               $user->deactivated_at = null;
            }
            
        });
    }
    
    /**
     * Get the associated Group instances.
     * @return Group[]
     */
    public function getGroups() : array
    {
        
        // Retrieve this instance's associated groups.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $criteria   = \App::make(GroupCriteria::class);
        $service    = \App::make(GroupService::class);
        $criteria->setUser($this);
        return $service->find($criteria);
        
    }
    
    /**
     * Get the associated Application instances.
     * @return Application[]
     */
    public function getApplications() : array
    {
        
        // Retrieve this instance's associated applications.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $criteria   = \App::make(ApplicationCriteria::class);
        $service    = \App::make(ApplicationService::class);
        $criteria->setUser($this);
        return $service->find($criteria);
        
    }
    
}

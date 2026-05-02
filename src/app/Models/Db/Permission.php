<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Services\Application\ApplicationService;
use App\Services\Client\ClientService;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permission extends TimestampedSoftDeletableUuidModel
{
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'permission';
    
    
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
                'is_custom' => 'boolean',
            ]
        );
    }
    
    /**
     * Get the relationship to the client this record belongs to (if any).
     * @return BelongsTo
     */
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    /**
     * Get the relationship to the application this record belongs to (if any).
     * @return BelongsTo
     */
    public function application() : BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
    
    /**
     * Get the associated Client instance (if any).
     * @return Client
     */
    public function getClient() : Client|null
    {
        
        // Get the client ID from this model instance.
        $clientId = $this->client_id;
        
        // Handle no associated record.
        if (null === $clientId) {
            return null;
        }
        
        // Retrieve the Client instance by its ID.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $clientService = \App::make(ClientService::class);
        return $clientService->findOneById($clientId);
        
    }
    
    /**
     * Get the associated Application instance (if any).
     * @return Application|null
     */
    public function getApplication() : Application|null
    {
        
        // Get the application ID from this model instance.
        $applicationId = $this->application_id;
        
        // Handle no associated record.
        if (null === $applicationId) {
            return null;
        }
            
        // Retrieve the Application instance by its ID.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $applicationService = \App::make(ApplicationService::class);
        return $applicationService->findOneById($applicationId);
        
    }
    
}

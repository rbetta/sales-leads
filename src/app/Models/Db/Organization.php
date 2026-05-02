<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Services\Client\ClientCriteria;
use App\Services\Client\ClientService;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends TimestampedSoftDeletableUuidModel
{
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'organization';
    
    /**
     * Get the relationship to the users belonging to this organization.
     * @return HasMany
     */
    public function users() : HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
    
    /**
     * Get the client this organization belongs to.
     * @return BelongsTo
     */
    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    /**
     * Get the parent organization this instance belongs to.
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }
    
    /**
     * Get the child organizations this instance has.
     * @return HasMany
     */
    public function children() : HasMany
    {
        return $this->hasMany(Organization::class, 'parent_id');
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
    
}

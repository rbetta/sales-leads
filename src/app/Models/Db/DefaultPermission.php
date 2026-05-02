<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Services\Permission\DefaultPermissionService;
use App\Services\Permission\PermissionLevelService;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefaultPermission extends TimestampedSoftDeletableUuidModel
{
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'default_permission';
    
    
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
            []
        );
    }
    
    /**
     * Get the relationship to the permission level this record belongs to.
     * @return BelongsTo
     */
    public function permissionLevel() : BelongsTo
    {
        return $this->belongsTo(PermissionLevel::class, 'permission_level_id');
    }
    
    /**
     * Get the associated PermissionLevel instance (if any).
     * @return PermissionLevel
     */
    public function getPermissionLevel() : PermissionLevel|null
    {
        
        // Get the permission level ID from this model instance.
        $permissionLevelId = $this->permission_level_id;
        
        // Retrieve the PermissionLevel instance by its ID.
        //
        // Use the service so we can avail ourselves of any caching
        // it may internally implement.
        //
        $permissionLevelService = \App::make(PermissionLevelService::class);
        return $permissionLevelService->findOneById($permissionLevelId);
        
    }
    
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Illuminate\Support\Facades\DB;

/**
 * An eloquent model that has a UUID primary key.
 * @author Randall Betta
 *
 */
class UuidModel extends BaseModel
{
    
    /**
     * Define the name of the primary key colmun.
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Define the primary key's data type.
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Define whether the primary key is automatically incrementing.
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * Handle the Laravel model initialization.
     */
    public static function boot()
    {
        parent::boot();
        
        // Generate a UUID primary key in the database before inserting this
        // model instance into the database.
        //
        // WARNING:
        //
        //      No matter what method is chosen to generate a UUID, a UUID
        //      primary key MUST be assigned immediately after inserting a
        //      model instance into the database. This is because Laravel
        //      will NOT automatically retrieve any newly-generated UUID
        //      primary key from a newly inserted record, so after saving
        //      the primary key will be null without this extra step.
        //
        static::creating(function (UuidModel $instance) {
            
            // Get the database connection used by the model instance.
            $dbConn = $instance->getConnection();
            
            // Calculate a UUID on the database server.
            $row    = $dbConn->selectOne('SELECT UUID() AS uuid_value');
            $uuid   = $row->uuid_value;
            
            // Assign the UUID to the model instance prior to saving it.
            // Laravel otherwise will not know the new record's UUID after
            // it is saved, so the model instance in memory will have no
            // primary key defined (making it impossible to reuse in any
            // contexts where the new record's primary key must be known).
            $keyName = $instance->getKeyName();
            $instance->{$keyName} = $uuid;
            
        });

    }
    
}

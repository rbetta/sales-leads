<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

/**
 * An eloquent model that has timestamp columns and a UUID primary key.
 * @author Randall Betta
 *
 */
class TimestampedUuidModel extends UuidModel
{
    
    /**
     * A class constant that defines the creation timestamp column name.
     * @var string
     */
    const CREATED_AT = 'created_at';
    
    /**
     * A class constant that defines the update timestamp column name.
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Get the attributes that should be cast.
     *
     * @return string[] An array whose keys are field names, and whose
     * values are casting instructions for the Eloquent ORM framework.
     */
    protected function casts() : array
    {
        return [
            self::CREATED_AT    => 'datetime',
            self::UPDATED_AT    => 'datetime',
        ];
    }
    
}

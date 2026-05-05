<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * An eloquent model that has timestamp columns and a UUID primary key,
 * and can be soft-deleted.
 * @author Randall Betta
 *
 */
class TimestampedSoftDeletableUuidModel extends TimestampedUuidModel
{
    
    use SoftDeletes;
    
    /**
     * A class constant that defines the deletion timestamp column name.
     * @var string
     */
    const DELETED_AT = 'deleted_at';
    
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
            [self::DELETED_AT    => 'datetime',]
        );
    }
    
}

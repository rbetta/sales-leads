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
    
}

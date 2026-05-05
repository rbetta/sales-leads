<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A base eloquent model from which all other models should descend.
 * @author Randall Betta
 *
 */
class BaseModel extends Model
{
    
    /**
     * Get this instance as an array with snake case properties names
     * converted to camel case.
     * @param BaseModel $instance
     * @return array The supplied instance as an array, with snake
     * case property names converted to camel case.
     */
    protected function toCamelCaseArray() : array
    {
        $arrayData = $this->toArray();
        return array_combine(
            array_map(
                array_keys($arrayData),
                fn(string $v) => Str::camel($v)
            ),
            $arrayData
        );
    }
    
}

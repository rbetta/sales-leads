<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection;

use Carcosa\Core\Collection\Interfaces\iSensitiveKeyValueCollection;
use Carcosa\Core\Collection\Traits\tSensitiveKeyValueCollection;

/**
 * A collection of sensitive key-value pairs. These values should not
 * appear in the output of PHP's var_dump() function.
 * @author Randall Betta
 *
 */
class SensitiveKeyValueCollection implements iSensitiveKeyValueCollection
{
    
    use tSensitiveKeyValueCollection;
    
}

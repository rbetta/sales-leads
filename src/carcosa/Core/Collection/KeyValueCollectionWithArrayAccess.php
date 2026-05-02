<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection;

use Carcosa\Core\Collection\Interfaces\iKeyValueCollectionWithArrayAccess;
use Carcosa\Core\Collection\Traits\tKeyValueCollectionWithArrayAccess;

/**
 * A key-value collection that implements the SPL \ArrayAccess interface.
 * @author Randall Betta
 *
 */
class KeyValueCollectionWithArrayAccess implements iKeyValueCollectionWithArrayAccess
{
    
    use tKeyValueCollectionWithArrayAccess;
    
}

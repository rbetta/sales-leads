<?php
declare(strict_types = 1);

namespace Carcosa\Core\Collection\Interfaces;

/**
 * An interface that extends the iKeyValueCollection to support all
 * necessary methods from the SPL \ArrayAccess interface.
 * @author Randall Betta
 *
 */
interface iKeyValueCollectionWithArrayAccess extends iKeyValueCollection, \ArrayAccess
{
    
}

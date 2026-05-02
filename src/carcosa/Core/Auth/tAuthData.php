<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth;

use Carcosa\Core\Collection\Traits\tSensitiveKeyValueCollection;

/**
 * A trait designed to implement the iAuthData interface, and to
 * automatically hide sensitive parameters from stack traces and var dumps.
 * @author Randall Betta
 *
 */
trait tAuthData
{
    
    use tSensitiveKeyValueCollection;
    
}

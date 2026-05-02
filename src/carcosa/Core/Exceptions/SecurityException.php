<?php
declare(strict_types = 1);

namespace Carcosa\Core\Exceptions;

/**
 * An exception that indicates a potential attempt to circumvent security,
 * or that a security feature is not operating correctly.
 * 
 * More stringent logging or more proactive reporting may be desired
 * for these cases.
 * @author Randall Betta
 *
 */
class SecurityException extends \Exception
{

}

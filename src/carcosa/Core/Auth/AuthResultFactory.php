<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth;

use App\Models\Db\User;
use Carcosa\Core\DataObjects\User\UserDataFactory;
use Carcosa\Core\Messages\MessageCollectionFactory;

/**
 * A class that creates AuthResult instances.
 * @author Randall Betta
 *
 */
class AuthResultFactory
{
    
    /**
     * Create a failed AuthResult instance.
     * @return AuthResult
     */
    public function createFailedAuthResult() : AuthResult
    {
        return new AuthResult(false);
    }
    
    /**
     * Create a successful AuthResult instance.
     * @param User $user The authenticated user.
     */
    public function createSuccessfulAuthResult(User $user) : AuthResult
    {
        return new AuthResult(true, $user);
    }
    
}

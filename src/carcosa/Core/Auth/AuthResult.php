<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth;

use App\Models\Db\User;
use Carcosa\Core\Messages\iHasMessages;
use Carcosa\Core\Messages\tHasMessages;

/**
 * A class that represents an authentication result.
 * @author Randall Betta
 *
 */
class AuthResult implements \JsonSerializable, iHasMessages
{
    
    use tHasMessages;
    
    /**
     * Whether the authentication was successful.
     * @var bool
     */
    private bool $isSuccess;
    
    /**
     * The authenticated user, if authentication was successful.
     * @var User|null
     */
    private User|null $user;
    
    /**
     * Create an instance of this class.
     * @param bool $isSuccess Whether the authentication was successful.
     * @param User|null $user The authenticated user (if any).
     * @throws \RuntimeException If authentication succeeded, but no user
     * was supplied.
     * @throws \RuntimeException If authentication failed, but a user
     * was supplied. 
     */
    public function __construct(bool $isSuccess, User|null $user = null)
    {
        
        // Sanity checks.
        if ($isSuccess && null === $user) {
            throw new \RuntimeException(
                "Authentication succeeded, but no user was supplied to " . __METHOD__
            );
        } elseif ($user && ! $isSuccess) {
            throw new \RuntimeException(
                "Authentication failed, but a user was supplied to " . __METHOD__
            );
        }
        
        // Initialize the collection of messages.
        $this->initializeMessages();
        
        // Set the results of the login authentication attempt.
        $this->isSuccess = $isSuccess;
        $this->user = $user;
    }
    
    /**
     * Get whether the authentication was successful.
     * @return bool
     */
    public function getIsSuccess() : bool
    {
        return $this->isSuccess;
    }
    
    /**
     * Get the authenticated user if authentication succeeded, or null
     * if it failed.
     * @return ?User
     */
    public function getUser() : ?User
    {
        return $this->user;
    }

    /**
     * Convert this class into a format suitable for JSON serialization.
     * @return mixed
     */
    public function jsonSerialize()
    {
        return (object) [
            'isSuccess' => $this->getIsSuccess(),
            'user'      => $this->getUser(),
            'messages'  => $this->getMessages(),
        ];
    }

}

<?php
declare(strict_types = 1);

namespace App\Services\Auth\AuthTypes\Password;

use Carcosa\Core\Auth\tAuthData;
use Carcosa\Core\Auth\AuthTypes\Password\iAuthDataPassword;

class PasswordAuthData implements iAuthDataPassword
{
    
    use tAuthData;
    
    /**
     * Construct an instance of this class, storing the user credentials
     * using a sensitive data storage mechanism.
     */
    public function __construct(string $username, string $password)
    {
        $this
            ->setValue($this->getUsernameKey(), $username)
            ->setValue($this->getPasswordKey(), $password);
    }
    
    /**
     * Get the key for the username.
     * @return string
     */
    public function getUsernameKey() : string
    {
        return 'username';
    }
    
    /**
     * Get the username.
     * @return string
     */
    public function getUsername() : string
    {
        return $this->getValue($this->getUsernameKey());
    }
    
    /**
     * Get the key for the password.
     * @return string
     */
    public function getPasswordKey() : string
    {
        return 'password';
    }
    
    /**
     * Get the password.
     * @return string
     */
    public function getPassword() : string
    {
        return $this->getValue($this->getPasswordKey());
    }
    
}

<?php
declare(strict_types = 1);

namespace App\Services\Auth\AuthTypes\Password;

/**
 * A factory class for instantiating PasswordAuthData instances.
 * @author Randall Betta
 *
 */
class PasswordAuthDataFactory
{
    
    /**
     * Create a PasswordAuthData instance.
     * @param string $username The username.
     * @param string $password The password.
     * @return PasswordAuthData
     */
    public function create(string $username, string $password) : PasswordAuthData
    {
        return new PasswordAuthData($username, $password);
    }
    
}

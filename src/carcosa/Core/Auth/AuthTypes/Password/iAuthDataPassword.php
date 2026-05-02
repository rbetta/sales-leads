<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth\AuthTypes\Password;

use Carcosa\Core\Auth\iAuthData;

interface iAuthDataPassword extends iAuthData
{
    
    /**
     * Get the key for the username.
     * @return string
     */
    public function getUsernameKey() : string;
    
    /**
     * Get the username.
     * @return string
     */
    public function getUsername() : string;
    
    /**
     * Get the key for the password.
     * @return string
     */
    public function getPasswordKey() : string;

    /**
     * Get the password.
     * @return string
     */
    public function getPassword() : string;
    
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth\AuthTypes\Password;

use Carcosa\Core\Auth\AuthResult;

class AuthManagerPassword
{
    
    /**
     * Log in using some authentication data.
     * @param iAuthDataPassword $authData
     * @return AuthResult
     */
    public function authenticate(iAuthDataPassword $authData) : AuthResult
    {
        $username = $authData->getData($authData->getUsernameKey());
        $password = $authData->getData($authData->getPasswordKey());
    }
    
}

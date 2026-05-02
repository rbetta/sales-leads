<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth;

use Carcosa\Core\Auth\LoginManager;

/**
 * A class for creating LoginManager instances/
 * @author Randall Betta
 */
class LoginManagerFactory
{
    
    /**
     * Get the default session key where login data are stored.
     * @return string
     */
    public function getDefaultSessionKey() : string
    {
        return 'CarcosaLoginData';
    }
    
    /**
     * Create a default LoginManager instance.
     * @return LoginManager
     */
    public function create() : LoginManager
    {
        $sessionKey = $this->getDefaultSessionKey();
        return new LoginManager($sessionKey);
    }
    
    /**
     * Create a LoginManager instance that uses a custom session key to
     * store login data.
     * @param string $sessionKey
     * @return LoginManager
     */
    public function createWithSessionKey(string $sessionKey) : LoginManager
    {
        return new LoginManager($sessionKey);
    }

}

<?php
declare(strict_types = 1);

namespace App\Services\Auth;

use App\Services\Auth\AuthTypes\Password\PasswordAuthData;
use App\Services\Auth\AuthTypes\Password\PasswordAuthManager;
use Carcosa\Core\Auth\AuthResult;
use Carcosa\Core\Auth\LoginManagerFactory;

/**
 * A service for managing user authentication.
 * @author Randall Betta
 *
 */
class AuthService
{
    
    /**
     * Given a cleartext password, return its hashed value.
     * @param string $cleartextPassword
     * @return string The hashed password.
     */
    public function getPasswordHash(#[\SensitiveParameter] string $cleartextPassword) : string
    {
        $authManager = \App::make(PasswordAuthManager::class);
        return $authManager->getPasswordHash($cleartextPassword);
    }
    
    /**
     * Attempt to authenticate a user using username/password credentials.
     * This DOES NOT log the user into the system; it only verifies that
     * the user's credentials are valid.
     * PasswordAuthData $authData The supplied username and password.
     * @return AuthResult
     */
    public function authenticateWithPassword(PasswordAuthData $authData) : AuthResult
    {
        
        // Attempt to authenticate the user.
        $authManager        = \App::make(PasswordAuthManager::class);
        $authResult         = $authManager->authenticate($authData);
        
        // Record the login attempt locally in a stateful way, so the browser
        // knows whether the current user is successfully authenticated.
        $this->recordLoginAttempt($authResult);
        
        // Return the authentication result.
        return $authResult;
        
    }
    
    /**
     * Locally record an authentication attempt's result in a stateful way,
     * so the browser knows whether the current user is logged in or not.
     * @param AuthResult $authResult
     * @return $this
     */
    private function recordLoginAttempt(AuthResult $authResult) : self
    {
        // Obtain a login manager.
        $loginManagerFactory    = \App::make(LoginManagerFactory::class);
        $loginManager           = $loginManagerFactory->create();
        
        // Locally record the login attempt's results.
        $loginManager->recordLoginAttempt($authResult);
        
        return $this;
    }
    
}

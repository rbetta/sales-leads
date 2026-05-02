<?php
declare(strict_types = 1);

namespace App\Services\Auth\AuthTypes\Password;

use App\Models\Db\User;
use Carcosa\Core\Auth\iAuthData;
use Carcosa\Core\Auth\AuthManagerInterface;
use Carcosa\Core\Auth\AuthResult;
use Carcosa\Core\Auth\AuthResultFactory;
use Carcosa\Core\Auth\AuthTypes\Password\iAuthDataPassword;
use Carcosa\Core\Messages\MessageCollection;
use Carcosa\Core\Messages\MessageFactory;

class PasswordAuthManager
{
    
    /**
     * Given a cleartext password, return its hashed value.
     * @param string $cleartextPassword
     * @return string The hashed password.
     */
    public function getPasswordHash(#[\SensitiveParameter] string $cleartextPassword) : string
    {
        return password_hash($cleartextPassword, PASSWORD_BCRYPT);
    }
    
    /**
     * Log in using some authentication data.
     * @param iAuthData $authData
     * @return AuthResult
     * @throws \RuntimeException If the user account is found, but no
     * password hash exists for that user record in the database.
     * (This should never happen.)
     */
    public function authenticate(iAuthDataPassword $authData) : AuthResult
    {
        
        // Obtain the login credentials.
        $username = $authData->getUsername();
        $password = $authData->getPassword();
        
        $messages = \App::make(MessageCollection::class);
        $messageFactory = \App::make(MessageFactory::class);
        
        // Obtain the login account.
        $user = User::where('username', $username)->get()->first();
        if (null === $user) {
            
            // User is nonexistent.
            $text = "The requested user account does not exist.";
            $message = $messageFactory->createError($text, true);
            $messages->add($message, 'username');
            
        } elseif ("" === $user->password) {
            
            // The password value is missing from the database.
            // (This should never happen.)
            throw new \RuntimeException(
                "No password hash is present for user record " . $user->id
            );
        
        } elseif (! password_verify($password, $user->password)) {
            
            // The password does not match.
            $text = "The supplied password is incorrect.";
            $message = $messageFactory->createError($text, true);
            $messages->add($message, 'username');
            
        } elseif (! $user->is_active) {
            
            // The user account is inactive.
            $text = "This account has been deactivated.";
            $message = $messageFactory->createError($text, true);
            $messages->add($message, 'username');
            
        }
        
        // Return the results of authentication.
        $authResultFactory = \App::make(AuthResultFactory::class);
        if ($messages->getHasError()) {
            
            // Authentication failed.
            $result = $authResultFactory->createFailedAuthResult();
            $result->addMessages($messages);
            
        } else {
            
            // Authentication succeeded. Return the authentication result,
            // including the logged-in user's account data.
            //
            // Note: convert the database-connected ORM model instance into
            // a representation that does not require any connection to the DB.
            //
            $result = $authResultFactory->createSuccessfulAuthResult($user);
            
        }
        return $result;

    }
    
}

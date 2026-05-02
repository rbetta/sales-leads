<?php
declare(strict_types = 1);

namespace Carcosa\Core\Auth;

use App\Models\Db\User;
use App\Services\User\UserService;
use Carcosa\Core\Auth\AuthResult;

/**
 * A class that can be used to record a login result, and query the user's
 * logged-in status.
 * @author Randall Betta
 *
 */
class LoginManager
{
    
    /**
     * The session sub-key where the logged-in user is stored.
     * @var string
     */
    private const SESSION_SUBKEY_FOR_LOGGED_IN_USER_ID = 'loggedInUserId';
    
    /**
     * The session key in which login data are stored.
     * @var string
     */
    private string $sessionKey;
    
    /**
     * Construct an instance of this class.
     * @param string $sessionKey The session key where login data are stored.
     * @throws \InvalidArgumentException If the specified key is equal to
     * the empty string after trimming whitespace.
     */
    public function __construct(string $sessionKey)
    {
        // Record the session key that contains all session data.
        $this->setSessionKey($sessionKey);
        
        // Initialize the session login data, if necessary.
        if (! session()->exists($sessionKey)) {
            session()->put($sessionKey, []);
        }
    }
    
    /**
     * Set the session key in which login data are stored.
     * @param string $key
     * @throws \InvalidArgumentException If the specified key is equal to
     * the empty string after trimming whitespace.
     * @return $this
     */
    private function setSessionKey(string $key) : self
    {
        // Sanity check on the key.
        if ('' === trim($key)) {
            throw new \InvalidArgumentException(
                "An invalid key was supplied to " . __METHOD__ . " (expected: " .
                "a nonempty key after trimming whitespace."
            );
        }
        
        $this->sessionKey = $key;
        return $this;
    }
    
    /**
     * Get the session key in which login data are stored.
     * @return string
     */
    public function getSessionKey() : string
    {
        return $this->sessionKey;
    }
    
    /**
     * Get the session key that stores the logged-in user ID (if any).
     * @return string
     */
    private function getSessionKeyForLoggedInUserId() : string
    {
        return $this->getSessionKey() . '.' . self::SESSION_SUBKEY_FOR_LOGGED_IN_USER_ID;
    }
    
    /**
     * Record an authentication result.
     * @param AuthResult $authResult
     * @return $this
     */
    public function recordLoginAttempt(AuthResult $authResult) : self
    {
        if ($authResult->getIsSuccess()) {
            
            // Authentication succeeded. Record the user's successful login.
            $this->setLoggedInUser($authResult->getUser());
            
            // Save the session immediately, to preserve authentication data
            // in case of any later error that prevents serialization to storage.
            session()->save();
            
        } else {
            
            // A login attempt failed. Log the current user out (if any).
            $this->logout();
            
        }
        return $this;
    }
    
    /**
     * Record a logged-in user.
     * @param User $user
     * @return $this
     */
    private function setLoggedInUser(User $user) : self
    {        
        // Get the session key that contains the logged-in user.
        $sessionKeyForLoggedInUserId = $this->getSessionKeyForLoggedInUserId();
        
        // Record the logged-in user in the session.
        session()->put($sessionKeyForLoggedInUserId, $user->id);
        return $this;
    }
    
    /**
     * Get the currently logged-in user (if any).
     * @return ?User
     */
    public function getLoggedInUser() : ?User
    {
        
        // Get the session key that contains the logged-in user.
        $sessionKeyForLoggedInUserId = $this->getSessionKeyForLoggedInUserId();

        // Retrieve the logged-in user (if any).
        $userId = session()->get($sessionKeyForLoggedInUserId, null);
        
        // Retrieve the logged-in user. Use an appropriate service so we
        // can take advantage of any centralized caching it may implement.
        $userService = \App::make(UserService::class);
        return ($userId ? $userService->findOneById($userId) : null);
    }
    
    /**
     * Get whether the user is logged in.
     * @return bool
     */
    public function getIsLoggedIn() : bool
    {
        return (bool) $this->getLoggedInUser();
    }
    
    /**
     * Log the current user out. If the user is already logged out, then
     * do nothing.
     * @return $this
     */
    public function logout() : self
    {
        // Remove all login data from the session.
        $this->clearLoginData();
        
        return $this;
    }
    
    /**
     * Clear the login data from the session.
     * @return $this
     */
    private function clearLoginData() : self
    {
        // Get the session key that contains all login data.
        $sessionKey = $this->getSessionKey();
        
        // Delete all login data from the session.
        session()->put($sessionKey, []);
        
        return $this;
    }
}

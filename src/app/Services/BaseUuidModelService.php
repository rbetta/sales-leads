<?php
declare(strict_types = 1);
namespace App\Services;

use App\Models\Db\User;
use Carcosa\Core\Auth\LoginManagerFactory;
use Carcosa\Core\Service\AbstractUuidModelService;

/**
 * A service for managing records with UUID primary keys.
 * @author Randall Betta
 *
 */
class BaseUuidModelService extends AbstractUuidModelService
{
    
    /**
     * Whether to restrict results to only those belonging to
     * the logged-in user.
     * @var bool
     */
    private bool $restrictResultsByUser = true;
    
    /**
     * Set whether to restrict results to only those belonging to
     * the logged-in user.
     * @param bool $restrict
     * @return $this
     */
    public function setRestrictResultsByUser(bool $limit) : self
    {
        $this->restrictResultsByUser = $restrict;
        return $this;
    }
    
    /**
     * Get whether to restrict results to only those belonging to
     * the logged-in user.
     * @return bool
     */
    public function getRestrictResultsByUser() : bool
    {
        return $this->restrictResultsByUser;
    }
    
    /**
     * Get the logged-in user.
     * @return User|null The logged-in user, or null if no user is logged in.
     */
    protected function getLoggedInUser() : User|null
    {
        $loginManager = (\App::make(LoginManagerFactory::class))->create();
        return $loginManager->getLoggedInUser();
    }
    
}

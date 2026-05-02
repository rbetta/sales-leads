<?php
declare(strict_types = 1);
namespace Carcosa\Core\Service;

use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A class that represents criteria for retrieving one or more models
 * through a service.
 * @author Randall Betta
 *
 */
abstract class AbstractModelCriteria
{
    
    /**
     * Whether to limit results to only those the logged-in user
     * has permission to see.
     * @var bool
     */
    private bool $limitToLoggedInUser = true;
    
    /**
     * Set whether to limit results to only those the logged-in user
     * has permission to see.
     * @param bool $limit
     * @return $this
     */
    public function setLimitToLoggedInUser(bool $limit) : self
    {
        $this->limitToLoggedInUser = $limit;
        return $this;
    }
    
    /**
     * Get whether to limit results to only those the logged-in user
     * has permission to see.
     * @return bool
     */
    public function getLimitToLoggedInUser() : bool
    {
        return $this->limitToLoggedInUser;
    }
    
}

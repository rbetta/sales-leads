<?php
declare(strict_types = 1);
namespace App\Http\Middleware;

use App\Models\Db\User;

class AuthenticateSystemAdminUser extends AbstractAuthenticateUser
{
    
    /**
     * Define a method that, given a logged-in user record, returns
     * whether the user is authorized to access the requested resource.
     * @param User $user
     * @return bool
     */
    protected function getIsAuthorized(User $user) : bool
    {
        return $user->is_system_admin;
    }
    
}
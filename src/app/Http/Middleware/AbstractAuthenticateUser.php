<?php
declare(strict_types = 1);
namespace App\Http\Middleware;
 
use App\Models\Db\User;
use Carcosa\Core\Auth\LoginManagerFactory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
abstract class AbstractAuthenticateUser
{
    
    /**
     * Define a method that, given a logged-in user record, returns
     * whether the user is authorized to access the requested resource.
     * @param User $user
     * @return bool
     */
    protected abstract function getIsAuthorized(User $user) : bool;
    
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure(Request): (Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Instantiate a login manager.
        $loginManagerFactory = \App::make(LoginManagerFactory::class);
        $loginManager = $loginManagerFactory->create();
        
        // Obtain the user record (if any).
        $user = $loginManager->getLoggedInUser();
        
        // Check if the user provided valid credentials.
        $isLoggedIn = $loginManager->getIsLoggedIn();
        
        // Check if the user is permitted to access the requested resource.
        $isAuthorized = $user ? $this->getIsAuthorized($user) : false;
        
        // Redirect if the user is not logged in and authorized.
        if ( ! ($isLoggedIn && $isAuthorized) )
        {
            
            // If the originally requested URL was a GET request, then
            // ensure the user is redirected to that inteded URL after
            // login is successful.
            $queryParams = [];
            if ($request->isMethod('get')) {
                $queryParams['redirectTo'] = $request->fullUrl();
            }
            
            // Redirect to the login screen, including the original intended URL.
            return redirect()->route('login:password:display-login-form', $queryParams);
        }
        
        // Pass the request to the next middleware instance.
        return $next($request);
        
    }
}
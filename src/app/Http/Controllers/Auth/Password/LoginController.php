<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Auth\Password;

use App\Services\Auth\AuthService;
use App\Services\Auth\AuthTypes\Password\PasswordAuthDataFactory;
use App\Http\Controllers\BaseController;
use Carcosa\Core\Messages\MessageType;
use Carcosa\Core\Auth\LoginManagerFactory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class LoginController extends BaseController
{

    /**
     * The login form view.
     * @var string
     */
    private const LOGIN_FORM_VIEW = 'login.login-form';
    
    /**
     * Display the login screen.
     * @param Request $request
     */
    public function displayLoginForm(Request $request)
    {
        return view(self::LOGIN_FORM_VIEW, [
            'loginActionUrl'    => route('login:password:handle-login-form'),
            'username'          => '',
            'password'          => '',
            'redirectTo'        => $request->query('redirectTo', ''),
        ]);
    }
    
    /**
     * Handle a login request.
     * @param Request $request
     * @param PasswordAuthDataFactory $passwordAuthDataFactory
     * @param AuthService $authService
     * @param LoginManagerFactory $loginManagerFactory
     * @return View|RedirectResponse
     */
    public function handleLoginForm(
        Request $request,
        PasswordAuthDataFactory $passwordAuthDataFactory,
        AuthService $authService,
        LoginManagerFactory $loginManagerFactory
    ) : View|RedirectResponse
    {
        
        // Obtain login credentials.
        $username = $request->post('username');
        $password = $request->post('password');
        
        // Obtain the URL to redirect the user to after authentication, if any.
        $redirectTo = $request->post('redirectTo', '');
        
        // Convert the sensitive login credentials into a form that will
        // not be outputted as part of a stack trace if any error occurs.
        $authData = $passwordAuthDataFactory->create($username, $password);
        
        // Attempt to log the user in.
        $authResult = $authService->authenticateWithPassword($authData);
        
        // Record the authentication result using the login manager.
        $loginManager = $loginManagerFactory->create();
        $loginManager->recordLoginAttempt($authResult);
        
        // Handle a successful or failed login appropriately.
        if ($authResult->getIsSuccess()) {
            
            // Login succeeded.
            $user = $authResult->getUser();
            if ('' !== $redirectTo) {
                return redirect()->to($redirectTo);
            } elseif ($user->is_seller) {
                return redirect()->route('seller:home:logged-in');
            } elseif ($user->is_customer) {
                return redirect()->route('customer:home:logged-in');
            } elseif ($user->is_system_admin) {
                return redirect()->route('system-admin:home:logged-in');
            }
            
        } else {
            
            // Login failed.
            
            // Retrieve validation error messages.
            $messages = $authResult->getMessages();
            $errors = $messages
                ->filterByType(MessageType::Error);
            
            // Redisplay the login form.
            return redirect()
                ->route('login:password:display-login-form')
                ->withErrors($errors->toMessageBag())
                ->withInput();

        }
        
    }
    
    /**
     * Handle a logout request.
     * @param Request $request
     * @param LoginManagerFactory $loginManagerFactory
     * @return View|RedirectResponse
     */
    public function handleLogoutForm(
        Request $request,
        LoginManagerFactory $loginManagerFactory
    ) : View|RedirectResponse
    {
            
            // Log the user out.
            $loginManager = $loginManagerFactory->create();
            $loginManager->logout();

            // Redirect to the logged-out homepage.
            return redirect()->route('home:logged-out');
            
    }
    
}

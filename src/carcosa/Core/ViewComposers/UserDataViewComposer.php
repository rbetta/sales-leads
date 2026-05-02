<?php
declare(strict_types=1);
namespace Carcosa\Core\ViewComposers;

use Carcosa\Core\Auth\LoginManager;
use Carcosa\Core\Auth\LoginManagerFactory;
use Carcosa\Core\I18n\LocaleFactory;
use Illuminate\View\View;
 
/**
 * A view composer for including information about the logged-in user
 * into a view.
 * @author Randall Betta
 */
class UserDataViewComposer
{
    /**
     * A LoginManager instance.
     * @var LoginManager
     */
    private LoginManager $loginManager;
    
    /**
     * Create a new instance of this class.
     * @param LoginManager $loginManager
     * @param string $sessionKey The session key where login data will
     * be stored.
     */
    public function __construct(LoginManagerFactory $loginManagerFactory, ?string $sessionKey = null)
    {
        if (null === $sessionKey) {
            $this->loginManager = $loginManagerFactory->create();
        } else {
            $this->loginManager = $loginManagerFactory->create($sessionKey);
        }
    }
    
    /**
     * Get the login manager.
     * @return LoginManager
     */
    private function getLoginManager() : LoginManager
    {
        return $this->loginManager;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view) : void
    {
        
        $loggedInUser   = $this->getLoginManager()->getLoggedInUser();
        $localeFactory  = \App::make(LocaleFactory::class);
        
        $view->with([
            'carcosa' => [
                'user'      => $loggedInUser,
                'locale'    => $loggedInUser?->getLocale() ?? $localeFactory->getDefaultLocale(),
            ],
        ]);
    }
}
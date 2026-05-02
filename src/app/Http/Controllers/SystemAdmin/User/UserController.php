<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Admin\AdminBaseController;;
use App\Models\Db\Client;
use App\Services\Application\ApplicationCriteria;
use App\Services\Application\ApplicationService;
use App\Services\Client\ClientService;
use App\Services\Group\GroupCriteria;
use App\Services\Group\GroupService;
use App\Services\User\UserCriteria;
use App\Services\User\UserService;
use Carcosa\Core\I18n\LocaleFactory;
use Carcosa\Core\Services\ServiceRequestFactory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends AdminBaseController
{

    /**
     * Display the list of users.
     * @param Request $request
     * @param ClientService $clientService
     * @param UserService $userService
     * @param UserCriteria $userCriteria
     * @param string $clientId The ID of the client whose users
     * should be displayed.
     * @return View
     */
    public function displayUsersList(
        Request $request,
        ClientService $clientService,
        UserService $userService,
        UserCriteria $userCriteria,
        string $clientId,
    ) {
        
        // Retrieve the client.
        $client = $clientService->findOneById($clientId);
        
        // Find all users belonging to the requested client.
        $userCriteria->setClient($client);
        $users = $userService->find($userCriteria);
        
        // Display the list to the end user.
        return view('admin.user.list-users', [
            'client'    => $client,
            'users'     => $users,
        ]);
        
    }
    
    /**
     * Display the Create User form.
     * @param Request $request
     * @param ClientService $clientService
     * @param ApplicationCriteria $applicationCriteria
     * @param ApplicationService $applicationService
     * @param string $clientId The ID of the client that will own the new
     * user record.
     * @return View
     */
    public function displayCreateUserForm(
        Request $request,
        ClientService $clientService,
        ApplicationCriteria $applicationCriteria,
        ApplicationService $applicationService,
        string $clientId
    )
    {
        $client = $clientService->findOneById($clientId);
        
        // Retrieve all applications for the given client.
        $allApplications = $this->getApplicationLabelsById($client);
        
        // Retrieve all groups (not just those associated
        // with this user).
        $allGroups = $this->getGroupLabelsById();
        
        return view('admin.user.create-or-edit-user', [
            'client'            => $client,
            'user'              => null,
            'locales'           => $this->getLocaleSelectorData(),
            'allApplications'   => $allApplications,
            'allGroups'         => $allGroups,
            'groups'            => [],
        ]);
    }
    
    /**
     * Display the Edit User form.
     * @param Request $request
     * @param UserService $userService
     * @param string $userId
     * @return View
     */
    public function displayEditUserForm(
        Request $request,
        UserService $userService,
        string $userId
    )
    {
        // We are editing an existing user. Retrieve it.
        $user = $userService->findOneById($userId);
        
        // Retrieve the user's parent client.
        $client = $user->getClient();
        
        // Retrieve all applications (not just those
        // associated with this user).
        $allApplications = $this->getApplicationLabelsById($client);
        
        // Retrieve all groups (not just those associated
        // with this user).
        $allGroups = $this->getGroupLabelsById();
        
        // Retrieve group IDs associated with this user.
        $groups = array_map(
            fn(Group $g) => $g->id,
            $user->getGroups()
        );
        
        return view('admin.user.create-or-edit-user', [
            'user'          => $user,
            'client'        => $client,
            'locales'       => $this->getLocaleSelectorData(),
            'allGroups'     => $allGroups,
            'groups'        => $groups,
        ]);
    }
    
    /**
     * Handle the Create or Edit User form submission.
     * @param Request $request,
     * @param UserService $userService
     * @return View
     */
    public function handleCreateOrEditUserForm(
        Request $request,
        UserService $userService,
    )
    {
        
        // Store form submission values in the session.
        $request->flash();
        
        // Retrieve model data from the request.
        $prefix     = 'user';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $userService->save($data);
        $hasError       = $serviceResult->getHasError();
        $user           = $serviceResult->getValue('user');
        
        // Display the result to the end user.
        if (! $hasError) {
            
            // A record was created or updated. Redirect to the record list.
            $clientId = $user->getClient()->id;
            return redirect()->route(
                'admin:user:display-user-list',
                ['clientId' => $clientId]
            );
            
        } else {
            
            // A validation error occurred. Redisplay the form.
            $errors = $serviceResult
                ->getMessages()
                ->withPrefixForFields($prefix, '.')
                ->toMessageBag();
            return back()->withInput()->withErrors($errors);
            
        }
        
    }
    
    /**
     * Get the data for the "Locales" selection form field.
     * @return string[] An array whose keys are values for an HTML option
     * element's "value" attribute, and whose values are their corresponding
     * displayed text.
     */
    private function getLocaleSelectorData() : array
    {
        // Get all supported locale codes.
        $localeFactory = \App::make(LocaleFactory::class);
        $locales = $localeFactory->getAllLocaleCodes();
        
        // Format the data for the UI's selector field.
        return array_combine($locales, $locales);
    }
    
    /**
     * Get all application labels for the given client,
     * keyed by their IDs.
     * @param Client $client
     * @return string[]
     * @throws \RuntimeException If locale-aware sorting fails.
     */
    private function getApplicationLabelsById(Client $client) : array
    {
        
        // Retrieve all applications for the client.
        $criteria = \App::make(ApplicationCriteria::class);
        $service  = \App::make(ApplicationService::class);
        $criteria->setClient($client);
        $records = $service->find($criteria);
        
        // Convert the records into an array structure suitable for
        // use in an HTML dropdown or similar form element.
        $results = $this->getValuesByIdFromModels(
            $records,
            fn($instance) => $instance->label
        );

        // Sort the results alphabetically, according to the user's locale.
        $this->asortByLoggedInUserLocale($results);

        return $results;
    }
    
    /**
     * Get all group labels for the given application,
     * keyed by their IDs.
     * @return string[]
     * @throws \RuntimeException If locale-aware sorting fails.
     */
    private function getGroupLabelsById() : array
    {
        
        // Retrieve all groups for the application.
        $criteria = \App::make(GroupCriteria::class);
        $service  = \App::make(GroupService::class);
        $records = $service->find($criteria);
        
        // Convert the records into an array structure suitable for
        // use in an HTML dropdown or similar form element.
        $results = $this->getValuesByIdFromModels(
            $records,
            fn($instance) => $instance->label
        );

        // Sort the results alphabetically, according to the user's locale.
        $this->asortByLoggedInUserLocale($results);
        
        return $results;
    }
    
}

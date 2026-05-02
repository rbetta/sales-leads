<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Client\ClientCriteria;
use App\Services\Client\ClientService;
use App\Services\Organization\OrganizationCriteria;
use App\Services\Organization\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends AdminBaseController
{

    /**
     * Display the list of organizations.
     * @param Request $request
     * @param ClientService $clientService
     * @param OrganizationService $orgService
     * @param OrganizationCriteria $orgCriteria
     * @param string $clientId The ID of the client whose organizations
     * should be displayed.
     * @return View
     */
    public function displayOrganizationsList(
        Request $request,
        ClientService $clientService,
        OrganizationService $orgService,
        OrganizationCriteria $orgCriteria,
        string $clientId
    ) {
        
        // Retrieve the client.
        $client = $clientService->findOneById($clientId);
        
        // Find all organizations belonging to the requested client.
        $orgCriteria->setClient($client);
        $orgs = $orgService->find($orgCriteria);

        // Display the list to the end user.
        return view('admin.organization.list-organizations', [
            'client'        => $client,
            'organizations' => $orgs,
        ]);
        
    }
    
    /**
     * Display the Create Organization form.
     * @param Request $request
     * @param ClientService $clientService
     * @param string $clientId The ID of the client that will own the new
     * organization record.
     * @return View
     */
    public function displayCreateOrganizationForm(
        Request $request,
        ClientService $clientService,
        string $clientId
    )
    {
        // Retrieve the client this new record will belong to..
        $client = $clientService->findOneById($clientId);
        
        // Display the form.
        return view('admin.organization.create-or-edit-organization', [
            'organization'  => null,
            'client'        => $client,
        ]);
    }
    
    /**
     * Display the Edit Organization form.
     * @param Request $request
     * @param OrganizationService $orgService
     * @param string $organizationId
     * @return View
     */
    public function displayEditOrganizationForm(
        Request $request,
        OrganizationService $orgService,
        string $organizationId
    )
    {
        
        // We are editing an existing organization. Retrieve it.
        $org = $orgService->findOneById($organizationId);
        
        // Retrieve the organization's parent client.
        $client = $org->getClient();

        return view('admin.organization.create-or-edit-organization', [
            'organization'  => $org,
            'client'        => $client,
        ]);
    }
    
    /**
     * Handle the Create or Edit Organization form submission.
     * @param Request $request,
     * @param OrganizationService $organizationService
     * @param OrganizationCriteria $orgCriteria
     * @return View
     */
    public function handleCreateOrEditOrganizationForm(
        Request $request,
        OrganizationService $orgService,
        OrganizationCriteria $orgCriteria,
    )
    {
        
        // Store form submission values in the session.
        $request->flash();
        
        // Retrieve model data from the request.
        $prefix     = 'organization';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $orgService->save($data);
        $hasError       = $serviceResult->getHasError();
        $org            = $serviceResult->getValue('organization');
        
        // Display the result to the end user.
        if (! $hasError) {
            
            // A record was created or updated. Redirect to the record list.
            $clientId = $org->getClient()->id;
            return redirect()->route(
                'admin:organization:display-organization-list',
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
    
}

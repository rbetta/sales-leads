<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Client\ClientCriteria;
use App\Services\Client\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends AdminBaseController
{

    /**
     * Display the list of clients.
     * @param Request $request
     * @param ClientService $clientService
     * @param ClientCriteria $clientCriteria
     * @return View
     */
    public function displayClientsList(
        Request $request,
        ClientService $clientService,
        ClientCriteria $clientCriteria
    ) {
        
        // Retrieve all clients.
        $clients = $clientService->find($clientCriteria);

        // Display the list to the end user.
        return view('admin.client.list-clients', [
            'clients' => $clients,
        ]);
        
    }
    
    /**
     * Display the Create or Edit Client form.
     * @param Request $request
     * @param ClientService $clientService
     * @param ClientCriteria $clientCriteria
     * @param ?string $clientId
     * @return View
     */
    public function displayCreateOrEditClientForm(
        Request $request,
        ClientService $clientService,
        ClientCriteria $clientCriteria,
        ?string $clientId = null
    )
    {

        // Determine if we are creating or editing a record.
        $isNew = ('' === (string) $clientId);

        $viewData = [];
        if ($isNew) {
            
            // We are creating a new record.
            $viewData['client'] = null;
            
        } else {
            
            // We are editing an existing record.
            $clientCriteria->setId($clientId);
            $client = $clientService->findOne($clientCriteria);
            $viewData['client'] = $client;
            
        }
        
        return view('admin.client.create-or-edit-client', $viewData);
    }
    
    /**
     * Handle the Create or Edit Client form submission.
     * @param Request $request,
     * @param ClientService $clientService
     * @return View
     */
    public function handleCreateOrEditClientForm(
        Request $request,
        ClientService $clientService
    )
    {
        
        // Store form submission values in the session.
        $request->flash();

        // Retrieve model data from the request.
        $prefix     = 'client';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $clientService->save($data);
        $hasError       = $serviceResult->getHasError();
        $client         = $serviceResult->getValue('client');
        
        // Display the result to the end user.
        if (! $hasError) {
            
            // A record was created or updated. Redirect to the record list.
            return redirect()->route(
                'admin:client:display-client-list',
                []
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

<?php
declare(strict_types = 1);
namespace App\Http\Controllers\SystemAdmin\LeadCategory;

use App\Http\Controllers\SystemAdmin\SystemAdminBaseController;
use App\Services\LeadCategory\LeadCategoryCriteria;
use App\Services\LeadCategory\LeadCategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadCategoryController extends SystemAdminBaseController
{

    /**
     * Display the list of lead categories.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param LeadCategoryCriteria $leadCategoryCriteria
     * @return View
     */
    public function displayLeadCategoriesList(
        Request $request,
        LeadCategoryService $leadCategoryService,
        LeadCategoryCriteria $leadCategoryCriteria
    ) {
        
        // Retrieve all records.
        $leadCategories = $leadCategoryService->find($leadCategoryCriteria);

        // Display the list to the end user.
        return view('system-admin.lead-category.list-lead-categories', [
            'leadCategories' => $leadCategories,
        ]);
        
    }
    
    /**
     * Display the Create or Edit Lead Category form.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param LeadCategoryCriteria $leadCategoryCriteria
     * @param ?string $leadCategoryId
     * @return View
     */
    public function displayCreateOrEditLeadCategoryForm(
        Request $request,
        LeadCategoryService $leadCategoryService,
        LeadCategoryCriteria $leadCategoryCriteria,
        ?string $leadCategoryId = null
    )
    {

        // Determine if we are creating or editing a record.
        $isNew = ('' === (string) $leadCategoryId);

        $viewData = [];
        if ($isNew) {
            
            // We are creating a new record.
            $viewData['leadCategory'] = null;
            
        } else {
            
            // We are editing an existing record.
            $leadCategoryCriteria->setId($leadCategoryId);
            $leadCategory = $leadCategoryService->findOne($leadCategoryCriteria);
            $viewData['leadCategory'] = $leadCategory;
            
        }
        
        return view('system-admin.lead-category.create-or-edit-lead-category', $viewData);
    }
    
    /**
     * Handle the Create or Edit Lead Category form submission.
     * @param Request $request,
     * @param LeadCategoryService $leadCategoryService
     * @return View
     */
    public function handleCreateOrEditLeadCategoryForm(
        Request $request,
        LeadCategoryService $leadCategoryService
    )
    {
        
        // Store form submission values in the session.
        $request->flash();

        // Retrieve model data from the request.
        $prefix     = 'leadCategory';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $leadCategoryService->save($data);
        $hasError       = $serviceResult->getHasError();
        $leadCategory   = $serviceResult->getValue('leadCategory');
        
        // Display the result to the end user.
        if (! $hasError) {
            
            // A record was created or updated. Redirect to the record list.
            return redirect()->route(
                'system-admin:lead-category:display-lead-category-list',
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

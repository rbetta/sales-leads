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
     * @param LeadCategoryCriteria $leadCategoryCriteria
     * @return View
     */
    public function displayLeadCategoriesList(
        Request $request,
        LeadCategoryService $leadCategoryService,
    ) {
        
        // Retrieve all records as an array of TreeNode instances,
        // each containing a LeadCategory instance as its value.
        $leadCategoryTrees = $leadCategoryService->getAllAsTrees();

        // Display the list to the end user.
        return view('system-admin.lead-category.list-lead-categories', [
            'leadCategories' => $leadCategoryTrees,
        ]);
        
    }
    
    /**
     * Display the Create Lead Category form.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService,
     * @param ?string $parentId The parent lead category ID (if any).
     * @return View
     */
    public function displayCreateLeadCategoryForm(
        Request $request,
        LeadCategoryService $leadCategoryService,
        ?string $parentId = null
    ) {
        
        $leadCategory = $leadCategoryService->createNew();
        if ('' !== "$parentId") {
            $parentCategory = $leadCategoryService->findOneById($parentId);
            $leadCategory->parent()->associate($parentCategory);
        }
        return view('system-admin.lead-category.create-or-edit-lead-category', [
            'leadCategory' => $leadCategory,
        ]);
        
    }
    
    /**
     * Display the Edit Lead Category form.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param ?string $leadCategoryId
     * @return View
     */
    public function displayEditLeadCategoryForm(
        Request $request,
        LeadCategoryService $leadCategoryService,
        string $leadCategoryId
    )
    {
        $leadCategory = $leadCategoryService->findOneById($leadCategoryId);
        return view('system-admin.lead-category.create-or-edit-lead-category', [
            'leadCategory' => $leadCategory,
        ]);
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

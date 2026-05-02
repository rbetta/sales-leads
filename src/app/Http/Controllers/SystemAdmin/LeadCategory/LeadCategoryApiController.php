<?php
declare(strict_types = 1);
namespace App\Http\Controllers\SystemAdmin\LeadCategory;

use App\Http\Controllers\SystemAdmin\SystemAdminBaseController;
use App\Services\LeadCategory\LeadCategoryCriteria;
use App\Services\LeadCategory\LeadCategoryService;
use Carcosa\Core\Api\ApiResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadCategoryApiController extends SystemAdminBaseController
{

    /**
     * Get lead categories by parent ID.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param LeadCategoryCriteria $leadCategoryCriteria
     * @param ApiResponseFactory $apiResponseFactory
     * @param string|null $parentId
     * @return View
     */
    public function getByParentId(
        Request $request,
        LeadCategoryService $leadCategoryService,
        LeadCategoryCriteria $leadCategoryCriteria,
        ApiResponseFactory $apiResponseFactory,
        string|null $parentId = null,
    ) {
        
        // Retrieve all root records.
        $leadCategoryCriteria->setParentId(null);
        $leadCategories = $leadCategoryService->find($leadCategoryCriteria);

        // Convert categories into objects implementing iDataObject.
        $results = $this->getDataObjectsFromModels($leadCategories);
        
        // Create the API response.
        $apiResponse = $apiResponseFactory->create(
            ['leadCategories' => $results]
        );
        
        // Return the list to the end user.
        return response()->json($apiResponse);
        
    }
    
    /**
     * Save a lead category.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param string|null $leadCategoryId
     * @return View
     */
    public function save(
        Request $request,
        LeadCategoryService $leadCategoryService,
        string|null $leadCategoryId = null,
    ) {
        
        // Retrieve model data from the request.
        $prefix     = 'leadCategory';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $leadCategoryService->save((array) $data);
        $hasError       = $serviceResult->getHasError();
        $leadCategory   = $serviceResult->getValue('leadCategory');
        
        if ($hasError) {
            
            // An error occurred.
            return response()->json($serviceResult);
            
        } else {
            
            // The record was successfully saved. Return it.
            return response()->json($serviceResult);
            
        }
        
    }
    
}

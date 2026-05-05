<?php
declare(strict_types = 1);
namespace App\Http\Controllers\SystemAdmin\LeadCategory;

use App\Http\Controllers\SystemAdmin\SystemAdminBaseController;
use App\Services\LeadCategory\LeadCategoryCriteria;
use App\Services\LeadCategory\LeadCategoryService;
use Carcosa\Core\Api\ApiResponseFactory;
use Carcosa\Core\Util\ListFormatter;
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
     * @param ApiResponseFactory $apiResponseFactory
     * @param string|null $leadCategoryId
     * @return View
     */
    public function save(
        Request $request,
        LeadCategoryService $leadCategoryService,
        ApiResponseFactory $apiResponseFactory,
        string|null $leadCategoryId = null,
    ) {
        
        // Retrieve model data from the request.
        $prefix     = 'leadCategory';
        $data       = $request->input($prefix);
        
        // Attempt to create or update the record.
        $serviceResult  = $leadCategoryService->save((array) $data);
        $hasError       = $serviceResult->getHasError();
        $leadCategory   = $serviceResult->getValue('leadCategory');
        
        // Construct an API response.
        $apiResponse = $apiResponseFactory->create(
            ['leadCategory' => $leadCategory?->toDataObject()],
            $serviceResult->getMessages(),
        );
        
        if ($hasError) {
            
            // An error occurred.
            return response()->json($apiResponse);
            
        } else {
            
            // The record was successfully saved. Return it.
            return response()->json($apiResponse);
            
        }
        
    }
    
    /**
     * Soft-delete a lead category.
     * @param Request $request
     * @param LeadCategoryService $leadCategoryService
     * @param ApiResponseFactory $apiResponseFactory
     * @param ListFormatter $listFormatter
     * @param string $childStrategy The literal string "delete-children"
     * or "promote-children" (indicating how to handle children of the
     * deleted lead category).
     * @param string $leadCategoryId
     * @return View
     * @throws \RuntimeException If the $handleChildren argument
     * has an unexpected value.
     */
    public function delete(
        Request $request,
        LeadCategoryService $leadCategoryService,
        ApiResponseFactory $apiResponseFactory,
        ListFormatter $listFormatter,
        string $childStrategy,
        string $leadCategoryId,
    ) {
        
        // Sanity check for how to handle children.
        //
        // Note that this should automatically be handled by the route
        // logic, so this only tests the code's internal logical consistency.
        $this->validateChildStrategyForDeletion($childStrategy);
        
        // Attempt to create or update the record.
        $serviceResult  = $leadCategoryService->delete([
            'leadCategoryId'    => $id,
            'childStrategy'     => $childStrategy,
        ]);
        $hasError       = $serviceResult->getHasError();
        
        $leadCategory   = $serviceResult->getValue('leadCategory');
        
        // Construct an API response.
        $apiResponse = $apiResponseFactory->create(
            ['leadCategory' => $leadCategory?->toDataObject()],
            $serviceResult->getMessages(),
        );
        
        if ($hasError) {
            
            // An error occurred.
            return response()->json($apiResponse);
            
        } else {
            
            // The record was successfully saved. Return it.
            return response()->json($apiResponse);
            
        }
        
    }
    
    /**
     * Validate the path paremter for how to handle child nodes during deletion.
     * @param string $option Either "delete-children" or "promote-children" as
     * a string literal.
     * @return $this
     * @throws \RuntimeException If the $options argument is invalid.
     */
    private function validateChildStrategyForDeletion(string $option) : self
    {
        $listFormatter = \App::make(ListFormatter::class);
        $validChildrenOptions = ['delete-children', 'promote-children'];
        $validChildrenOptionsText = $listFormatter->format($validChildrenOptions);
        
        // Perform the validation.
        if (! in_array($handleChildren, $validChildrenOptions, true) ) {
            throw new \RuntimeException(
                "The path parameter for how to handle children of " .
                "deleted lead categories has the uexpected value " .
                "\"$handleChildren\" (expected: $validChildrenOptionsText)."
            );
        }
        return $this;
    }
    
}

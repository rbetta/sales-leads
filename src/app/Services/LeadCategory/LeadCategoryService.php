<?php
declare(strict_types = 1);
namespace App\Services\LeadCategory;

use App\Models\Db\LeadCategory;
use App\Services\BaseUuidModelService;
use App\Services\LeadCategory\LeadCategoryCriteria;
use Carcosa\Core\Service\iServiceResult;
use Carcosa\Core\Service\NoCriterion;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A service for managing lead category records.
 * @author Randall Betta
 *
 */
class LeadCategoryService extends BaseUuidModelService
{
    
    /**
     * Create a new Lead Category instance.
     * @return Lead Category
     */
    public function createNew() : LeadCategory
    {
        return new LeadCategory();
    }
    
    /**
     * Find exactly one instance by its ID.
     * 
     * This is a convenience method.
     * @param string $id
     * @return LeadCategory
     */
    public function findOneById(string $id) : LeadCategory
    {
        $criteria = \App::make(LeadCategoryCriteria::class);
        $criteria->setId($id);
        return $this->findOne($criteria);
    }
    
    /**
     * Find exactly one instance.
     * @param LeadCategoryCriteria $criteria
     * @return LeadCategory
     */
    public function findOne(LeadCategoryCriteria $criteria) : LeadCategory
    {
        $results = $this->find($criteria);
        return $this->handleFindOne($results);
    }
    
    /**
     * Find zero or one model instance.
     * @param LeadCategoryCriteria $criteria
     * @return LeadCategory|null
     */
    public function findOneOrNone(LeadCategoryCriteria $criteria) : LeadCategory|null
    {
        $results = $this->find($criteria);
        return $this->handleFindOneOrNone($results);
    }
    
    
    /**
     * Find an arbitrary number of model instances.
     * @param LeadCategoryCriteria $criteria
     * @return LeadCategory[]
     */
    public function find(LeadCategoryCriteria $criteria) : array
    {
        // Retrieve the search criteria.
        $ids        = $criteria->getIds();
        $parentId   = $criteria->getParentId();
        
        // Create a query builer using the search criteria.
        $results = LeadCategory::query()
            
            // If we join with other tables later, then any identical
            // field names will be clobbered. Prevent that by forcing
            // retrieval of only fields from the table we want.
            ->select('lead_category.*')
            
            // Limit results to only the requested IDs (if any).
            ->when($ids, function ($q) use ($criteria) {
                $this->applyIdsToQueryBuilder($q, $criteria);
            })
            
            // Limit results by parent ID. Note that null is a valid value
            // used to retrieve root categories, so a NoCriterion instance
            // indicates this criterion is not set instead.
            ->when((! $parentId instanceof NoCriterion), function ($q) use ($parentId) {
                $q->when(
                    ($parentId),
                    fn($q) => $q->where('parent_id', $parentId),
                    fn($q) => $q->whereNull('parent_id')
                );
            })
            
            // Execute the query and return the results.
            ->get();

        return $results->all();
        
    }
    
    /**
     * Attempt to save a lead category record.
     * @param LeadCategory|array $leadCategory The lead category (or its field data as an array).
     * @return iServiceResult
     */
    public function save(LeadCategory|array $leadCategory) : iServiceResult
    {
        
        // Define the validator.
        //
        // Note: we explicitly retrieve the database connection name here
        // for use in validator rules. This allows us to segment our data
        // in different databases, in preparation for decomposition into
        // microservices.
        //
        $connectionName = (new LeadCategory())->getConnectionName();
        $data           = is_array($leadCategory) ? $leadCategory : $leadCategory->toArray();
        $leadCategoryId = $data['id'] ?? null;
        $leadCategory   = ("" !== "$leadCategoryId") ? $this->findOneById($leadCategoryId) : null;
        $parentId       = $data['parent_id'] ?? null;
        $validator      = ValidatorFacade::make(
            $data,
            [
                "id"            => "nullable|uuid",
                "parentId"      => "nullable|uuid|exists:$connectionName.lead_category,id",
                "label"         => [
                    "required",
                    "string",
                    Rule::unique("$connectionName.lead_category")
                        ->where(function($query) use ($parentId, $leadCategory) {
                            $query

                                // Label uniqueness only counts among tree siblings.
                                ->when(
                                    (null === $parentId),
                                    fn($q) => $q->whereNull('parent_id'),
                                    fn($q) => $q->where('parent_id', $parentId)
                                );
                        })
                                
                        // Deleted records can be duplicates.
                        ->whereNull('deleted_at')       
                        
                        // Ignore the record being edited.
                        //
                        // WARNING:
                        //      DO NOT use a user-supplied value here;
                        //      it is an injection vulnerability.
                        //
                        ->ignore($leadCategory?->id),
                        
                ],
            ], [
                "label.required"         => "This field is required.",
                "label.unique"           => "This label is already in use.",
            ]
        );
        
        // Validate the request and create a result instance.
        $result = $this->createServiceResult($data, $validator);

        // Construct the request contents.
        if ($result->getHasError()) {
            
            // Validation failed.
            $result->setValue('leadCategory', null);
            
        } else {
            
            // Validation succeeded. Instantiate the ORM model instance.
            $leadCategory = ('' === "$leadCategoryId")
                ? $this->createNew()
                : LeadCategory::findOrFail($leadCategoryId);
        
            // Update the database record.
            $leadCategory->label        = $data['label'];
            $leadCategory->parent_id    = $data['parentId'];
            $leadCategory->save();
            
            $result->setValue('leadCategory', $leadCategory);
            
        }
        
        return $result->toImmutable();
        
    }
    
}


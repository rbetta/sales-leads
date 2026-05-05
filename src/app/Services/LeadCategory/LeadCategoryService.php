<?php
declare(strict_types = 1);
namespace App\Services\LeadCategory;

use App\Models\Db\LeadCategory;
use App\Services\BaseUuidModelService;
use App\Services\LeadCategory\LeadCategoryCriteria;
use Carcosa\Core\DataStructures\TreeNodeFactory;
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
        $data           = is_array($leadCategory) ? $leadCategory : $leadCategory->toCamelCaseArray();
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
    
    /**
     * Attempt to soft-delete a lead category record.
     * @param array $deletionData An array containing a "leadCategoryIds"
     * key (an array indicating the lead categories to delete), and a
     * "childStrategy" key (a string indicating how to handle child nodes,
     * either "delete-children" or "promote-children").
     * @return iServiceResult
     * @todo Convert modification of children or further descendants into
     * a single SQL operation instead of an iteration. (Low priority, due to
     * limited tree size and rarity of invocation in the admin system.)
     * @todo Child strategies are not optimized; we should precalculate the
     * dependency graph among them to prevent potentially duplicative
     * promotions or deletions of children. (Low priority, due to
     * limited tree size and rarity of invocation in the admin system.)
     */
    public function delete(array $deletionData) : iServiceResult
    {
        
        // Define the validator.
        //
        // Note: we explicitly retrieve the database connection name here
        // for use in validator rules. This allows us to segment our data
        // in different databases, in preparation for decomposition into
        // microservices.
        //
        $connectionName = (new LeadCategory())->getConnectionName();
        $data           = $deletionData;
        $validator      = ValidatorFacade::make(
            $data,
            [
                "leadCategoryIds"   => "array",
                "leadCategoryIds.*" => "uuid|exists:$connectionName.lead_category,id",
                "childStrategy"     => "string|in:delete-children,promote-children",
            ], [
                // Any custom validation error messages go here.
            ]
        );
        
        // Validate the request and create a result instance.
        $result = $this->createServiceResult($data, $validator);

        // Construct the request contents.
        if ($result->getHasError()) {
            
            // Validation failed.
            $result->setValue('leadCategory', null);
            
        } else {
            
            // Validation succeeded.
            $leadCategoryIds    = array_unique($data['leadCategoryIds']);
            $childStrategy      = $data['childStrategy'];
            DB::transaction(function () use ($leadCategoryId, $childStrategy) {
                
                // Obtain the lead categories to delete.
                $criteria = \App::make(LeadCategoryCriteria::class);
                $criteria->setIds($leadCategoryIds);
                $leadCategoriesToDelete = $this->find($leadCategoryIds);
                
                // Handle the children (if any).
                if ('promote-children' === $childStrategy) {
                    
                    // Move all children up one level.
                    $criteria = \App::make(LeadCategoryCriteria::class);
                    $criteria->setParentId($leadCategoryId);
                    $children = $this->find($criteria);
                    foreach ($children as $child) {
                        
                        // Change the next child's parent and attempt to save it.
                        $child->parent_id = $leadCategoryToDelete->parent_id;
                        $childResult = $this->save($child);
                        if ($childResult->getHasError()) {
                            $childId = $child->id;
                            throw new \RuntimeException(
                                "Failed to update parent ID of child lead " .
                                "category $childId in " . __METHOD__
                            );
                        }
                        
                    }
                    
                } elseif ('delete-children' === $childStrategy) {
                    
                    // Delete all children and further descendants.
                    
                } else {
                    
                    // This should not happen, due to previous validation.
                    throw new \RuntimeException(
                        "The unrecognized child strategy \"$shildStrategy\" " .
                        "was encountered by " . __METHOD__
                    );
                    
                }
                
            });
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
    
    /**
     * Given an array of LeadCategory instance nodes, return an array
     * of subtrees where each root nodes is a supplied instance that
     * does not have another supplied instance as its parent.
     * 
     * This logic is used to identify which nodes must be promoted during
     * deletion of the supplied nodes, since any child node that must be
     * promoted can immediately be promoted to the parent ID of its subtree
     * root node.
     * @param TreeNode[] $nodesToDelete An array of TreeNode root node
     * instances arranged as subtrees, each containing a LeadCategory
     * instance as its value.
     * @return array LeadCategory The affected subtrees to modify.
     * @throws \RuntimeException If any value in the supplied array is not a
     * LeadCategory instance.
     */
    private function calculateSubtreesForChildPromotion(array $nodesToDelete) : array
    {
        
        // Validate the input array.
        $this->validateLeadCategoriesArray($nodesToDelete);
        
        $treeNodeFactory = \App::make(TreeNodeFactory::class);
        return $treeNodeFactory->createSubtreesFromValues(
            $nodesToDelete,
            fn(LeadCategory $category) => $category->id,
            fn(LeadCategory $category) => $category->parent_id,
        );
        
    }
    
    /**
     * Validate that an array contains only LeadCategory instances.
     * @param LeadCategory[] $leadCategories
     * @return $this
     * @throws \RuntimeException If any value in the array is not a
     * LeadCategory instance.
     */
    private function validateLeadCategoriesArray(array $leadCategories) : self
    {
        foreach ($leadCategories as $instance) {
            if ( ! $instance instanceof LeadCategory ) {
                $type = get_debug_type($instance);
                throw new \RuntimeException(
                    "An invalid value of type $type was supplied to " .
                    __METHOD__
                );
            }
        }
        return $this;
    }
    
}


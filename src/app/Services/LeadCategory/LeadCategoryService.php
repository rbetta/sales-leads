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
            
        } else {
            
            // Validation succeeded.
            $leadCategoryIds    = array_unique($data['leadCategoryIds']);
            $childStrategy      = $data['childStrategy'];
            DB::transaction(function () use ($leadCategoryIds, $childStrategy, $result) {
                
                // Obtain the lead categories to delete.
                $criteria = \App::make(LeadCategoryCriteria::class);
                $criteria->setIds($leadCategoryIds);
                $leadCategoriesToDelete = $this->find($leadCategoryIds);
                
                // Obtain an array of TreeNode instances, where each value
                // is the root of a subtree of lead categories to delete.
                $treesToDelete = $this->calculateSubtreesForChildPromotion($leadCategoriesToDelete);
                
                // Handle the children (if any).
                if ('promote-children' === $childStrategy) {
                    
                    // Update all children of all nodes in each subtree,
                    // setting their parent IDs to the parent ID of the
                    // subtree's root node. (This will automatically promote
                    // all affected children to the correct level.)
                    foreach ($treesToDelete as $treeToDelete) {
                        
                        // Obtain the root LeadCategory of this subtree.
                        $rootLeadCategory = $treeToDelete->getValue();
                        
                        // Obtain all model IDs in this subtree.
                        $treeIdsToDelete = $this->getIdsFromModels(
                            $treeToDelete->toFlattenedValuesArray()
                        );
                        
                        // Promote all children in this subtree.
                        DB::table('lead_category')
                            
                            // Select all children of deleted nodes.
                            ->whereIn('parent_id', $treeIdsToDelete)
                            
                            // Exclude the deleted nodes themselves.
                            ->whereNotIn('id', $treeIdsToDelete)
                            
                            ->update(['parent_id' => $rootLeadCategory->parent_id]);
                        
                    }
                    
                    // Delete all requested LeadCategory instances.
                    foreach ($leadCategoriesToDelete as $leadCategoryToDelete) {
                        $leadCategory->delete();
                    }
                    
                } elseif ('delete-children' === $childStrategy) {
                    
                    // Delete all children and further descendants.
                    foreach ($treesToDelete as $treeToDelete) {
                        
                        // Obtain the root LeadCategory of this subtree.
                        $rootLeadCategory = $treeToDelete->getValue();
                        
                        // Identify all lead category IDs in this subtree, including
                        // all descendants at all level (even those not explicitly
                        // included in the supplied list of lead categories to delete).
                        $results = DB::select('
                            WITH RECURSIVE tree_nodes AS (
                                
                                -- Select root node.
                                SELECT      id
                                FROM        lead_category
                                WHERE       id = :root_id

                                UNION ALL

                                -- Recursively join the table to the CTE.
                                SELECT      lc.id
                                FROM        lead_category lc
                                INNER JOIN  tree_nodes tn ON lc.parent_id = tn.id

                            )
                            SELECT id FROM tree_nodes',
                            ['root_id' => $rootLeadCategory->id]
                        );
                        
                        // Convert the lead category IDs into LeadCategory instances,
                        // and delete them.
                        //
                        // TO-DO: consider deleting in batch SQL updates for efficiency, rather than using Eloquent soft-delete operations.
                        //
                        $criteria = \App::make(LeadCategoryCriteria::class);
                        $criteria->setIds(array_map(fn($row) => $row->id, $results));
                        foreach ($this->find($criteria) as $leadCategory) {
                            $leadCategory->delete();
                        }
                        
                    }
                    
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
     * of TreeNode subtrees where each array value is the root of a
     * subtree of related LeadCategory instances.
     * @param LeadCategory[] $nodesToDelete An array of LeadCategory
     * instances to delete.
     * @return TreeNode[] An array of TreeNode instances, where each array
     * value is the root of a subtree of related LeadCategory values to
     * delete.
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


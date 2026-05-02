<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db\Tree;

use Carcosa\Core\Util\ListFormatter;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\FacadesDB;

/**
 * A trait that handles hierarchical data storage and retrieval
 * operations for an Eloquent model using the nested set algorithm.
 * @author Randall Betta
 *
 */
trait NestedSetTrait
{
    
    /**
     * A constant that defines the name of the global scope that excludes
     * tree nodes that have been removed (i.e. that excludes nodes in
     * the scratchspace).
     * @var string
     */
    private const SCOPE_WITHOUT_REMOVED_NODES = 'withoutRemovedNodes';
    
    /**
     * The field name that stores the "left" property for nested set
     * hierarchical storage.
     * @var string
     */
    private string $treeLeftIndexField = 'tree_left';
    
    /**
     * The field name that stores the "right" property for nested set
     * hierarchical storage.
     * @var string
     */
    private string $treeRightIndexField = 'tree_right';
    
    /**
     * The field name that stores the "parent" foreign key for nested set
     * hierarchical storage.
     * @var string
     */
    private string $treeParentIdField = 'tree_parent_id';
    
    /**
     * The field name that stores the depth of each node in the tree (for
     * nested set hierarchical storage).
     * @var int
     */
    private string $treeDepthField = 'tree_depth';
    
    /**
     * The field names used to partition the hierarchical storage indices.
     * On insertion or deletion of a record in the hierarchy, storage
     * indices will only be updated for records where these fields match
     * their corresponding values in the inserted or deleted node.
     * @var string[]
     */
    private array $partitionFields = [];
    
    /**
     * Set the field name that stores the "left" property.
     * @param string $field
     * @throws \InvalidArgumentException If the field name is the
     * empty string.
     */
    protected function setTreeLeftIndexField(string $field) : self
    {
        // Sanity check.
        if ('' === $field) {
            throw new \InvalidArgumentException(
                "No field name was supplied to " . __METHOD__
            );
        }
        
        $this->treeLeftIndexField = $field;
        return $this;
    }
    
    /**
     * Get the field name that stores the "left" property.
     * @return string
     */
    public function getTreeLeftIndexField() : string
    {
        return $this->treeLeftIndexField;
    }
    
    /**
     * Get this node's left tree index.
     * @return int|null
     */
    public function getTreeLeftIndex() : int|null
    {
        $field = $this->getTreeLeftIndexField();
        return $this->{$field};
    }
    
    /**
     * Set the field name that stores the "right" property.
     * @param string $field
     * @throws \InvalidArgumentException If the field name is the
     * empty string.
     */
    protected function setTreeRightIndexField(string $field) : self
    {
        // Sanity check.
        if ('' === $field) {
            throw new \InvalidArgumentException(
                "No field name was supplied to " . __METHOD__
            );
        }
        
        $this->treeRightIndexField = $field;
        return $this;
    }
    
    /**
     * Get the field name that stores the "right" property.
     * @return string
     */
    public function getTreeRightIndexField() : string
    {
        return $this->treeRightIndexField;
    }
    
    /**
     * Get this node's right tree index.
     * @return int|null
     */
    public function getTreeRightIndex() : int|null
    {
        $field = $this->getTreeRightIndexField();
        return $this->{$field};
    }
    
    /**
     * Set the field name that stores the parent foreign key.
     * @param string $field
     * @throws \InvalidArgumentException If the field name is the
     * empty string.
     */
    protected function setTreeParentIdField(string $field) : self
    {
        // Sanity check.
        if ('' === $field) {
            throw new \InvalidArgumentException(
                "No field name was supplied to " . __METHOD__
            );
        }
        
        $this->treeParentIdField = $field;
        return $this;
    }
    
    /**
     * Get the field name that stores the parent foreign key.
     * @return string
     */
    public function getTreeParentIdField() : string
    {
        return $this->treeParentIdField;
    }
    
    /**
     * Get this node's tree parent ID.
     * @return string|null
     */
    public function getTreeParentId() : string|null
    {
        $field = $this->getTreeParentIdField();
        return $this->{$field};
    }
    
    /**
     * Set the field name that stores the tree depth.
     * @param string $field
     * @throws \InvalidArgumentException If the field name is the
     * empty string.
     */
    protected function setTreeDepthField(string $field) : self
    {
        // Sanity check.
        if ('' === $field) {
            throw new \InvalidArgumentException(
                "No field name was supplied to " . __METHOD__
            );
        }
        
        $this->treeDepthField = $field;
        return $this;
    }
    
    /**
     * Get the field name that stores the tree depth.
     * @return string
     */
    public function getTreeDepthField() : string
    {
        return $this->treeDepthField;
    }
    
    /**
     * Get this node's tree depth.
     * @return int|null
     */
    public function getTreeDepth() : int|null
    {
        $field = $this->getTreeDepthField();
        return $this->{$field};
    }
    
    /**
     * Set the field names used to partition the hierarchical storage indices.
     * On insertion or deletion of a record in the hierarchy, storage
     * indices will only be updated for records where these fields match
     * their corresponding values in the inserted or deleted node.
     * @param string ...$fields
     */
    protected function setPartitionFields(string ...$fields) : self
    {
        $this->partitionFields = $fields;
    }
    
    /**
     * Get the field names used to partition the hierarchical storage indices.
     * On insertion or deletion of a record in the hierarchy, storage
     * indices will only be updated for records where these fields match
     * their corresponding values in the inserted or deleted node.
     * @return string[]
     */
    public function getPartitionFields() : array
    {
        return $this->partitionFields;
    }

    /**
     * Add this model as a root node in its hierarchical storage. Database
     * changes will take effect immediately, and will operate safely within
     * a transaction.
     * @param int|null $index The zero-indexed position to insert this node
     * into. If the index is null or exceeds the current number of root nodes
     * then the node will be appended to the end of all other root nodes.
     * @return $this
     * @throws \RuntimeException If the supplied index is negative.
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     * @todo Improve this algorithm's efficiency by only updating
     * tree indices between the new and previous positions if an existing
     * node is being moved within the tree. This will substantially reduce
     * extra database write operations.
     */
    public function addAsRoot(int|null $index) : self
    {
        return $this->addToParentOrAsRoot(null, $index);
    }
    
    /**
     * Add this model to a parent model in its hierarchical storage. Database
     * changes will take effect immediately, and will operate safely within
     * a transaction.
     * @param self $parent The parent model instance.
     * @param int|null $index The zero-indexed position to insert this node
     * into. If the index is null or exceeds the current number of children
     * belonging to the parent, then the node will be appended to the end of
     * all the parent's children.
     * @return $this
     * @throws \RuntimeException If the supplied parent node is not already
     * stored in the database.
     * @throws \RuntimeException If the parent node does not have the same
     * values for all partition fields as this instance.
     * @throws \RuntimeException If the supplied index is negative.
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     * @todo Improve this algorithm's efficiency by only updating
     * tree indices between the new and previous positions if an existing
     * node is being moved within the tree. This will substantially reduce
     * extra database write operations.
     */
    public function addToParent(self $parent, int|null $index) : self
    {
        return $this->addToParentOrAsRoot($parent, $index);
    }
    
    /**
     * Add this model either as a root node or a child node in its
     * hierarchical storage. Database changes will take effect immediately,
     * and will operate safely within a transaction.
     * @param self|null $parent The parent model instance to add this node
     * onto, or null if this will be a root node.
     * @param int|null $index The zero-indexed position to insert this node
     * into among its new siblings. If the index is null or exceeds the
     * number of new siblings, then the node will be appended to the end of
     * all the new siblings.
     * @return $this
     * @throws \RuntimeException If a parent node is supplied but it is
     * not yet stored in the database.
     * @throws \RuntimeException If a parent node is supplied but it does
     * not have the same values for all partition fields as this instance.
     * @throws \RuntimeException If a parent node is supplied but it has
     * been removed from the tree (i.e. it is in the scratchspace).
     * @throws \RuntimeException If the supplied index is negative.
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     * @todo Improve this algorithm's efficiency by only updating
     * tree indices between the new and previous positions if an existing
     * node is being moved within the tree. This will substantially reduce
     * extra database write operations.
     */
    private function addToParentOrAsRoot(self|null $parent, int|null $index) : self
    {
        
        // Sanity check: index cannot be negative.
        if ($index < 0) {
            throw new \InvalidArgumentException(
                "The negative index $index was supplied to " .
                __METHOD__ . " (expected: non-negative integer.)"
            );
        }
        
        // Perform sanity checks.
        $this->validateThisInstanceIsModel();
        if ($parent) {
            $this
                ->validateModelInstanceExists($parent)
                ->validatePartitionFieldValuesMatch($parent)
                ->validateModelInstanceIsInTree($parent);
        }
        
        // Perform all operations within a database transaction.
        DB::transaction(function () use ($parent, $index) {
            
            // Obtain all hierarchical information about the parent node.
            //
            // WARNING:
            //
            //  1:  We MUST obtain a write lock during this operation,
            //      in order to prevent race conditions.
            //
            //  2:  We will operate on a copy of the parent model instance,
            //      since reloading the parent's properties from the database
            //      might overwrite other fields on the parent model that
            //      have not yet been saved to the database and have nothing
            //      to do with its tree position data.
            //
            if (null === $parent) {
                $parentId   = null;
                $parentCopy = null;
            } else {
                $parentId   = $parent->getKey();
                $parentCopy = self::lockForUpdate()->find($parentId);
            }
            
            // If this node exists in the database, then obtain a
            // copy of this node.
            //
            // WARNING:
            //
            //      All cautions relating to the retrieval of the parent
            //      node also apply to this node.
            //
            if ($this->exists) {
                $thisCopy = self::lockForUpdate()
                    ->withRemovedNodes()
                    ->find($this->getKey());
            } else {
                // This node isn't yet in the database. We can operate
                // on it directly, since it only exists in memory so far.
                $thisCopy = $this;
            }
            
            // Obtain the field names for storing tree information.
            $leftField      = $this->getTreeLeftIndexField();
            $rightField     = $this->getTreeRightIndexField();
            $parentIdField  = $this->getTreeParentIdField();
            $depthField     = $this->getTreeDepthField();
            
            // Remove this instance and its subtree from the tree,
            // if it has not already been removed.
            //
            // WARNING:
            //
            //      This operation MUST occur before we calculate this
            //      node's new tree index values, since its removal
            //      will change the left index and right index values
            //      of all nodes after it in the tree traversal.
            //
            if ($this->getIsInTree()) {
                $this->removeSubtree();
            }
            
            // Retrieve all nodes that will be leftward siblings
            // to this node after it is moved.
            //
            // Note: if this node will be at index 0, then we can
            // skip this database call, since we know there will be
            // no siblings to its left.
            //
            if (0 === $index) {
                $newSiblingsOnLeft = collect([]);
            } else {
                $newSiblingsOnLeft = self::lockForUpdates()
                    ->when(
                        $parent,
                        function (Builder $q) {
                            // A new parent for this node was specified.
                            $q->where($parentField, $parentId);
                        },
                        function (Builder $q) {
                            // This node will be a root node with no parent.
                            $q->whereNull($parentField);
                        }
                    )
                    ->where($this->getPartitionFieldValues())
                    ->orderBy($leftField, 'asc')
                    ->when(null !== $index, function ($q) {
                        return $q->limit($index);
                    })
                    ->get();
            }
            
            // If the index is null or is too large, then adjust it so
            // this instance will be the last child of its new parent.
            if (null === $index || $index > count($siblings)) {
                $index = count($siblings);
            }
            
            // Determine the new left value for this node in its new
            // position in the tree.
            if ($index > 0) {
                
                // The new left index of this node is one more than
                // the right index value of its preceding new sibling.
                $newLeft = $newSiblingsOnLeft[$index - 1]->getTreeRightIndex();
                
            } elseif ($parent) {
            
                // This will be the new first sibling of its new parent node.
                // This instance's new left value will be its parent left
                // index value plus one.
                $newLeft = $parent->getTreeLeftIndex() + 1;
                
            } else {
                
                // This is the new leftmost root node.
                $newLeft = 0;
                
            }
            
            // Calculate this node's new tree depth.
            $newDepth = $parent ? ($parent->getDepth() + 1) : 0;
            
            // Obtain a class for quoting database identifiers.
            $dbIdentifier = $this->getDbIdentifier();
            
            // Identify how many index positions this node takes up.
            // The total number of nodes in this node's subtree will
            // be this number divided by 2.
            $thisLeft   = $thisCopy->getTreeLeftIndex();
            $thisRight  = $thisCopy->getTreeRightIndex();
            if (null !== $thisLeft && null !== $thisRight) {
                
                // This node's indices are already defined.
                // Note: this math works correctly with the
                // negative indices used in removed nodes (i.e.
                // nodes present in the scratchspace).
                $absMin = min(abs($thisLeft), abs($thisRight));
                $absMax = max(abs($thisLeft), abs($thisRight));
                $thisIndexWidth = $absMax - $absMin + 1;
                
            } else {
                
                // This node's indices are not yet defined. It
                // must be a single node with no children.
                $thisIndexWidth = 2;
                
            }
            
            // Calculate this node's new right index.
            $newRight = $newLeft + $indexWidth - 1;
            
            // Make a space for this node among its new siblings.
            $escLeftField   = $dbIdentifier($leftField);
            $escRightField  = $dbIdentifier($rightField);
            $escDepthField  = $dbIdentifier($depthField);
            DB::table($this->table)
                ->where($this->getPartitionFieldValues())
                ->where($leftField, '>=', $newLeft)
                ->update([
                    $leftField => DB::raw($escLeftField + $thisIndexWidth),
                ]);
            DB::table($this->table)
                ->where($this->getPartitionFieldValues())
                ->where($rightField, '>', $newLeft)
                ->update([
                    $rightField => DB::raw($escRightField + $thisIndexWidth),
                ]);
            
            // Update this node and its entire subtree, placing it into
            // its new position in the tree. This will move it from the
            // scratchspace into the empty range in the left and right indices
            // that were opened up in the preceding step.
            if ($this->exists) {
                
                // This node is already in the database. Update its left and
                // right indices to move it out of the scratchspace and into
                // its new position in the tree.
                $changeLeftIndexBy  = $newLeft  - $thisCopy->getTreeLeftIndex();
                $changeRightIndexBy = $newRight - $thisCopy->getTreeRightIndex();
                DB::table($this->table)
                    ->where($this->getPartitionFieldValues())
                    ->where($leftField, '>=', $thisCopy->getTreeLeftIndex())
                    ->where($leftField, '<=', $thisCopy->getTreeRightIndex())
                    ->update([
                        $leftField  => DB::raw("$escLeftField + $changeLeftIndexBy"),
                        // Also update the subtree's depth while we're here.
                        $depthField => DB::raw("$escDepthField + $newDepth")
                    ]);
                
                DB::table($this->table)
                    ->where($this->getPartitionFieldValues())
                    ->where($rightField, '>=', $thisCopy->getTreeLeftIndex())
                    ->where($rightField, '<=', $thisCopy->getTreeRightIndex())
                    ->update([
                        $rightField => DB::raw("$escRightField + $changeRightIndexBy")
                    ]);
                
                // Update this node's parent ID.
                DB::table($this->table)
                    ->where($this->getKeyName(), $this->getKey())
                    ->update([
                        $parentIdField => $parent?->getKey()
                    ]);
                $this->save();
                
            } else {
                
                // This node is not yet in the database. Put it into
                // its new position in the tree and save it to the database.
                $this->setTreeLeftIndex($newLeft);
                $this->setTreeRightIndex($newRight);
                $this->setTreeDepth($newDepth);
                $this->setTreeParentId();
                $this->save();
                
            }
            
        });
        
        return $this;
        
    }
    
    /**
     * Remove this instance and all of its children from their current
     * positions in the hierarchy, moving them into a scratchspace.
     * This will immediately update the database, and will do so properly
     * and safely within a transaction.
     * @return $this
     * @throws \RuntimeException If this node is not already stored in the
     * database.
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     * @throws \RuntimeException If this node is not already in the tree
     * (i.e. if it is already in the scratchspace).
     */
    private function removeSubtree() : self
    {
        
        // Sanity checks:
        $this
            ->validateThisInstanceIsModel()
            ->validateModelInstanceExists($this)
            ->validateModelInstanceIsInTree($this);
        
        // Perform all database operations within a transaction.
        DB::transaction(function () {
            
            // Obtain a clone of this instance within the transaction.
            // This will prevent certain race conditions from occurring.
            $thisClone = self::lockForUpdates()
                ->withRemovedNodes()
                ->find($this->getKey());
            
            // Get the field names for the left and right tree indices,
            // as well as the tree depth.
            $leftField      = $thisClone->getTreeLeftIndexField();
            $rightField     = $thisClone->getTreeRightIndexField();
            $depthField     = $thisClone->getTreeDepthField();
            $parentIdField  = $thisClone->getTreeParentIdField();
            
            // Obtain this instance's current tree position data.
            $oldLeft        = $thisClone->getTreeLeftIndex();
            $oldRight       = $thisClone->getTreeRightIndex();
            $oldDepth       = $thisClone->getTreeDepth();
            $oldParentId    = $thisClone->getTreeParentId();
            
            // Obtain a class for quoting database identifiers.
            $dbIdentifier = $this->getDbIdentifier();
            
            // Detach this node from its parent.
            $thisClone->setTreeParentId(null);
            $thisClone->save();
            
            // Identify the current lowest left tree index in the scratchspace.
            // Note: this may be null if no nodes are in the scratchspace.
            $leftmostRootNodeInScratchpad = self::lockForUpdates()
                ->withRemovedNodesOnly()
                ->where($this->getPartitionFieldValues())
                ->orderBy($leftField, 'asc')
                ->limit(1)
                ->get()
                ->first();
            
            // Translate this subtree's indices into the scratchspace,
            // and translate its starting depth to 0.
            //
            // This subtree will occupy a new root node in the scratchspace.
            // Its left and right values will be translated sufficiently
            // to allow it to sit next to any existing root subtrees that
            // are already there. This will allow us to move more than one
            // subtree into the scratchspace at the same time.
            //
            // Note that all left and right values are preserved relative
            // to all other left and right values in the subtree, though
            // they will all now be negative.
            //
            $lowestLeft     = $leftmostRootNodeInScratchpad?->getTreeLeftIndex() ?? 0;
            $subtract       = ($oldRight + abs($lowestLeft)) + 1;
            $escLeftField   = $dbIdentifier($leftField);
            $escRightField  = $dbIdentifier($rightField);
            $escDepthField  = $dbIdentifier($depthField);
            DB::update($this->table)
                ->where($this->getPartitionFieldValues())
                ->where($leftField, '>=', $oldLeft)
                ->where($rightField, '<=', $oldRight)
                ->update([
                    $leftField  => DB::raw($escLeftField - $subtract),
                    $rightField => DB::raw($escRightField - $subtract),
                    $depthField => DB::raw($escDepthField - $oldDepth),
                ]);
            
            // Update all elements with higher tree indices than this node's
            // original values, decreasing their values to fill the space
            // previously taken by this node and its subtree.
            //
            // Note: we must update the left and right values SEPARATELY,
            // since the logic differs between the removed subtree's
            // ancestor and sibling nodes.
            //
            $subtract       = $oldRight - $oldLeft + 1;
            $escLeftField   = $dbIdentifier($leftField);
            $escRightField  = $dbIdentifier($rightField);
            DB::update($this->table)
                ->where($this->getPartitionFieldValues())
                ->where($leftField, '>', $oldRight)
                ->update([
                    $leftField  => DB::raw($escLeftField - $subtract),
                ]);
            DB::update($this->table)
                ->where($this->getPartitionFieldValues())
                ->where($rightField, '>', $oldRight)
                ->update([
                    $rightField => DB::raw($escRightField - $subtract),
                ]);

        });
        
        return $this;
        
    }
    
    /**
     * Get all tree partition field names and values as an associative array.
     * @return array An array whose keys are partition field names and whose
     * values are their corresponding field values for this instance. 
     */
    public function getPartitionFieldValues() : array
    {
        $result = [];
        foreach ($this->getPartitionFields() as $field) {
            $results[$field] = $this->{$field};
        }
        return $results;
    }
    
    /**
     * Validate that an instance of this model is already stored in the
     * database.
     * @param self $instance
     * @return $this
     * @throws \RuntimeException If the supplied model instance is not already
     * stored in the database.
     */
    private function validateModelInstanceExists(self $instance) : self
    {
        // Throw an exception if the supplied instance is not already in
        // the database.
        if (! $parent->exists) {
            throw new \RuntimeException(
                "The parent model instance supplied to ". __METHOD__ . " is " .
                "not already stored in the database."
            );
        }
        
        // Return $this for method chaining.
        return $this;
    }
    
    /**
     * Get whether this model instance is in its hierarchical tree (not in
     * the scratchspace).
     * @return bool
     */
    private function getIsInTree() : bool
    {
        
        // Obtain this instance's positional data for the tree.
        $left   = $this->getTreeLeftIndex();
        $right  = $this->getTreeRightIndex();
        $depth  = $this->getTreeDepth();
        
        // Identify whether this instance is in its hierarchical
        // tree (i.e. not in th scratchspace).
        return (
                null !== $left
            &&  null !== $right
            &&  null !== $depth
            &&  $left >= 0
            &&  $right >= 0
            &&  $depth >= 0
            &&  $left < $right
        );
        
    }
        
    /**
     * Validate that an instance of this model has a valid location in its
     * hierarchical tree (not in the scratchspace).
     * @param self $instance
     * @return $this
     * @throws \RuntimeException If the supplied model instance does not
     * have a valid location in its hierarchical tree (not in the
     * scratchspace).
     */
    private function validateModelInstanceIsInTree(self $instance) : self
    {
        
        // Throw an exception if the supplied instance does not occupy
        // a valid location in the tree (not in the scratchspace).
        if ( ! $instance->getIsInTree() ) {
            
            // Obtain the instance's positional data for the tree.
            $left   = $instance->getTreeLeftIndex();
            $right  = $instance->getTreeRightIndex();
            $depth  = $instance->getTreeDepth();
            $class  = get_class($instance);
            
            // Throw the exception.
            $listFormatter = \App::make(ListFormatter::class);
            throw new \RuntimeException(
                "The $class instance supplied to ". __METHOD__ . " does " .
                "not have a valid location in its tree (" .
                $listFormatter->setEnclosure("")->format([
                    "ID: " . $instance->getKey() ?? "[NONE]",
                    "Left: $left",
                    "Right: $right",
                    "Depth: $depth",
                ]) . ")."
            );
            
        }
        
        // Return $this for method chaining.
        return $this;
    }
    
    /**
     * Validate that a model of this class has the same partition field
     * values as this instance does.
     * @param self $instance
     * @return $this
     * @throws \RuntimeException If any partition field values are not
     * identical between this instance and the supplied instance.
     */
    private function validatePartitionFieldValuesMatch(self $instance) : self
    {
        
        // Iteratively verify that each partition field value is identical
        // between this instance and the supplied instance.
        $partitionFields = $this->getPartitionFields();
        foreach ($partitionFields as $field) {
            
            // Retrieve the next partition field for each instance.
            $thisValue  = $this->{$field};
            $otherValue = $instance->{$field};
            
            // Verify the field values are identical.
            if ($thisValue !== $otherValue) {
                $thisClass  = get_class($this);
                $otherClass = get_class($instance);
                $thisType   = get_debug_type($thisValue);
                $otherType  = get_debug_type($otherValue);
                throw new \RuntimeException(
                    "The $field field value differs between the supplied " .
                    "$otherClass instance and the executing $thisClass " .
                    "instance in " . __METHOD__ . " (supplied instance " .
                    "value of type $otherType: \"$otherValue\", executing " .
                    "instance value of type $thisType: \"$thisValue\")."
                );
            }

        }
        
        // Return this instance for method chaining.
        return $this;
    }
    
    /**
     * Validate that this trait is executing on an Eloquent  Model instance.
     * @return $this
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     */
    private function validateThisInstanceIsModel() : self
    {
        if (! $this instanceof Model) {
            throw new \RuntimeException(
                "Attempted to invoke trait method " . __METHOD__ . " on an " .
                "invalid class instance (expected: " . Model::class . ")."
            );
        }
        return $this;
    }
    
    /**
     * Validate that this trait is executing on a class instance that
     * implemens the SoftDeletes interface.
     * @return $this
     * @throws \RuntimeException If this trait's implementing class
     * does not implement the SoftDeletes interface.
     */
    private function validateThisInstanceHasSoftDeletion() : self
    {
        if (! $this instanceof SoftDeletes) {
            throw new \RuntimeException(
                "Attempted to invoke trait method " . __METHOD__ . " on a " .
                "class instance that does not support soft deletion."
            );
        }
        return $this;
    }

    /**
     * Get a DbIdentifier instance for this model instance's connection.
     * @return DbIdentifier
     * @throws \RuntimeException If this trait's implementing class
     * is not an Eloquent model instance.
     */
    private function getDbIdentifier() : DbIdentifier
    {
        // Sanity check: this can't execute if this instance is not an
        // Eloquent Model instance.
        $this->validateThisInstanceIsModel();
        
        // Get this instance's connection name.
        $connectionName = $this->connection;
        
        // Obtain a class for quoting database identifiers.
        $dbIdentifierFactory = \App::make(DbIdentifierFactory::class);
        return $dbIdentifier = $dbIdentifierFactory->createByConnectionName(
            $connectionName
        );
        
    }
    
    /**
     * Add a global model scope that, by default, does not return tree
     * nodes that have been removed from the tree (and are therefore in
     * the scratchspace).
     */
    protected static function booted() : void
    {
        // By default, automatically exclude any hierarchical tree nodes
        // that are in the tree's scratchspace.
        $scopeName = self::SCOPE_WITHOUT_REMOVED_NODES;
        static::addGlobalScope($scopeName, function (Builder $q) {
            $q->where($this->getTreeLeftIndexField(), '>=', 0);
        });
    }
    
    /**
     * Define a scope for retrieving only tree nodes that have been
     * removed from the tree (and are therefore in the scratchspace).
     * @param Builder $q A query builder to apply this scope to.
     * @return Builder
     */
    #[Scope]
    protected function withRemovedNodesOnly(Builder $q) : Builder
    {
        return $q
            ->withoutGlobalScope(self::SCOPE_WITHOUT_REMOVED_NODES)
            ->where($this->getTreeRightIndexField(), '<', 0);
    }
    
    /**
     * Define a scope that allows queries to return tree nodes
     * regardless of whether they have been removed or not (i.e.
     * regardless of whether they are in the scratchspace).
     * @param Builder $q A query builder to apply this scope to.
     * @return Builder
     */
    #[Scope]
    protected function withRemovedNodes(Builder $q) : Builder
    {
        return $q->withoutGlobalScope(self::SCOPE_WITHOUT_REMOVED_NODES);
    }
    
}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataStructures;


/**
 * A factory for instantiating TreeNode instances.
 */
class TreeNodeFactory
{
    
    /**
     * Create a TreeNode instance.
     * @param mixed $value The value contained in the TreeNode instance.
     * @param TreeNode|null The new TreeNode instance's parent (if any).
     * @throws \RuntimeException If a TreeNode instance is supplied as the
     * node's initial value itself.
     * @return TreeNode
     */
    public function create($value, TreeNode|null $parent = null) : TreeNode
    {
        return new TreeNode($value, $parent);
    }
    
    /**
     * Create an array of TreeNode subtrees from an array of values that
     * have defined relationships to their parents.
     * @param iterable $values An array of values to wrap in TreeNode
     * instances.
     * @param \Closure $getId A closure that accepts a value as its only
     * argument, and returns that value's unique ID as a string or integer.
     * @param \Closure $getParentId A closure that accepts a value as its
     * only argument, and returns that value's parent ID as a string,
     * integer, or null (where null indicates the value has no parent).
     * @return TreeNode[] An array of TreeNode instances, each of which
     * is a root node for a subtree of supplied values' TreeNode wrappers.
     * Note that a root node in the returned array may have a non-null
     * parent ID, indicating that it does have a parent but its parent
     * was not in the supplied array of values.
     * @throws \RuntimeException If any supplied value is a TreeNode
     * instance itself.
     * @throws \RuntimeException If a closure returns a value ID that
     * is neither a string nor integer.
     * @throws \RuntimeException If a closure returns a parent ID that
     * is not a string, integer, or null.
     */
    public function createSubtreesFromValues(
        iterable $values,
        \Closue $getId,
        \Closure $getParentId
    ) : array
    {
        
        // Initialize a flat array of all supplied values, keyed by
        // their IDs and wrapped in TreeNode instances.
        $allNodesById = $values
            ->all()
            ->keyBy($this->wrapIdGetter($getId))
            ->map(fn($value) => $this->create($value));
        
        // Wrap the closure that obtains a value's parent ID, so we
        // can enforce type safety on its return value.
        $getParentId = $this->wrapParentIdGetter($getParentId);
            
        // Attach each node to its parent (if its parent is in the
        // supplied array of nodes).
        foreach ($allNodesById as $nodeToAttach) {
            
            // Obtain the next node's parent ID.
            $parentId = $getParentId($nodeToAttach);
            
            if (array_key_exists($parentId, $allNodesById)) {
                
                // The parent node is in the array of all supplied nodes.
                // Attach the child node to the parent node.
                $parentNode = $allNodesById[$parentId];
                $nodeToAttach->setParent($parentNode);
            }
            
        }
        
        // Create an array of root subtree nodes.
        $rootNodes = [];
        foreach ($allNodesById as $node) {
            if ( ! $node->getHasParent() ) {
                $rootNodes[] = $node;
            }
        }
        
        return $rootNodes;
    }
    
    /**
     * Given a closure to obtain a unique ID from a value, return a
     * wrapper that guarantees either a string or integer as its
     * return type.
     * @param \Closure $getId A closure that accepts a single value as
     * its argument, and returns its unqiue ID as a string or integer.
     * @return \Closure
     * @throws \RuntimeException If a closure returns a value ID that
     * is neither a string nor integer.
     */
    private function wrapIdGetter(\Closure $getter) : \Closure
    {
        return function ($value) use ($getter) {
            
            // Invoke the closure to retrieve a value's ID.
            $id = $getter($value);
            
            // Validate the closure's return value.
            if (! (is_int($id) || is_string($id) ) ) {
                
                // The closure returned an invalid data type.
                $type = get_debug_type($id);
                throw new \RuntimeException(
                    "The ID retrieval closure supplied to " .
                    __METHOD__ . " returned an invalid value of " .
                    "type $type (expected: integer or string)."
                );
                
            }
            return $id;
        };
    }
    
    /**
     * Given a closure to obtain a parent ID from a value, return a
     * wrapper that guarantees a string, integer, or null as its
     * return type.
     * @param \Closure $getter A closure that accepts a single
     * value as its argument, and returns its parent ID as a string,
     * integer, or null.
     * @return \Closure
     * @throws \RuntimeException If a closure returns a value ID that
     * is not a string, integer, or null.
     */
    private function wrapParentIdGetter(\Closure $getter) : \Closure
    {
        return function ($value) use ($getter) {
            
            // Invoke the closure to retrieve a value's parent ID.
            $parentId = $getter($value);
            
            // Validate the closure's return value.
            if (! (
                is_int($parentId)       ||
                is_string($parentId)    ||
                is_null($parentId)
            ) ) {
                
                // The closure returned an invalid data type.
                $type = get_debug_type($parentId);
                throw new \RuntimeException(
                    "The parent ID retrieval closure supplied to " .
                    __METHOD__ . " returned an invalid value of " .
                    "type $type (expected: integer, string, or null)."
                );
                
            }
            return $parentId;
        };
    }
    
}

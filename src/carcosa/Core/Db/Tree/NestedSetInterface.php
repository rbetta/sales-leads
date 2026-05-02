<?php
declare(strict_types = 1);

namespace Carcosa\Core\Db\Tree;

/**
 * An interface that handles hierarchical data storage and retrieval
 * operations for an Eloquent model using the nested set algorithm.
 * @author Randall Betta
 *
 */
interface NestedSetInterface
{
    
    /**
     * Get the field name that stores the "left" property for nested set
     * hierarchical storage.
     * @return string
     */
    public function getTreeLeftIndexField() : string;
    
    /**
     * Get the field name that stores the "right" property for nested set
     * hierarchical storage.
     * @return string
     */
    public function getTreeRightIndexField() : string;
    
    /**
     * Get the field name that stores the "parent" foreign key for nested set
     * hierarchical storage.
     * @return string
     */
    public function getTreeParentIdField() : string;
    
    /**
     * Get the field name that stores the tree depth for nested set
     * hierarchical storage.
     * @return string
     */
    public function getTreeDepthField() : string;
    
    /**
     * Get this node's left tree index.
     * @return int|null
     */
    public function getTreeLeftIndex() : int|null;
    
    /**
     * Get this node's right tree index.
     * @return int|null
     */
    public function getTreeRightIndex() : int|null;
    
    /**
     * Get this node's parent node ID.
     * @return string|null
     */
    public function getTreeParentId() : string|null;
    
    /**
     * Get this node's tree depth.
     * @return int|null
     */
    public function getTreeDepth() : int|null;
    
}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataStructures;


/**
 * A class that represents a tree node containing arbitrary data.
 */
class TreeNode
{
    
    /**
     * A reference to the parent tree node (if any).
     * @var TreeNode|null
     */
    private TreeNode|null $parent = null;
    
    /**
     * An array of the child tree nodes (if any).
     * @var TreeNode[]
     */
    private array $children = [];
    
    /**
     * The value in this tree node.
     * @var mixed
     */
    private $value;
    
    /**
     * Construct an instance of this class.
     * @param mixed $value The value contained in the new tree node instance.
     * @param TreeNode|null The new tree node's parent (if any).
     * @throws \RuntimeException If a TreeNode instance is supplied as this
     * node's initial value.
     */
    public function __construct($value, TreeNode|null $parent = null)
    {
        $this->setValue($value);
        $this->setParent($parent);
    }
    
    /**
     * An overrideable method that can perform validation prior to
     * adding a value to this instance (e.g. to enforce type checking).
     * @param mixed $value The value to validate.
     * @return void
     */
    protected function validateValue($value) : void
    {
        return;
    }
    
    /**
     * Set this node's value.
     * @param mixed $value
     * @return $this
     * @throws \RuntimeException If a TreeNode instance is supplied as this
     * node's initial value.
     */
    public function setValue($value) : self
    {
        
        // Sanity check; the node's value cannot be a TreeNode itself.
        if ($value instanceof self) {
            $type = get_debug_type($value);
            throw new \RuntimeException(
                "An invalid value of type $type was supplied to " . __METHOD__
            );
        }
        
        // Validate the value.
        // (This does nothing here, but subclasses may override this method.)
        $this->validateValue($value);
        
        // Set the value.
        $this->value = $value;
        return $this;
        
    }
    
    /**
     * Get this node's value.
     * @return mixed
     */
    public function getValue() : mixed
    {
        return $this->value;
    }
    
    /**
     * Set this node's parent node. This will automatically add it
     * to the new parent's children.
     * @param TreeNode|null $parent
     * @return $this
     */
    public function setParent(TreeNode|null $parent) : self
    {
        // Obtain this node's parent prior to setting it to
        // the newly supplied value.
        $prevParent = $this->getParent();
        
        // Remove this node from the parent's array of children.
        if (null !== $prevParent) {
            $prevParent->removeChild($this);
        }
        
        // Attach this node to the new parent, or make it a root node.
        if (null === $parent) {
            
            // This is a new root node.
            $this->parent = null;
            
        } else {
            
            // This node is being made a child of another existing node.
            $parent->addChild($this);
            
        }
        
        return $this;
        
    }
    
    /**
     * Get this node's parent.
     * @return TreeNode|null
     */
    public function getParent() : self|null
    {
        return $this->parent;
    }
    
    /**
     * Ascertain whether the specified child is already a child of
     * this node.
     * @param TreeNode|mixed $child If this is a TreeNode instance,
     * then its presence will be checked among this node's children.
     * If it is any other value, then it will be checked among
     * this node's children's values instead.
     * @return bool
     */
    public function getHasChild($child) {
        
        $childValueToRemove = $child->getValue();
        foreach ($this->getChildren() as $existingChild) {
            
            // Check if the current child in this iteration
            // matches the supplied child, either by comparing
            // TreeNode instances or by comparing their values.
            $found = ($child instanceof self)
                ? ($child === $existingChild)
                : ($childValueToRemove === $existingChild->getValue());
            
            if ($found) {
                return true;
            }
            
        }
        return false;
        
    }
    
    /**
     * Ascertain whether this node has a parent node.
     * @return bool
     */
    public function getHasParent() : bool
    {
        return (null === $this->getParent());
    }
    
    /**
     * Add a child to this node. This will also automatically set the
     * child's parent to this node, and will remove it from its previous
     * parent (if any).
     * @param TreeNode|mixed $child The child to add to this node.
     * If a TreeNode instance is supplied, it will be added directly,
     * and removed from its previous parent (if any). If any other
     * value is supplied, it will be wrapped in a TreeNode instance
     * first (or whatever subclass this instance is, as appropriate).
     * @return $this
     * @throws \RuntimeException If a TreeNode is supplied as a child,
     * but that instance is already a child of this node.
     */
    public function addChild($child) : self
    {
        
        if ($child instanceof self) {
            
            // A TreeNode was supplied. Ensure it is not already a child
            // of this node.
            if ($this->getHasChild($child)) {
                $type = get_debug_type($child);
                throw new \RuntimeException(
                    "An existing child of type $type was supplied to " .
                    __METHOD__
                );
            }
            
            // Remove the child from its previous parent (if any).
            $prevParent = $child->getParent();
            if (null !== $prevParent) {
                $prevParent->removeChild($child);
            }
            
            // Add the child to this node's children.
            $thild->setParent($this);
            
        } else {
            
            // A raw value was supplied. Wrap it in a new TreeNode instance.
            $newChild = new (get_class($this))($child->getValue());
            
            // Since this is a completely new TreeNode, we can safely
            // immediately add it to this node's children.
            $newChild->setParent($this);
            $this->children[] = $newChild;
            
        }
        
        return $this;
        
    }
    
    /**
     * Remove a child from this node.
     * @param TreeNode|mixed $child The child to remove from this node.
     * If this is a TreeNode, then the specified node will be removed.
     * If it is any other data type, then the child TreeNode containing
     * the specified value will be removed.
     * @return $this
     * @throws \RuntimeException If the specified child is not actually
     * a child of this node.
     */
    public function removeChild($child) : self
    {
        
        // Locate the specified child among this node's children.
        $childValueToRemove = $child->getValue();
        foreach ($this->getChildren() as $key => $existingChild) {
            
            // Determine if the current child in the iteration
            // is the one we're looking for.
            $removeChild = ($child instanceof self)
                ? ($child === $existingChild)
                : ($childValueToRemove === $existingChild->getValue());
            
            if ($removeChild) {
                
                // The specified child is really a child on this node.
                // Remove it from this node's children.
                unset($this->children[$key]);
                
                // Re-index this node's children.
                $this->children = array_values($this->children);
                
                // Set the deleted child TreeNode's parent to null.
                //
                // WARNING:
                //
                //      Do not invoke setParent() to do this, since this
                //      would create an infinite loop.
                //
                $existingChild->parent = null;
                
                return $this;
                
            }
            
        }
        
        // The specified child was not found among this node's children.
        $type = get_debug_type($child);
        throw new \RuntimeException(
            "A nonexistent child of type $type was supplied to " . __METHOD__
        );
        
    }
    
    /**
     * Get this node's children.
     * @return TreeNode[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }
    
    /**
     * Return the values from this tree after flattening its structure.
     * @return mixed[] An array of all values in this instance's nodes.
     */
    public function toFlattenedValuesArray() : array
    {
        $results = [$this->getValue()];
        foreach ($this->getChildren() as $child) {
            $results = array_merge($results, $child->toFlattenedValues());
        }
        return $results;
    }
    
}

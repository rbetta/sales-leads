<?php
declare(strict_types = 1);
namespace App\View\Components;

use App\Models\Db\LeadCategory as LeadCategoryModel;
use Carcosa\Core\DataStructures\TreeNode;
use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * A component for rendering a lead category tree element.
 * @author Randall Betta
 *
 */
class LeadCategory extends Component
{
    
    /**
     * The TreeNode containing a LeadCategory instance to render.
     * @var TreeNode
     */
    public TreeNode $leadCategory;
    
    /**
     * Construct an instance of this class.
     * @param TreeNode $treeNode The TreeNode instance to render.
     * @throws \RuntimeException If the supplied TreeNode instance
     * does not have a LeadCategory as its value.
     */
    public function __construct(TreeNode $leadCategory)
    {
        // Ensure the supplied TreeNode contains a LeadCategory instance.
        $value = $leadCategory->getValue();
        if ( ! $value instanceof LeadCategoryModel ) {
            
            $type = get_debug_type($value);
            $expected = LeadCategoryModel::class;
            throw new \RuntimeException(
                "A tree node containing a value of type $type was supplied to " .
                __METHOD__ . " (expected: a contained value of type $expected)."
            );
            
        }
        
        $this->leadCategory = $leadCategory;
    }

    /**
     * Render this component.
     * @return View
     */
    public function render() : View
    {
        return view('components.lead-category');
    }
    
}

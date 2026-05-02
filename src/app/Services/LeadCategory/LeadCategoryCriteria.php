<?php
declare(strict_types = 1);
namespace App\Services\LeadCategory;

use Carcosa\Core\Service\AbstractUuidModelCriteria;
use Carcosa\Core\Service\NoCriterion;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A class that represents criteria for selecting one or more LeadCategory instances.
 * @author Randall Betta
 *
 */
class LeadCategoryCriteria extends AbstractUuidModelCriteria
{
    
    /**
     * The parent ID. Null implies retrieval of root categories.
     * @var string|null|NoCriterion
     */
    private string|null|NoCriterion $parentId;
    
    /**
     * Construct an instance of this class.
     */
    public function __construct()
    {
        $this->parentId = new NoCriterion();
    }
    
    /**
     * Set the parent ID. Null implies retrieval of root categories.
     * @param string|null|NoCriterion $parentId
     * @return $this
     */
    public function setParentId(string|null|NoCriterion $parentId) : self
    {
        $this->parentId = $parentId;
        return $this;
    }
    
    /**
     * Get the parent ID. Null implies retrieval of root categories.
     * @return string|null|NoCriterion
     */
    public function getParentId() : string|null|NoCriterion
    {
        return $this->parentId;
    }
    
}

<?php
declare(strict_types=1);
namespace App\Models\DataObjects\LeadCategory;

use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents a lead category.
 */
class LeadCategoryData extends AbstractSoftDeletableTimestampedUuid
{
    
    /**
     * Set the parent ID.
     * @param string|null $id
     * @return $this
     */
    public function setParentId(string|null $id) : self
    {
        return $this->setProperty("parentId", $id);
    }
    
    /**
     * Get the parent ID.
     * @return string|null
     */
    public function getParentId() : string|null
    {
        return $this->getProperty("parentId");
    }
    
    /**
     * Set the human-readable label.
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label) : self
    {
        return $this->setProperty("label", $label);
    }
    
    /**
     * Get the human-readable label.
     * @return string
     */
    public function getLabel() : string
    {
        return $this->getProperty("label");
    }
    
}

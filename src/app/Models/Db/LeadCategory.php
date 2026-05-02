<?php
declare(strict_types = 1);

namespace App\Models\Db;

use App\Models\DataObjects\LeadCategory\LeadCategoryDataFactory;
use Carcosa\Core\DataObjects\iDataObject;
use Carcosa\Core\DataObjects\iToDataObject;
use Carcosa\Core\Db\TimestampedSoftDeletableUuidModel;
use Carcosa\Core\Db\tToDataObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadCategory extends TimestampedSoftDeletableUuidModel implements iToDataObject
{
    
    use tToDataObject;
    
    /**
     * Define the table name.
     * @var string
     */
    protected $table = 'lead_category';
    
    /**
     * Get the attributes that should be cast.
     *
     * @return string[] An array whose keys are field names, and whose
     * values are casting instructions for the Eloquent ORM framework.
     */
    protected function casts() : array
    {
        return array_merge(
            parent::casts(),
            []
        );
    }
    
    /**
     * Get an instance of the factory class used to create this
     * model instance's associated data object (which must
     * implement the iDataObject interface).
     * @return LeadCategoryDataFactory
     */
    protected function getDataObjectFactory() : LeadCategoryDataFactory
    {
        return \App::make(LeadCategoryDataFactory::class);
    }
    
    /**
     * Assign custom properties to the data object created using the
     * toDataObject() method. Subclasses should override this method
     * if they have properties to assign.
     * @param iDataObject $dataObject
     * @return self
     */
    protected function assignDataObjectProperties(iDataObject $dataObject) : self
    {
        $dataObject
            ->setLabel(     $this->label        )
            ->setParentId(  $this->parent_id    )
            ;
        
        return $this;
    }
    
    /**
     * Get the relationship to the parent lead category.
     * @return BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->parent(LeadCategory::class, 'parent_id');
    }
    
    /**
     * Get the relationship to the child lead categories.
     * @return HasMany
     */
    public function children() : HasMany
    {
        return $this->hasMany(LeadCategory::class, 'parent_id');
    }
    
}

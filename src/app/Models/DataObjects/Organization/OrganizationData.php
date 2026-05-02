<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Organization;

use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents an organization.
 */
class OrganizationData extends AbstractSoftDeletableTimestampedUuid
{
    
    /**
     * Set the organization's associated client ID.
     * @param string $clientId
     * @return $this
     */
    public function setClientId(string $clientId) : self
    {
        return $this->setProperty("clientId", $clientId);
    }
    
    /**
     * Get the organization's associated client ID.
     * @return string
     */
    public function getClientId() : string
    {
        return $this->getProperty("clientId");
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

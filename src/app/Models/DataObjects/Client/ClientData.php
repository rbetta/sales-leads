<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects\Client;

use Carcosa\Core\DataObjects\AbstractSoftDeletableTimestampedUuid;
use Carcosa\Core\Json\Adapters\CarbonJsonAdapter;
use Illuminate\Contracts\Validation\Validator;

/**
 * A class that represents a client.
 */
class ClientData extends AbstractSoftDeletableTimestampedUuid
{
    
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
    
    /**
     * Set whether this is a test record.
     * @param bool $isTest
     * @return $this
     */
    public function setIsTest(bool $isTest) : self
    {
        return $this->setProperty("isTest", $isTest);
    }
    
    /**
     * Get whether this is a test record.
     * @return bool
     */
    public function getIsTest() : bool
    {
        return $this->getProperty("isTest");
    }
    
    /**
     * Set whether this is an internal record.
     * @param bool $isInternal
     * @return $this
     */
    public function setIsInternal(bool $isInternal) : self
    {
        return $this->setProperty("isInternal", $isInternal);
    }
    
    /**
     * Get whether this is an internal record.
     * @return bool
     */
    public function getIsInternal() : bool
    {
        return $this->getProperty("isInternal");
    }
    
}

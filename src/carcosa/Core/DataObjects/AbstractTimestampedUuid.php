<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carbon\CarbonInterface;

/**
 * An abstract class that represents a data object with a UUID, creation
 * timestamp, and update timestamp.
 */
abstract class AbstractTimestampedUuid extends AbstractUuid
implements iTimestampedUuid
{

    /**
     * Set the creation timestamp.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setCreatedAt(?CarbonInterface $timestamp) : self
    {
        return $this->setProperty("createdAt", $timestamp);
    }
    
    /**
     * Get the creation timestamp.
     * @return ?CarbonInterface
     */
    public function getCreatedAt() : ?CarbonInterface
    {
        return $this->getProperty("createdAt");
    }
    
    /**
     * Set the update timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setUpdatedAt(?CarbonInterface $timestamp) : self
    {
        return $this->setProperty("updatedAt", $timestamp);
    }
    
    /**
     * Get the update timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getUpdatedAt() : ?CarbonInterface
    {
        return $this->getProperty("updatedAt");
    }
    
}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carbon\CarbonInterface;

/**
 * An abstract class that represents a data object with a UUID, creation
 * timestamp, update timestamp, and soft deletion timestamp.
 */
abstract class AbstractSoftDeletableTimestampedUuid extends AbstractTimestampedUuid
implements iSoftDeletableTimestampedUuid
{

    /**
     * Set the deletion timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setDeletedAt(?CarbonInterface $timestamp) : self
    {
        return $this->setProperty("deletedAt", $timestamp);
    }
    
    /**
     * Get the deletion timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getDeletedAt() : ?CarbonInterface
    {
        return $this->getProperty("deletedAt");
    }
    
}

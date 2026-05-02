<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carbon\CarbonInterface;

/**
 * An interface for a data object with a UUID, creation timestamp,
 * update timestamp, and soft deletion timestamp.
 */
interface iSoftDeletableTimestampedUuid extends iTimestampedUuid
{

    /**
     * Set the deletion timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setDeletedAt(?CarbonInterface $timestamp) : self;
    
    /**
     * Get the deletion timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getDeletedAt() : ?CarbonInterface;
    
}

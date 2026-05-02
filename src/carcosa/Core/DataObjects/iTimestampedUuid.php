<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

use Carbon\CarbonInterface;

/**
 * An interface for a data object with a UUID, creation timestamp, and
 * update timestamp.
 */
interface iTimestampedUuid extends iUuid
{

    /**
     * Set the creation timestamp.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setCreatedAt(?CarbonInterface $timestamp) : self;
    
    /**
     * Get the creation timestamp.
     * @return ?CarbonInterface
     */
    public function getCreatedAt() : ?CarbonInterface;
    
    /**
     * Set the update timestamp, if any.
     * @param ?CarbonInterface $timestamp
     * @return $this
     */
    public function setUpdatedAt(?CarbonInterface $timestamp) : self;
    
    /**
     * Get the update timestamp, if any.
     * @return ?CarbonInterface
     */
    public function getUpdatedAt() : ?CarbonInterface;
    
}

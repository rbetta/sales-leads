<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

/**
 * An interface for a data object with a UUID.
 */
interface iUuid extends iDataObject
{

    /**
     * Set the uniquely-identifying UUID.
     * @param ?string $uuid
     * @return $this
     */
    public function setId(?string $uuid) : self;
    
    /**
     * Get the uniquely-identifying UUID.
     * @return ?string
     */
    public function getId() : ?string;
    
}

<?php
declare(strict_types=1);
namespace Carcosa\Core\DataObjects;

/**
 * An abstract class that represents a data object with a UUID.
 */
abstract class AbstractUuid extends AbstractDataObject implements iUuid
{
    
    /**
     * Set the uniquely-identifying UUID.
     * @param ?string $uuid
     * @return $this
     */
    public function setId(?string $uuid) : self
    {
        return $this->setProperty("id", $uuid);
    }
    
    /**
     * Get the uniquely-identifying UUID.
     * @return ?string
     */
    public function getId() : ?string
    {
        return $this->getProperty("id");
    }
    
}

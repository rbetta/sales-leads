<?php
declare(strict_types = 1);
namespace Carcosa\Core\Service;

use Carcosa\Core\Regex\RegexFactory;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A class that represents criteria for retrieving one or more
 * UuidModel model subclass instances through a service.
 * @author Randall Betta
 *
 */
abstract class AbstractUuidModelCriteria extends AbstractModelCriteria
{
    
    /**
     * The IDs to return.
     * @var string[]
     */
    private array $ids = [];
    
    /**
     * Validate if a string is a UUID.
     * @param string $value
     * @return $this
     * @throws \InvalidArgumentException If the specified value is not a UUID.
     */
    private function validateUuid(string $value) : self
    {
        // Define the UUID regex.
        $pattern    = '/^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$/i';
        $regex      = (new RegexFactory())->create($pattern);
        
        // Validate that the supplied value is a UUID.
        if (! $regex->getIsMatch($value) ) {
            throw new \InvalidArgumentException(
                "The non-UUID value \"$value\" was supplied to " . __METHOD__
            );
        }
        
        return $this;
        
    }
    
    /**
     * Clear all IDs.
     * @return $this
     */
    protected function clearIds() : self
    {
        $this->ids = [];
        return $this;
    }
    
    /**
     * Set a single ID to return.
     * @param string $uuid The UUID model ID to return.
     * @return $this
     * @throws \InvalidArgumentException If the supplied ID is not a UUID.
     */
    public function setId(string $uuid) : self
    {
        $this
            ->clearIds()
            ->addId($uuid);
        return $this;
    }
    
    /**
     * Set the IDs to return.
     * @param string[] $uuids The UUID model IDs to return.
     * @return $this
     * @throws \InvalidArgumentException If a supplied ID is not a UUID.
     */
    public function setIds(array $uuids) : self
    {
        $this->clearIds();
        foreach ($uuids as $uuid) {
            $this->addId($uuid);
        }
        return $this;
    }
    
    /**
     * Add an ID to return.
     * @param string $uuid A UUID model ID to return.
     * @return $this
     * @throws \InvalidArgumentException If the supplied ID is not a UUID.
     */
    private function addId(string $uuid) : self
    {
        $this->validateUuid($uuid);
        $this->ids[] = $uuid;
        return $this;
    }
    
    /**
     * Get the IDs to return.
     * @return string[] An array of UUID model IDs to return.
     */
    public function getIds() : array
    {
        return $this->ids;
    }
    
}

<?php
declare(strict_types = 1);

namespace Carcosa\Core\Service;

use Carcosa\Core\Messages\MessageCollection;

/**
 * A class that presents a read-only interface into a ServiceResult instance.
 * @author Randall Betta
 */
class ServiceResultImmutable implements iServiceResult
{
    
    /**
     * The ServiceResult whose data will be read.
     * @var ServiceResult
     */
    private ServiceResult $serviceResult;
    
    /**
     * Construct an instance of this class.
     * @param ServiceResult $serviceResult
     */
    public function __construct(ServiceResult $serviceResult)
    {
        $this->setServiceResult($serviceResult);
    }
    
    /**
     * Set the service Result whose data will be read.
     * @param ServiceResult $serviceResult
     * @return $this
     */
    private function setServiceResult(ServiceResult $serviceResult) : self
    {
        $this->serviceResult = $serviceResult;
        return $this;
    }
    
    /**
     * Get the service Result whose data will be read.
     * @return ServiceResult
     */
    private function getServiceResult() : ServiceResult
    {
        return $this->serviceResult;
    }
    
    /**
     * Get a value.
     * @param string $name The data name.
     * @return mixed Any non-resource value.
     * @throws \LogicException If a nonexistent data name is supplied.
     */
    public function getValue(string $name)
    {
        return $this->getServiceResult()->getValue($name);
    }
    
    /**
     * Get whether a value exists
     * @param string $name The data name.
     * @return bool
     */
    public function getHasValue(string $name) : bool
    {
        return $this->getServiceResult()->getHasValue($name);
    }
    
    /**
     * Get all values.
     * @return array An array whose keys are value names as strings, and
     * whose values are their corresponding non-resource data values.
     */
    public function getValues() : array
    {
        return $this->getServiceResult()->getValue();
    }
    
    /**
     * Get whether this instance contains at least one error message.
     * @return bool
     */
    public function getHasError() : bool
    {
        return $this->getMessages()->getHasError();
    }

    /**
     * Get a copy of this instance's data as an associative array.
     * @return array An array whose keys are data names as strings,
     * and whose values are their corresponding non-resource data values.
     */
    public function toArray() : array
    {
       return $this->getServiceResult()->toArray();
    }

	/**
	 * Get the MessageCollection that contains this instance's messages.
	 * @return MessageCollection
	 */
	public function getMessages() : MessageCollection
	{
	    return $this->getServiceResult()->getMessages();
	}
    
}

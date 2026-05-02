<?php
declare(strict_types = 1);
namespace App\Services\Organization;

use App\Models\Db\Client;
use Carcosa\Core\Service\AbstractUuidModelCriteria;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A class that represents criteria for selecting one or more Organization instances.
 * @author Randall Betta
 *
 */
class OrganizationCriteria extends AbstractUuidModelCriteria
{
    
    /**
     * The client to limit organization results to.
     * @var Client|null
     */
    private Client|null $client = null;
    
    /**
     * Set the client to limit organization searches to.
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client) : self
    {
        $this->client = $client;
        return $this;
    }
    
    /**
     * Get the client to limit organization searches to.
     * @return Client|null
     */
    public function getClient() : Client|null
    {
        return $this->client;
    }
    
}

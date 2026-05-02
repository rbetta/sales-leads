<?php
declare(strict_types = 1);
namespace App\Services\User;

use App\Models\Db\Client;
use Carcosa\Core\Service\AbstractUuidModelCriteria;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A class that represents criteria for selecting one or more User instances.
 * @author Randall Betta
 *
 */
class UserCriteria extends AbstractUuidModelCriteria
{
    
    /**
     * The client to limit user results to.
     * @var Client|null
     */
    private Client|null $client = null;
    
    /**
     * Set the client to limit user searches to.
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client) : self
    {
        $this->client = $client;
        return $this;
    }
    
    /**
     * Get the Client to limit user searches to.
     * @return Client
     */
    public function getClient() : Client
    {
        return $this->client;
    }
    
}

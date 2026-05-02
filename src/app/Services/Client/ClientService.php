<?php
declare(strict_types = 1);
namespace App\Services\Client;

use App\Models\Db\Client;
use App\Services\BaseUuidModelService;
use App\Services\Client\ClientCriteria;
use Carcosa\Core\Service\iServiceResult;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A service for managing client records.
 * @author Randall Betta
 *
 */
class ClientService extends BaseUuidModelService
{
    
    /**
     * Create a new Client instance.
     * @return Client
     */
    public function createNew() : Client
    {
        return new Client();
    }
    
    /**
     * Find exactly one instance by its ID.
     * 
     * This is a convenience method.
     * @param string $id
     * @return Client
     */
    public function findOneById(string $id) : Client
    {
        $criteria = \App::make(ClientCriteria::class);
        $criteria->setId($id);
        return $this->findOne($criteria);
    }
    
    /**
     * Find exactly one instance.
     * @param ClientCriteria $criteria
     * @return Client
     */
    public function findOne(ClientCriteria $criteria) : Client
    {
        $results = $this->find($criteria);
        return $this->handleFindOne($results);
    }
    
    /**
     * Find zero or one model instance.
     * @param ClientCriteria $criteria
     * @return Client|null
     */
    public function findOneOrNone(ClientCriteria $criteria) : Client|null
    {
        $results = $this->find($criteria);
        return $this->handleFindOneOrNone($results);
    }
    
    
    /**
     * Find an arbitrary number of model instances.
     * @param ClientCriteria $criteria
     * @return Client[]
     */
    public function find(ClientCriteria $criteria) : array
    {
        // Retrieve the search criteria.
        $ids = $criteria->getIds();
        
        // Create a query builer using the search criteria.
        $results = Client::query()
            
            // If we join with other tables later, then any identical
            // field names will be clobbered. Prevent that by forcing
            // retrieval of only fields from the table we want.
            ->select('client.*')
            
            // Limit results to only the requested IDs (if any).
            ->when($ids, function ($q) use ($criteria) {
                $this->applyIdsToQueryBuilder($q, $criteria);
            })
            
            // Execute the query and return the results.
            ->get();

        return $results->all();
        
    }
    
    /**
     * Attempt to save a client record.
     * @param Client|array $client The client (or its field data as an array).
     * @return iServiceResult
     */
    public function save(Client|array $client) : iServiceResult
    {
        
        // Define the validator.
        //
        // Note: we explicitly retrieve the database connection name here
        // for use in validator rules. This allows us to segment our data
        // in different databases, in preparation for decomposition into
        // microservices.
        //
        $connectionName = (new Client())->getConnectionName();
        $data           = is_array($client) ? $client : $client->toArray();
        $clientId       = $data['id'] ?? null;
        $validator      = ValidatorFacade::make(
            $data,
            [
                "id"         => "nullable|uuid",
                "label"      => [
                    "required",
                    "string",
                    Rule::unique("$connectionName.client")
                        ->whereNull('deleted_at')   // Deleted records can be duplicates.
                        ->ignore($clientId),
                ],
                "isTest"     => "required|bool",
                "isInternal" => "required|bool",
            ], [
                "label.required"         => "This field is required.",
                "label.unique"           => "This label is already in use.",
                "isTest.required"        => "This field is required.",
                "isInternal.required"    => "This field is required.",
            ]
        );
        
        // Validate the request and create a result instance.
        $result = $this->createServiceResult($data, $validator);

        // Construct the request contents.
        if ($result->getHasError()) {
            
            // Validation failed.
            $result->setValue('client', null);
            
        } else {
            
            // Validation succeeded. Instantiate the ORM model instance.
            $client = ('' === "$clientId")
                ? new Client()
                : Client::findOrFail($clientId);
        
            // Update the database record.
            $client->label          = $data['label'];
            $client->is_test        = (bool) $data['isTest'];
            $client->is_internal    = (bool) $data['isInternal'];
            $client->save();
            
            $result->setValue('client', $client);
            
        }
        
        return $result->toImmutable();
        
    }
    
}

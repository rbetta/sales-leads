<?php
declare(strict_types = 1);
namespace App\Services\Organization;

use App\Models\Db\Client;
use App\Models\Db\Organization;
use App\Services\BaseUuidModelService;
use App\Services\Organization\OrganizationCriteria;
use Carcosa\Core\Service\iServiceResult;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A service for managing Organization records.
 * @author Randall Betta
 *
 */
class OrganizationService extends BaseUuidModelService
{
    
    /**
     * Create a new Organization instance.
     * @param Client $client The client this instance will belong to.
     * @return Organization
     */
    public function createNew(Client $client) : Organization
    {
        $instance = new Organization();
        $instance->client()->associate($client);
        return $instance;
    }
    
    /**
     * Find exactly one instance by its ID.
     * 
     * This is a convenience method.
     * @param string $id
     * @return Organization
     */
    public function findOneById(string $id) : Organization
    {
        $criteria = \App::make(OrganizationCriteria::class);
        $criteria->setId($id);
        return $this->findOne($criteria);
    }
    
    /**
     * Find exactly one instance.
     * @param OrganizationCriteria $criteria
     * @return Organization
     */
    public function findOne(OrganizationCriteria $criteria) : Organization
    {
        $results = $this->find($criteria);
        return $this->handleFindOne($results);
    }
    
    /**
     * Find zero or one model instance.
     * @param OrganizationCriteria $criteria
     * @return Organization|null
     */
    public function findOneOrNone(OrganizationCriteria $criteria) : Organization|null
    {
        $results = $this->find($criteria);
        return $this->handleFindOneOrNone($results);
    }
    
    
    /**
     * Find an arbitrary number of model instances.
     * @param OrganizationCriteria $criteria
     * @return Organization[]
     */
    public function find(OrganizationCriteria $criteria) : array
    {
        // Retrieve the search criteria.
        $ids    = $criteria->getIds();
        $client = $criteria->getClient();
        
        // Create a query builer using the search criteria.
        $results = Organization::query()
            
            // If we join with other tables later, then any identical
            // field names will be clobbered. Prevent that by forcing
            // retrieval of only fields from the table we want.
            ->select('organization.*')
            
            // Limit results to only the requested IDs (if any).
            ->when($ids, function ($q) use ($criteria) {
                $this->applyIdsToQueryBuilder($q, $criteria);
            })
            
            // Limit results to only the requested client (if any).
            ->when($client, function ($q) use ($client) {
                $q->where('client_id', $client->id);
            })
            
            // Execute the query and return the results.
            ->get();
            
        return $results->all();
        
    }
    
    /**
     * Attempt to save an organization record.
     * @param Organization|array $org The organization (or its field data as an array).
     * @return iServiceResult
     */
    public function save(Organization|array $org) : iServiceResult
    {
        
        // Define the validator.
        //
        // Note: we explicitly retrieve the database connection name here
        // for use in validator rules. This allows us to segment our data
        // in different databases, in preparation for decomposition into
        // microservices.
        //
        $connectionName = (new Organization())->getConnectionName();
        $data           = is_array($org) ? $org : $org->toArray();
        $orgId          = $data['id'] ?? null;
        $clientId       = $data['clientId'] ?? null;
        $validator      = ValidatorFacade::make(
            $data,
            [
                "id"        => "nullable|uuid",
                "clientId"  => "required_without:id|uuid|exists:$connectionName.client,id",
                "parentId"  => "nullable|uuid|exists:$connectionName.organization,id",
                "label"     => [
                    "required",
                    "string",
                    Rule::unique("$connectionName.organization")
                        ->where('client_id', $clientId)
                        ->whereNull('deleted_at')   // Deleted records can be duplicates.
                        ->ignore($orgId),
                ],
            ], [
                "label.required"         => "This field is required.",
                "label.unique"           => "This label is already in use.",
            ]
        );
        
        // Validate the request and create a result instance.
        $result = $this->createServiceResult($data, $validator);

        // Construct the request contents.
        if ($result->getHasError()) {
            
            // Validation failed.
            $result->setValue('organization', null);
            
        } else {
            
            // Validation succeeded. Instantiate the ORM model instance.
            $isNew = ('' === "$orgId");
            $org = $isNew
                ? new Organization()
                : Organization::findOrFail($orgId);
        
            // Update the database record.
            if ($isNew) {
                // Client ID can be set only on new records; no sneakily
                // changing an existing record's client.
                $org->client_id = $clientId;
            }
            $org->label = $data['label'];
            $org->save();
            
            $result->setValue('organization', $org);
            
        }
        
        return $result->toImmutable();
        
    }
    
}

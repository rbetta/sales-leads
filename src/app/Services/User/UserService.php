<?php
declare(strict_types = 1);
namespace App\Services\User;

use App\Models\Db\Client;
use App\Models\Db\User;
use App\Services\Auth\AuthService;
use App\Services\BaseUuidModelService;
use App\Services\User\UserCriteria;
use Carcosa\Core\Service\iServiceResult;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * A service for managing User records.
 * @author Randall Betta
 *
 */
class UserService extends BaseUuidModelService
{
    
    /**
     * Create a new User instance.
     * @param Client $client The client this instance will belong to.
     * @return User
     */
    public function createNew(Client $client) : User
    {
        $instance = new User();
        $instance->client()->associate($client);
        return $instance;
    }
    
    /**
     * Find exactly one instance by its ID.
     * 
     * This is a convenience method.
     * @param string $id
     * @return User
     */
    public function findOneById(string $id) : User
    {
        $criteria = \App::make(UserCriteria::class);
        $criteria->setId($id);
        return $this->findOne($criteria);
    }
    
    /**
     * Find exactly one instance.
     * @param UserCriteria $criteria
     * @return User
     */
    public function findOne(UserCriteria $criteria) : User
    {
        $results = $this->find($criteria);
        return $this->handleFindOne($results);
    }
    
    /**
     * Find zero or one model instance.
     * @param UserCriteria $criteria
     * @return User|null
     */
    public function findOneOrNone(UserCriteria $criteria) : User|null
    {
        $results = $this->find($criteria);
        return $this->handleFindOneOrNone($results);
    }
    
    
    /**
     * Find an arbitrary number of model instances.
     * @param UserCriteria $criteria
     * @return User[]
     */
    public function find(UserCriteria $criteria) : array
    {
        // Retrieve the search criteria.
        $ids = $criteria->getIds();
        
        // Create a query builer using the search criteria.
        $results = User::query()
            
            // If we join with other tables later, then any identical
            // field names will be clobbered. Prevent that by forcing
            // retrieval of only fields from the table we want.
            ->select('user.*')
            
            // Limit results to only the requested IDs (if any).
            ->when($ids, function ($q) use ($criteria) {
                $this->applyIdsToQueryBuilder($q, $criteria);
            })
            
            // Execute the query and return the results.
            ->get();
            
        return $results->all();
        
    }
    
    /**
     * Attempt to save a User record.
     * @param User|array $user The user (or its field data as an array).
     * @return iServiceResult
     */
    public function save(User|array $user) : iServiceResult
    {
        
        // Define the validator.
        //
        // Note: we explicitly retrieve the database connection name here
        // for use in validator rules. This allows us to segment our data
        // in different databases, in preparation for decomposition into
        // microservices.
        //
        $connectionName = (new User())->getConnectionName();
        $data           = is_array($user) ? $user : $user->toArray();
        $userId         = $data['id'] ?? null;
        $clientId       = $data['clientId'] ?? null;
        $validator      = ValidatorFacade::make(
            $data,
            [
                "id"                => "nullable|uuid|exists:$connectionName.user,id",
                "clientId"          => "required_without:id|uuid|exists:$connectionName.client,id",
                "username"          => [
                    "required",
                    "string",
                    Rule::unique("$connectionName.user")
                        ->where('client_id', $clientId)
                        ->whereNull('deleted_at')   // Deleted records can be duplicates.
                        ->ignore($userId),
                ],
                "password"          => [
                    "required_without:id",
                    "string",
                    // TODO: add password complexity requirements here.
                ],
                "email"             => [
                    "required",
                    "email",
                    Rule::unique("$connectionName.user")
                        ->where('client_id', $clientId)
                        ->whereNull('deleted_at')   // Deleted records can be duplicates.
                        ->ignore($userId),
                ],
                "isActive"          => "required_without:id|in:0,1",
                "label"             => "required|string",
                "locale"            => "required|string|max:20|regex:/^[a-z]{2}-[a-z]{2}$/i",
                "groupIds"          => "nullable|array",
                "groupIds.*"        => "uuid|exists:$connectionName.group,id",
            ], [
                "username.unique"           => "This username is already in use.",
                "password.required_without" => "The :attribute field is required.",
                "email.unique"              => "This email is already in use.",
                "locale.regex"              => "The locale is invalid (it must be formatted like \"en-US\").",
            ]
        );
        
        // Validate the request and create a result instance.
        $result = $this->createServiceResult($data, $validator);

        // Construct the request contents.
        if ($result->getHasError()) {
            
            // Validation failed.
            $result->setValue('user', null);
            
        } else {
            
            // Validation succeeded. Instantiate the ORM model instance.
            $isNew = ('' === "$userId");
            $user = $isNew
                ? new User()
                : User::findOrFail($userId);
        
            // Instantiate an authentication service for password operations.
            $authService = \App::make(AuthService::class);
                
            // Update the database record.
            if ($isNew) {
                
                // Client ID can be set only on new records; no sneakily
                // changing an existing record's organization.
                $user->client_id = $clientId;
                
                // The following fields can only be set at record creation.
                $user->password = $authService->getPasswordHash($data['password']);
                
            }
            
            $user->username     = $data['username'];
            $user->email        = $data['email'];
            $user->is_active    = $data['isActive'];
            $user->label        = $data['label'];
            $user->locale       = $data['locale'];
            
            // Save the user and its associated groups in a transaction,
            // to ensure both are handled atomically.
            $groupIds = $data['groupIds'] ?? [];
            DB::transaction(function () use ($user, $groupIds) {
                
                // Create or update the user.
                $user->save();
                
                // Synchronize the user's associated groups.
                $user->groups()->sync($groupIds);
                
            });
                
            $result->setValue('user', $user);
            
        }
        
        return $result->toImmutable();
        
    }
    
}

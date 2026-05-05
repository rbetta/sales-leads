<?php

use App\Http\Controllers\Auth\Password\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\SystemAdmin\LeadCategory\LeadCategoryApiController;
use App\Http\Controllers\SystemAdmin\LeadCategory\LeadCategoryController;
use App\Http\Controllers\Seller\SellerHomeController;
use App\Http\Controllers\SystemAdmin\SystemAdminHomeController;
use App\Http\Middleware\AuthenticateCustomerUser;
use App\Http\Middleware\AuthenticateSellerUser;
use App\Http\Middleware\AuthenticateSystemAdminUser;
use Illuminate\Support\Facades\Route;

// Handle the homepage.
Route::get('/', [HomeController::class, 'displayHomepage'])
    ->name('home:logged-out');

// Handle routes for authentication.
Route::prefix('auth')->namespace('Auth')->group(function () {
    
    // Handle password-based authentication.
    Route::prefix('password')->namespace('Password')->group(function () {
        
        // Display the login form.
        Route::get('login', [LoginController::class, 'displayLoginForm'])
            ->name('login:password:display-login-form');
        
        // Handle the login form submission.
        Route::post('login', [LoginController::class, 'handleLoginForm'])
            ->name('login:password:handle-login-form');
        
        // Handle the logout form submission.
        Route::get('logout', [LoginController::class, 'handleLogoutForm'])
            ->name('logout:password:handle-logout-form');
        
    });

});

// Handle routes for a seller.
Route::prefix('seller')->namespace('Seller')->middleware(AuthenticateSellerUser::class)->group(function () {
    
    // Display the logged-in homepage.
    Route::get('/', [SellerHomeController::class, 'displayHomepage'])
        ->name('seller:home:logged-in');
    
});

// Handle routes for a customer.
Route::prefix('customer')->namespace('Customer')->middleware(AuthenticateCustomerUser::class)->group(function () {
    
    // Display the logged-in homepage.
    Route::get('/', [CustomerHomeController::class, 'displayHomepage'])
        ->name('customer:home:logged-in');
    
});

// Handle routes for a system administrator.
Route::prefix('system-admin')->namespace('SystemAdmin')->middleware(AuthenticateSystemAdminUser::class)->group(function () {
    
    // Display the logged-in homepage.
    Route::get('/', [SystemAdminHomeController::class, 'displayHomepage'])
        ->name('system-admin:home:logged-in');
    
    // Handle routes related to managing lead categories.
    Route::prefix('lead-category')->namespace('LeadCategory')->group(function () {
        
        // Display the "list lead categories" screen.
        Route::get('list', [LeadCategoryController::class, 'displayLeadCategoriesList'])
            ->name('system-admin:lead-category:display-lead-category-list');
        
    });
    
});

// Handle front-end interface-populating API routes that require system admin user login.
Route::prefix('api/v1/system-admin')
    ->namespace('SystemAdmin')
    ->middleware(AuthenticateSystemAdminUser::class)
    ->group(function () {
    
    // Handle routes related to managing lead categories.
    Route::prefix('lead-category')->namespace('LeadCategory')->group(function () {
        
        // Get lead categories by their parent IDs.
        Route::get('by-parent-id/{parentId?}', [LeadCategoryApiController::class, 'getByParentId'])
            ->name('api:system-admin:lead-category:get:by-parent-id');
    
        // Save a lead category.
        Route::post('/', [LeadCategoryApiController::class, 'save'])
            ->name('api:system-admin:lead-category:save');
        
        // Delete a lead category.
        Route::delete('{childStrategy}/{leadCategoryId}', [LeadCategoryApiController::class, 'delete'])
            ->whereIn('childStrategy', ['delete-children', 'promote-children'])
            ->name('api:system-admin:lead-category:delete');
            
        
            
    });
    
});


// Handle routes that require administrative login.
/*
Route::prefix('admin')->namespace('Admin')->middleware(AuthenticateAdminUser::class)->group(function () {
    
    // Display the logged-in, administrative homepage.
    Route::get('/', [AdminHomeController::class, 'displayHomepage'])
        ->name('admin:home:logged-in');
    
    // Handle routes related to managing clients.
    Route::prefix('clients')->namespace('Client')->group(function () {
        
        // Display the "list clients" screen.
        Route::get('list', [ClientController::class, 'displayClientsList'])
            ->name('admin:client:display-client-list');
        
        // Display the "create or edit client" form.
        Route::get('edit/{clientId?}', [ClientController::class, 'displayCreateOrEditClientForm'])
            ->name('admin:client:display-create-or-edit-client-form');
            
        // Handle the submission of the "create or edit client" form.
        Route::post('edit/{clientId?}', [ClientController::class, 'handleCreateOrEditClientForm'])
            ->name('admin:client:handle-create-or-edit-client-form');
        
    });
    
    // Handle routes related to managing organizations.
    Route::prefix('organizations')->namespace('Organization')->group(function () {
        
        // Display the "list organizations" screen.
        Route::get('list/by-client/{clientId}', [OrganizationController::class, 'displayOrganizationsList'])
            ->name('admin:organization:display-organization-list');
        
        // Display the "create organization" form.
        Route::get('create/for-client/{clientId}', [OrganizationController::class, 'displayCreateOrganizationForm'])
            ->name('admin:organization:display-create-organization-form');
            
        // Display the "edit organization" form.
        Route::get('edit/{organizationId}', [OrganizationController::class, 'displayEditOrganizationForm'])
            ->name('admin:organization:display-edit-organization-form');
        
        // Handle the submission of the "create organization" or "edit organization" form.
        Route::post('edit', [OrganizationController::class, 'handleCreateOrEditOrganizationForm'])
            ->name('admin:organization:handle-create-or-edit-organization-form');
        
    });
    
    // Handle routes related to managing users.
    Route::prefix('users')->namespace('User')->group(function () {
        
        // Display the "list users" screen.
        Route::get('list/by-client/{clientId}', [UserController::class, 'displayUsersList'])
            ->name('admin:user:display-user-list');
        
        // Display the "create user" form.
        Route::get('create/for-client/{clientId}', [UserController::class, 'displayCreateUserForm'])
            ->name('admin:user:display-create-user-form');
            
        // Display the "edit user" form.
        Route::get('edit/{userId}', [UserController::class, 'displayEditUserForm'])
            ->name('admin:user:display-edit-user-form');
        
        // Handle the submission of the "create user" or "edit user" form.
        Route::post('edit', [UserController::class, 'handleCreateOrEditUserForm'])
            ->name('admin:user:handle-create-or-edit-user-form');
        
    });
    
    // Handle routes related to managing permissions.
    Route::prefix('permissions')->namespace('Permission')->group(function () {
        
        // Display the per-client "list permissions" screen.
        Route::get('list/by-client/{clientId}', [PermissionController::class, 'displayPermissionsList'])
            ->name('admin:permission:display-client-permission-list');
        
        // Display the per-client "create permission" form.
        Route::get('create/for-client/{clientId}', [PermissionController::class, 'displayCreatePermissionForm'])
            ->name('admin:permission:display-create-client-permission-form');
        
        // Display the per-application "list permissions" screen.
        Route::get('list/by-application/{applicationId}', [PermissionController::class, 'displayPermissionsList'])
            ->name('admin:permission:display-application-permission-list');
        
        // Display the per-application "create permission" form.
        Route::get('create/for-application/{applicationId}', [PermissionController::class, 'displayCreatePermissionForm'])
            ->name('admin:permission:display-create-application-permission-form');
        
        // Display the system "list permissions" screen.
        Route::get('list/system', [PermissionController::class, 'displayPermissionsList'])
            ->name('admin:permission:display-system-permission-list');
        
        // Display the system "create permission" form.
        Route::get('create/system', [PermissionController::class, 'displayCreatePermissionForm'])
            ->name('admin:permission:display-create-system-permission-form');
            
        // Display the "edit permission" form.
        Route::get('edit/{permissionId}', [PermissionController::class, 'displayEditPermissionForm'])
            ->name('admin:permission:display-edit-permission-form');
        
        // Handle the submission of the "create permission" or "edit permission" form.
        Route::post('edit', [PermissionController::class, 'handleCreateOrEditPermissionForm'])
            ->name('admin:permission:handle-create-or-edit-permission-form');
        
    });
    
    // Handle routes related to managing default permissions.
    Route::prefix('default-permissions')->namespace('Permission')->group(function () {
        
        // Display the "list default permissions" screen.
        Route::get('list', [DefaultPermissionController::class, 'displayDefaultPermissionsList'])
            ->name('admin:permission:display-default-permission-list');
        
        // Display the "create default permission" form.
        Route::get('create/for-level/{permissionLevelId}', [DefaultPermissionController::class, 'displayCreateDefaultPermissionForm'])
            ->name('admin:permission:display-create-default-permission-form');
        
        // Display the "edit default permission" form.
        Route::get('edit/{defaultPermissionId}', [DefaultPermissionController::class, 'displayEditDefaultPermissionForm'])
            ->name('admin:permission:display-edit-default-permission-form');
        
        // Handle the submission of the "create default permission" or "edit default permission" form.
        Route::post('edit', [DefaultPermissionController::class, 'handleCreateOrEditDefaultPermissionForm'])
            ->name('admin:permission:handle-create-or-edit-default-permission-form');
        
    });
    
});
*/

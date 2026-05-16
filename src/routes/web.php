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
        
        // Display the "create lead category" screen.
        Route::get('create/for-parent/{parentId?}', [LeadCategoryController::class, 'displayCreateLeadCategoryForm'])
            ->name('system-admin:lead-category:display-create-lead-category-form');

        // Handle a submission of the "create/edit lead category" screen.
        Route::post('lead-category', [LeadCategoryController::class, 'handleCreateOrEditLeadCategoryForm'])
            ->name('system-admin:lead-category:handle-create-or-edit-lead-category-form');
        
    });
    
});

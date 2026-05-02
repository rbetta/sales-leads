<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerHomeController extends CustomerBaseController
{

    /**
     * Display the logged-in seller homepage.
     * @return View
     */
    public function displayHomepage() {
        
        return view('customer.home', []);
        
    }
    
}

<?php
declare(strict_types = 1);
namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerHomeController extends SellerBaseController
{

    /**
     * Display the logged-in seller homepage.
     * @return View
     */
    public function displayHomepage() {
        
        return view('seller.home', []);
        
    }
    
}

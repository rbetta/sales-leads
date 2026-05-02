<?php
declare(strict_types = 1);
namespace App\Http\Controllers\SystemAdmin;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SystemAdminHomeController extends SystemAdminBaseController
{

    /**
     * Display the logged-in administrative homepage.
     * @return View
     */
    public function displayHomepage() {
        
        return view('system-admin.home', []);
        
    }
    
}

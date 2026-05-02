<?php
declare(strict_types = 1);
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class HomeController extends BaseController
{
    /**
     * Display the logged-in administrative homepage.
     * @param ApplicationCriteria $applicationCriteria
     * @param ApplicationService $applicationService
     * @return View
     */
    public function displayHomepage() {
        
        // Display the logged-out homepage.
        return view('home', []);
        
    }
}

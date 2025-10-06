<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Project;
use App\Models\ProjectStatus;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user()->load(['userType']);  

            if($user->UserTypeID == 1){
                return redirect()->route('showProdHead')->with('success', 'Login successful!');
            }elseif($user->UserTypeID == 2){
                return redirect()->route('showAdmin')->with('success', 'Login successful!');
            } else {
                return redirect()->route('showAdmin')->with('success', 'Login successful!');
            }
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function show()
    {
        return view('Admin.employees.index'); // Your new page view
    }


    public function Projects()
    {
        // Include these 5 statuses
        $allowedStatuses = ['Upcoming', 'On Going', 'Under Warranty', 'Completed', 'On Hold'];
        $statuses = ProjectStatus::whereIn('StatusName', $allowedStatuses)->get();
        
        // Get projects grouped by status
        $projectsByStatus = [];
        foreach ($statuses as $status) {
            $projectsByStatus[$status->StatusName] = Project::with('status')
                ->where('StatusID', $status->StatusID)
                ->orderBy('StartDate', 'desc')
                ->get();
        }
        
        // Get all projects for the main view (if needed)
        $allProjects = Project::with('status')->orderBy('StartDate', 'desc')->get();
        
        return view('ProdHeadPage.projects', compact('projectsByStatus', 'statuses', 'allProjects'));
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('showAdmin');
        }
        return view('LogInPage');
    }   
}
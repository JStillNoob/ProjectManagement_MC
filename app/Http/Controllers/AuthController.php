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
            }elseif($user->UserTypeID == 3){
                return redirect()->route('attendance.index')->with('success', 'Login successful!');
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
        // Automatically update project statuses based on dates before displaying
        $this->updateProjectStatuses();
        
        // Include these statuses in the desired order
        $allowedStatuses = ['Pending', 'Pre-Construction', 'On Going', 'Under Warranty', 'Completed', 'On Hold'];
        $statuses = ProjectStatus::whereIn('StatusName', $allowedStatuses)
            ->orderByRaw("FIELD(StatusName, 'Pending', 'Pre-Construction', 'On Going', 'Under Warranty', 'Completed', 'On Hold')")
            ->get();
        
        // Get projects grouped by status
        $projectsByStatus = [];
        foreach ($statuses as $status) {
            $projectsByStatus[$status->StatusName] = Project::with(['status', 'client'])
                ->where('StatusID', $status->StatusID)
                ->orderBy('StartDate', 'desc')
                ->get();
        }
        
        // Get all projects for the main view (if needed)
        $allProjects = Project::with(['status', 'client'])->orderBy('StartDate', 'desc')->get();
        
        return view('ProdHeadPage.projects', compact('projectsByStatus', 'statuses', 'allProjects'));
    }

    /**
     * Update project statuses based on their start and end dates.
     * This ensures projects automatically transition to "On Going" when start date arrives.
     */
    private function updateProjectStatuses()
    {
        $projects = Project::with('status')->get();
        
        foreach ($projects as $project) {
            // Skip if status is On Hold or Cancelled (manual statuses)
            $currentStatus = $project->status->StatusName ?? null;
            if (in_array($currentStatus, ['On Hold', 'Cancelled'])) {
                continue;
            }
            
            $newStatusId = Project::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyDays,
                $project->NTPStartDate
            );
            
            if ($project->StatusID != $newStatusId) {
                // Use saveQuietly to avoid triggering the updating event (which would recalculate)
                $project->StatusID = $newStatusId;
                $project->saveQuietly();
            }
        }
    }

    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if($user->UserTypeID == 1){
                return redirect()->route('showProdHead');
            }elseif($user->UserTypeID == 2){
                return redirect()->route('showAdmin');
            }elseif($user->UserTypeID == 3){
                return redirect()->route('attendance.index');
            } else {
                return redirect()->route('showAdmin');
            }
        }
        return view('LogInPage');
    }   
}
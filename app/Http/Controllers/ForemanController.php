<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectEmployee;
use App\Models\Employee;

class ForemanController extends Controller
{
    /**
     * Display all projects assigned to the foreman
     */
    public function myProjects()
    {
        $user = Auth::user();
        
        // Check if user is Foreman (UserTypeID = 3)
        if ($user->UserTypeID != 3) {
            abort(403, 'Access denied. Only Foremen can access this page.');
        }

        if (!$user->EmployeeID) {
            return view('foreman.projects', [
                'projects' => collect([]),
                'message' => 'No employee record found. Please contact administrator.'
            ]);
        }

        // Get all projects where the foreman is assigned
        $projectIds = ProjectEmployee::where('EmployeeID', $user->EmployeeID)
            ->where('status', 'Active')
            ->pluck('ProjectID');

        $projects = Project::whereIn('ProjectID', $projectIds)
            ->with(['status', 'client', 'milestones' => function($query) {
                $query->orderBy('order')->orderBy('milestone_id');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // If foreman is assigned to only one project, redirect directly to that project
        if ($projects->count() == 1) {
            return redirect()->route('foreman.projects.show', $projects->first());
        }

        return view('foreman.projects', compact('projects'));
    }

    /**
     * Display a specific project for foreman
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        
        // Check if user is Foreman (UserTypeID = 3)
        if ($user->UserTypeID != 3) {
            abort(403, 'Access denied. Only Foremen can access this page.');
        }

        if (!$user->EmployeeID) {
            return redirect()->route('foreman.projects')
                ->with('error', 'No employee record found. Please contact administrator.');
        }

        // Verify that the foreman is assigned to this project
        $isAssigned = ProjectEmployee::where('ProjectID', $project->ProjectID)
            ->where('EmployeeID', $user->EmployeeID)
            ->where('status', 'Active')
            ->exists();

        if (!$isAssigned) {
            abort(403, 'You are not assigned to this project.');
        }

        // Load project with necessary relationships, ordered by 'order' field
        $project->load([
            'status',
            'client',
            'milestones' => function($query) {
                $query->orderBy('order', 'asc')->orderBy('milestone_id', 'asc');
            },
            'milestones.materials.item',
            'milestones.equipment.item',
            'milestones.submittedBy.position',
            'milestones.approvedBy.position',
            'milestones.proofImages'
        ]);

        return view('foreman.project-show', compact('project'));
    }
}


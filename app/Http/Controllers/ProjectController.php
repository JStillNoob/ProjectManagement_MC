<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ProjectEmployee;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // This method is not used since we use AuthController@Projects instead
        return redirect()->route('ProdHead.projects');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $employees = Employee::active()->get();
        return view('projects.create', compact('clients', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ProjectName' => 'required|string|max:150|unique:projects,ProjectName',
            'ProjectDescription' => 'nullable|string',
            'Client' => 'nullable|string|max:150',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after:StartDate',
            'ClientID' => 'nullable|integer',
            'WarrantyEndDate' => 'nullable|date|after:EndDate',
            'StreetAddress' => 'nullable|string|max:200',
            'Barangay' => 'nullable|string|max:100',
            'City' => 'nullable|string|max:100',
            'StateProvince' => 'nullable|string|max:100',
            'ZipCode' => 'nullable|string|max:10',
        ]);

        $project = Project::create($request->all());

        // Assign employees if provided
        if ($request->has('employee_ids')) {
            $project->employees()->sync($request->employee_ids);
        }

        return redirect()->route('ProdHead.projects')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load(['status', 'client', 'employees']);
        return view('ProdHeadPage.project-show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $statuses = ProjectStatus::all();
        $clients = Client::all();
        $employees = Employee::active()->get();
        $projectEmployees = $project->employees->pluck('id')->toArray();
        return view('ProdHeadPage.project-edit', compact('project', 'statuses', 'clients', 'employees', 'projectEmployees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'ProjectName' => 'required|string|max:150|unique:projects,ProjectName,' . $project->ProjectID . ',ProjectID',
            'ProjectDescription' => 'nullable|string',
            'Client' => 'nullable|string|max:150',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after:StartDate',
            'ClientID' => 'nullable|integer',
            'WarrantyEndDate' => 'nullable|date|after:EndDate',
            'StreetAddress' => 'nullable|string|max:200',
            'Barangay' => 'nullable|string|max:100',
            'City' => 'nullable|string|max:100',
            'StateProvince' => 'nullable|string|max:100',
            'ZipCode' => 'nullable|string|max:10',
        ]);

        $project->update($request->all());

        // Update project employees if provided
        if ($request->has('employee_ids')) {
            $project->employees()->sync($request->employee_ids);
        }

        return redirect()->route('ProdHead.projects')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('ProdHead.projects')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Set project status to On Hold.
     */
    public function onHold(Project $project)
    {
        $onHoldStatus = ProjectStatus::where('StatusName', 'On Hold')->first();
       
        if ($onHoldStatus) {
            $project->update(['StatusID' => $onHoldStatus->StatusID]);

            
            return redirect()->route('ProdHead.projects')
                ->with('success', 'Project status updated to On Hold.');
        }
        
        return redirect()->route('ProdHead.projects')
            ->with('error', 'Could not update project status.');
            
    }

    /**
     * Show project employees management page.
     */
    public function manageEmployees(Project $project)
    {
        $project->load(['projectEmployees.employee.position', 'projectEmployees.employee.employeeType']);
        $availableEmployees = Employee::active()
            ->whereNotExists(function($query) use ($project) {
                $query->select(\DB::raw(1))
                      ->from('project_employees')
                      ->whereRaw('project_employees.EmployeeID = employees.id')
                      ->where('project_employees.ProjectID', $project->ProjectID);
            })
            ->with(['position', 'employeeType'])
            ->get();
        
        return view('projects.manage-employees', compact('project', 'availableEmployees'));
    }

    /**
     * Assign single employee to project.
     */
    public function assignSingleEmployee(Request $request, Project $project)
    {
        $request->validate([
            'EmployeeID' => 'required|exists:employees,id',
        ]);

        // Check if employee is already assigned to this project
        $existingAssignment = ProjectEmployee::where('ProjectID', $project->ProjectID)
            ->where('EmployeeID', $request->EmployeeID)
            ->first();

        if ($existingAssignment) {
            return redirect()->back()
                ->with('error', 'Employee is already assigned to this project.');
        }

        // Create project employee assignment
        ProjectEmployee::create([
            'ProjectID' => $project->ProjectID,
            'EmployeeID' => $request->EmployeeID,
            'role_in_project' => null, // No additional role needed
            'assigned_date' => now(),
            'status' => 'Active'
        ]);

        return redirect()->back()
            ->with('success', 'Employee assigned to project successfully.');
    }

    /**
     * Assign multiple employees to project.
     */
    public function assignMultipleEmployees(Request $request, Project $project)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $assignedCount = 0;
        $alreadyAssigned = [];

        foreach ($request->employee_ids as $employeeId) {
            // Check if employee is already assigned to this project
            $existingAssignment = ProjectEmployee::where('ProjectID', $project->ProjectID)
                ->where('EmployeeID', $employeeId)
                ->first();

            if (!$existingAssignment) {
                // Create project employee assignment
                ProjectEmployee::create([
                    'ProjectID' => $project->ProjectID,
                    'EmployeeID' => $employeeId,
                    'role_in_project' => null, // No additional role needed
                    'assigned_date' => now(),
                    'status' => 'Active'
                ]);
                $assignedCount++;
            } else {
                $employee = \App\Models\Employee::find($employeeId);
                $alreadyAssigned[] = $employee->full_name;
            }
        }

        $message = "Successfully assigned {$assignedCount} employee(s) to the project.";
        if (!empty($alreadyAssigned)) {
            $message .= " The following employees were already assigned: " . implode(', ', $alreadyAssigned);
        }

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Complete employee's job in project.
     */
    public function completeEmployeeJob(Project $project, ProjectEmployee $assignment)
    {
        // Verify the assignment belongs to this project
        if ($assignment->ProjectID != $project->ProjectID) {
            return redirect()->back()
                ->with('error', 'Invalid assignment.');
        }

        // Check if job is already completed
        if ($assignment->end_date) {
            return redirect()->back()
                ->with('error', 'Employee job is already completed.');
        }

        // Set end date to current date and update status
        $assignment->update([
            'end_date' => now()->toDateString(),
            'status' => 'Inactive'
        ]);

        return redirect()->back()
            ->with('success', 'Employee job marked as completed successfully.');
    }

    /**
     * Remove employee from project.
     */
    public function removeEmployee(Project $project, ProjectEmployee $assignment)
    {
        // Verify the assignment belongs to this project
        if ($assignment->ProjectID != $project->ProjectID) {
            return redirect()->back()
                ->with('error', 'Invalid assignment.');
        }

        $assignment->delete();

        return redirect()->back()
            ->with('success', 'Employee removed from project successfully.');
    }

    /**
     * Reactivate project from On Hold status.
     */
    public function reactivate(Project $project)
    {
        // Calculate the appropriate status based on current dates
        $newStatusId = Project::calculateStatus($project->StartDate, $project->EndDate, $project->WarrantyEndDate);
        $project->update(['StatusID' => $newStatusId]);
        
        $newStatus = $project->fresh()->status->StatusName;
        return redirect()->route('ProdHead.projects')
            ->with('success', "Project reactivated with status: {$newStatus}.");
    }

    /**
     * Assign employees to project.
     */
    public function assignEmployees(Request $request, Project $project)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $project->employees()->sync($request->employee_ids);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Employees assigned to project successfully.');
    }

}
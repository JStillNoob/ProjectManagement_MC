<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Client;
use App\Models\Employee;
use App\Models\ProjectEmployee;
use Barryvdh\DomPDF\Facade\Pdf;

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
        return view('projects.create', compact('clients'));
    }

    /**
     * Show the milestone management step for a new project.
     */
    public function createMilestones(Project $project)
    {
        $project->load(['milestones', 'projectEmployees.employee.position']);
        
        // Get available employees (not already assigned to this project)
        $availableEmployees = Employee::active()
            ->whereNotExists(function($query) use ($project) {
                $query->select(\DB::raw(1))
                      ->from('project_employees')
                      ->whereRaw('project_employees.EmployeeID = employees.id')
                      ->where('project_employees.ProjectID', $project->ProjectID);
            })
            ->with(['position'])
            ->get();
        
        return view('projects.create-milestones', compact('project', 'availableEmployees'));
    }

    /**
     * Complete project creation after milestone step.
     */
    public function completeCreation(Project $project)
    {
        $milestoneCount = $project->milestones()->count();
        $employeeCount = $project->projectEmployees()->count();
        
        $message = 'Project created successfully';
        if ($milestoneCount > 0) {
            $message .= ' with ' . $milestoneCount . ' milestone(s)';
        }
        if ($employeeCount > 0) {
            $message .= ' and ' . $employeeCount . ' employee(s) assigned';
        }
        $message .= '.';
        
        return redirect()->route('ProdHead.projects')
            ->with('success', $message);
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
            'EstimatedAccomplishDays' => 'required|integer|min:1',
            'ClientID' => 'nullable|integer',
            'WarrantyDays' => 'required|integer|min:0',
            'StreetAddress' => 'nullable|string|max:200',
            'Barangay' => 'nullable|string|max:100',
            'City' => 'nullable|string|max:100',
            'StateProvince' => 'nullable|string|max:100',
            'ZipCode' => 'nullable|string|max:10',
            'Blueprint' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg|max:10240',
            'FloorPlan' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg|max:10240',
        ]);

        $data = $request->all();
        
        // Handle Blueprint file upload
        if ($request->hasFile('Blueprint')) {
            $blueprintFile = $request->file('Blueprint');
            $blueprintName = 'blueprint_' . time() . '_' . uniqid() . '.' . $blueprintFile->getClientOriginalExtension();
            $blueprintPath = $blueprintFile->storeAs('project_documents/blueprints', $blueprintName, 'public');
            $data['BlueprintPath'] = $blueprintPath;
        }
        
        // Handle Floor Plan file upload
        if ($request->hasFile('FloorPlan')) {
            $floorPlanFile = $request->file('FloorPlan');
            $floorPlanName = 'floorplan_' . time() . '_' . uniqid() . '.' . $floorPlanFile->getClientOriginalExtension();
            $floorPlanPath = $floorPlanFile->storeAs('project_documents/floorplans', $floorPlanName, 'public');
            $data['FloorPlanPath'] = $floorPlanPath;
        }
        
        // Set StartDate and EndDate to null initially (will be set when NTP is approved)
        $data['StartDate'] = null;
        $data['EndDate'] = null;

        $project = Project::create($data);

        // Redirect to milestone management step (employees will be assigned there)
        return redirect()->route('projects.create-milestones', $project)
            ->with('success', 'Project information saved. Now add milestones and assign employees.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load([
            'status', 
            'client', 
            'employees', 
            'milestones.materials.item',
            'milestones.equipment.item',
            'milestones.requiredItems.item.resourceCatalog',
            'milestones.proofImages',
            'milestones.submittedBy.position',
            'milestones.approvedBy.position'
        ]);
        
        // Sequential Milestone Workflow: Only first incomplete milestone can be In Progress
        $projectStatus = $project->status->StatusName ?? '';
        
        // Only activate milestones when project is On Going
        if ($projectStatus === 'On Going') {
            // Get milestones ordered by 'order' field, then by milestone_id
            $orderedMilestones = $project->milestones->sortBy([
                ['order', 'asc'],
                ['milestone_id', 'asc']
            ]);
            
            $foundInProgress = false;
            
            foreach ($orderedMilestones as $milestone) {
                if ($milestone->status === 'Completed') {
                    // Skip completed milestones
                    continue;
                }
                
                if ($milestone->status === 'In Progress') {
                    // Already have an active milestone
                    $foundInProgress = true;
                    break;
                }
                
                if ($milestone->status === 'Pending' && !$foundInProgress) {
                    // This is the first incomplete milestone - activate it
                    $milestone->status = 'In Progress';
                    $milestone->saveQuietly();
                    $foundInProgress = true;
                    break;
                }
            }
        }
        
        // Reload milestones to get updated statuses, ordered by 'order' field
        $project->load([
            'status', 
            'client', 
            'employees',
            'projectEmployees.employee.position',
            'milestones.materials.item',
            'milestones.equipment.item',
            'milestones.requiredItems.item.resourceCatalog',
            'milestones.proofImages',
            'milestones.submittedBy.position',
            'milestones.approvedBy.position'
        ]);
        
        // Get available employees (not already assigned to this project)
        $availableEmployees = Employee::active()
            ->whereNotExists(function($query) use ($project) {
                $query->select(\DB::raw(1))
                      ->from('project_employees')
                      ->whereRaw('project_employees.EmployeeID = employees.id')
                      ->where('project_employees.ProjectID', $project->ProjectID);
            })
            ->with(['position'])
            ->get();
        
        // Load inventory items for modals
        $materials = \App\Models\InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Materials');
            })
            ->where('Status', 'Active')
            ->join('resource_catalog', 'inventory_items.ResourceCatalogID', '=', 'resource_catalog.ResourceCatalogID')
            ->orderBy('resource_catalog.ItemName')
            ->select('inventory_items.*')
            ->get();
        
        $equipment = \App\Models\InventoryItem::with('resourceCatalog')
            ->whereHas('resourceCatalog', function($q) {
                $q->where('Type', 'Equipment');
            })
            ->where('Status', 'Active')
            ->join('resource_catalog', 'inventory_items.ResourceCatalogID', '=', 'resource_catalog.ResourceCatalogID')
            ->orderBy('resource_catalog.ItemName')
            ->select('inventory_items.*')
            ->get();
        
        // Check if user is foreman (UserTypeID == 3)
        $user = Auth::user();
        $isForemanUser = ($user->UserTypeID == 3);
        
        // For foreman users, use the simplified project show view
        if ($isForemanUser) {
            return view('projects.show', compact('project'));
        }
        
        // For other users (engineers, managers, etc.), use the full project show view
        return view('ProdHeadPage.project-show', compact('project', 'materials', 'equipment', 'availableEmployees'));
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
        $project->load('milestones');
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
            'WarrantyDays' => 'required|integer|min:0',
            'StreetAddress' => 'nullable|string|max:200',
            'Barangay' => 'nullable|string|max:100',
            'City' => 'nullable|string|max:100',
            'StateProvince' => 'nullable|string|max:100',
            'ZipCode' => 'nullable|string|max:10',
        ]);

        $project->update($request->all());

        // Update project employees if provided
        if ($request->has('employee_ids')) {
            // Check for foreman conflicts before syncing
            $foremanConflicts = [];
            $validEmployeeIds = [];
            
            foreach ($request->employee_ids as $employeeId) {
                $employee = Employee::with('position')->find($employeeId);
                
                if ($this->isForeman($employee)) {
                    // Check if foreman is assigned to another project
                    $otherProjectAssignment = ProjectEmployee::where('EmployeeID', $employeeId)
                        ->where('ProjectID', '!=', $project->ProjectID)
                        ->where('status', 'Active')
                        ->first();

                    if ($otherProjectAssignment) {
                        $otherProject = Project::find($otherProjectAssignment->ProjectID);
                        $foremanConflicts[] = "{$employee->full_name} (already assigned to '{$otherProject->ProjectName}')";
                        continue;
                    }
                }
                
                $validEmployeeIds[] = $employeeId;
            }
            
            // Sync only valid employees
            $project->employees()->sync($validEmployeeIds);
            
            if (!empty($foremanConflicts)) {
                return redirect()->back()
                    ->with('warning', 'Project updated, but the following foremen could not be assigned (they can only be assigned to one project): ' . implode(', ', $foremanConflicts) . '.');
            }
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
        $project->load(['projectEmployees.employee.position']);
        $availableEmployees = Employee::active()
            ->whereNotExists(function($query) use ($project) {
                $query->select(\DB::raw(1))
                      ->from('project_employees')
                      ->whereRaw('project_employees.EmployeeID = employees.id')
                      ->where('project_employees.ProjectID', $project->ProjectID);
            })
            ->with(['position'])
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

        $employee = Employee::with('position')->findOrFail($request->EmployeeID);

        // Check if employee is already assigned to this project
        $existingAssignment = ProjectEmployee::where('ProjectID', $project->ProjectID)
            ->where('EmployeeID', $request->EmployeeID)
            ->first();

        if ($existingAssignment) {
            return redirect()->back()
                ->with('error', 'Employee is already assigned to this project.');
        }

        // Check if employee is a foreman and already assigned to another project
        if ($this->isForeman($employee)) {
            $otherProjectAssignment = ProjectEmployee::where('EmployeeID', $request->EmployeeID)
                ->where('ProjectID', '!=', $project->ProjectID)
                ->where('status', 'Active')
                ->first();

            if ($otherProjectAssignment) {
                $otherProject = Project::find($otherProjectAssignment->ProjectID);
                return redirect()->back()
                    ->with('error', "Foreman '{$employee->full_name}' is already assigned to project '{$otherProject->ProjectName}'. A foreman can only be assigned to one project at a time.");
            }
        }

        // Create project employee assignment
        $projectEmployee = ProjectEmployee::create([
            'ProjectID' => $project->ProjectID,
            'EmployeeID' => $request->EmployeeID,
            'role_in_project' => null, // No additional role needed
            'assigned_date' => now(),
            'status' => 'Active'
        ]);
        
        // Ensure QR code is generated
        if (!$projectEmployee->qr_code) {
            $projectEmployee->generateQrCode();
        }

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
        $foremanConflicts = [];

        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::with('position')->find($employeeId);
            
            // Check if employee is already assigned to this project
            $existingAssignment = ProjectEmployee::where('ProjectID', $project->ProjectID)
                ->where('EmployeeID', $employeeId)
                ->first();

            if ($existingAssignment) {
                $alreadyAssigned[] = $employee->full_name;
                continue;
            }

            // Check if employee is a foreman and already assigned to another project
            if ($this->isForeman($employee)) {
                $otherProjectAssignment = ProjectEmployee::where('EmployeeID', $employeeId)
                    ->where('ProjectID', '!=', $project->ProjectID)
                    ->where('status', 'Active')
                    ->first();

                if ($otherProjectAssignment) {
                    $otherProject = Project::find($otherProjectAssignment->ProjectID);
                    $foremanConflicts[] = "{$employee->full_name} (already assigned to '{$otherProject->ProjectName}')";
                    continue;
                }
            }

            // Create project employee assignment
            $projectEmployee = ProjectEmployee::create([
                'ProjectID' => $project->ProjectID,
                'EmployeeID' => $employeeId,
                'role_in_project' => null, // No additional role needed
                'assigned_date' => now(),
                'status' => 'Active'
            ]);
            
            // Ensure QR code is generated
            if (!$projectEmployee->qr_code) {
                $projectEmployee->generateQrCode();
            }
            
            $assignedCount++;
        }

        $message = "Successfully assigned {$assignedCount} employee(s) to the project.";
        
        if (!empty($alreadyAssigned)) {
            $message .= " The following employees were already assigned: " . implode(', ', $alreadyAssigned) . ".";
        }
        
        if (!empty($foremanConflicts)) {
            $message .= " The following foremen could not be assigned (they can only be assigned to one project): " . implode(', ', $foremanConflicts) . ".";
        }

        return redirect()->back()
            ->with($assignedCount > 0 ? 'success' : 'error', $message);
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
        $newStatusId = Project::calculateStatus($project->StartDate, $project->EndDate, $project->WarrantyDays);
        $project->update(['StatusID' => $newStatusId]);
        
        $newStatus = $project->fresh()->status->StatusName;
        return redirect()->route('ProdHead.projects')
            ->with('success', "Project reactivated with status: {$newStatus}.");
    }

    /**
     * End project by marking it as completed.
     */
    public function endProject(Project $project)
    {
        // Check if project is already completed
        if ($project->status->StatusName == 'Completed') {
            return redirect()->back()
                ->with('error', 'Project is already completed.');
        }

        // Set project status to Completed
        $completedStatus = ProjectStatus::where('StatusName', 'Completed')->first();
        
        if (!$completedStatus) {
            return redirect()->back()
                ->with('error', 'Could not find Completed status. Please contact administrator.');
        }

        $project->update(['StatusID' => $completedStatus->StatusID]);

        return redirect()->back()
            ->with('success', 'Project has been marked as completed successfully.');
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

        // Check for foreman conflicts before syncing
        $foremanConflicts = [];
        $validEmployeeIds = [];
        
        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::with('position')->find($employeeId);
            
            if ($this->isForeman($employee)) {
                // Check if foreman is assigned to another project
                $otherProjectAssignment = ProjectEmployee::where('EmployeeID', $employeeId)
                    ->where('ProjectID', '!=', $project->ProjectID)
                    ->where('status', 'Active')
                    ->first();

                if ($otherProjectAssignment) {
                    $otherProject = Project::find($otherProjectAssignment->ProjectID);
                    $foremanConflicts[] = "{$employee->full_name} (already assigned to '{$otherProject->ProjectName}')";
                    continue;
                }
            }
            
            $validEmployeeIds[] = $employeeId;
        }
        
        // Sync only valid employees
        $project->employees()->sync($validEmployeeIds);
        
        $message = 'Employees assigned to project successfully.';
        if (!empty($foremanConflicts)) {
            $message .= ' However, the following foremen could not be assigned (they can only be assigned to one project): ' . implode(', ', $foremanConflicts) . '.';
        }

        return redirect()->route('projects.show', $project)
            ->with(!empty($foremanConflicts) ? 'warning' : 'success', $message);
    }

    /**
     * Check if an employee is a foreman based on their position.
     */
    private function isForeman(Employee $employee)
    {
        // Get the position relationship object, not the accessor
        $position = $employee->relationLoaded('position') 
            ? $employee->getRelation('position') 
            : $employee->position()->first();
            
        if (!$position) {
            return false;
        }

        // Check if position name contains "Foreman" (case-insensitive)
        return stripos($position->PositionName, 'Foreman') !== false;
    }

    /**
     * Proceed with NTP (Notice to Proceed) - Admin only
     * Approves project and sets start/end dates based on NTP
     */
    public function proceedWithNTP(Request $request, Project $project)
    {
        // Check if user is Admin (UserTypeID == 2)
        if (Auth::user()->UserTypeID != 2) {
            return redirect()->back()->with('error', 'Access denied. Only Admin can approve projects with NTP.');
        }

        // Check if project is in "Pending" status
        if ($project->status->StatusName !== 'Pending') {
            return redirect()->back()->with('error', 'This project is not pending approval.');
        }

        $request->validate([
            'NTPStartDate' => 'required|date|after_or_equal:today',
            'NTPAttachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ], [
            'NTPStartDate.after_or_equal' => 'The NTP Start Date must be today or a future date. You cannot select a past date.',
        ]);

        // Upload NTP attachment
        $attachmentPath = null;
        if ($request->hasFile('NTPAttachment')) {
            $file = $request->file('NTPAttachment');
            $fileName = 'ntp_' . $project->ProjectID . '_' . time() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('ntp_attachments', $fileName, 'public');
        }

        // Calculate EndDate = NTPStartDate + EstimatedAccomplishDays
        $ntpStartDate = \Carbon\Carbon::parse($request->NTPStartDate);
        $endDate = $ntpStartDate->copy()->addDays($project->EstimatedAccomplishDays);

        // Update project
        $project->NTPStartDate = $ntpStartDate;
        $project->StartDate = $ntpStartDate;
        $project->EndDate = $endDate;
        $project->NTPAttachment = $attachmentPath;
        
        // Recalculate milestone target dates now that StartDate is set
        $this->recalculateAllMilestoneDates($project);
        
        // Set status based on whether start date is in the future
        $today = now()->toDateString();
        $startDateStr = $ntpStartDate->toDateString();
        
        if ($startDateStr > $today) {
            // NTP approved but start date is in the future - Pre-Construction
            $status = \App\Models\ProjectStatus::where('StatusName', 'Pre-Construction')->first();
            if ($status) {
                $project->StatusID = $status->StatusID;
            }
        } else {
            // Start date is today or in the past - status will be auto-calculated
            // Status will be auto-calculated by the model's boot method
        }
        
        $project->save();
        
        // Recalculate milestone target dates now that StartDate is set
        $this->recalculateAllMilestoneDates($project);
        
        return redirect()->route('ProdHead.projects')
            ->with('success', 'Project approved with NTP. Start date and end date have been set. Milestone target dates have been calculated.');
    }

    /**
     * Recalculate target dates for all milestones
     */
    private function recalculateAllMilestoneDates(Project $project)
    {
        if (!$project->StartDate) {
            return;
        }

        $milestones = $project->milestones()->orderBy('order')->orderBy('milestone_id')->get();
        $cumulativeDays = 0;

        foreach ($milestones as $ms) {
            if ($ms->EstimatedDays) {
                $cumulativeDays += $ms->EstimatedDays;
                $ms->target_date = \Carbon\Carbon::parse($project->StartDate)->addDays($cumulativeDays);
                $ms->saveQuietly();
            }
        }
    }

    /**
     * Generate PDF with all project employee QR codes.
     */
    public function generateEmployeeQrPdf(Project $project)
    {
        $project->load(['projectEmployees.employee.position']);
        
        $pdf = Pdf::loadView('projects.employees-qr-pdf', compact('project'));
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Project_' . str_replace(' ', '_', $project->ProjectName) . '_QR_Codes.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Upload attachment for a project.
     */
    public function uploadAttachment(Request $request, Project $project)
    {
        $request->validate([
            'attachment_type' => 'required|in:blueprint,floorplan,ntp',
            'attachment' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $type = $request->input('attachment_type');
        $file = $request->file('attachment');
        
        // Generate filename
        $filename = 'project_' . $project->ProjectID . '_' . $type . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs('project_attachments', $filename, 'public');

        // Update project based on type
        switch ($type) {
            case 'blueprint':
                // Delete old file if exists
                if ($project->BlueprintPath) {
                    Storage::disk('public')->delete($project->BlueprintPath);
                }
                $project->BlueprintPath = $path;
                break;
            case 'floorplan':
                // Delete old file if exists
                if ($project->FloorPlanPath) {
                    Storage::disk('public')->delete($project->FloorPlanPath);
                }
                $project->FloorPlanPath = $path;
                break;
            case 'ntp':
                // Delete old file if exists
                if ($project->NTPAttachment) {
                    Storage::disk('public')->delete($project->NTPAttachment);
                }
                $project->NTPAttachment = $path;
                break;
        }

        $project->save();

        return redirect()->back()->with('success', ucfirst($type) . ' uploaded successfully.');
    }

}
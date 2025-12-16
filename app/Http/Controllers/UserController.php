<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\UserType;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeactivated = $request->has('show_deactivated') && $request->show_deactivated == '1';
        
        if ($showDeactivated) {
            // Show only deactivated users
            $users = User::with(['userType', 'employee', 'role'])
                ->where('FlagDeleted', 1)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Show only active users
            $users = User::with(['userType', 'employee', 'role'])->active()->orderBy('created_at', 'desc')->get();
        }
        
        // Get data for create modal
        $userTypes = UserType::active()->get();
        $employees = Employee::active()->whereDoesntHave('users')->get(); // Only employees without existing user accounts
        
        return view('users.index', compact('users', 'showDeactivated', 'userTypes', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userTypes = UserType::active()->get();
        $employees = Employee::active()->whereDoesntHave('users')->get(); // Only employees without existing user accounts
        return view('users.create', compact('userTypes', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'EmployeeID' => 'required|exists:employees,id',
            'Email' => 'required|email|unique:users,Email',
            'Password' => 'required|string|min:6',
            'UserTypeID' => 'required|exists:tblusertype,UserTypeID',
        ]);

        $user = new User();
        $user->EmployeeID = $request->EmployeeID;
        $user->Email = $request->Email;
        $user->Password = bcrypt($request->Password);
        $user->UserTypeID = $request->UserTypeID;
        
        // Get employee details to populate user fields
        $employee = Employee::findOrFail($request->EmployeeID);
        $user->ContactNumber = $employee->contact_number ?? null;
        
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['userType', 'employee', 'role'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $userTypes = UserType::active()->get();
        $employees = Employee::active()->get();
        return view('users.edit', compact('user', 'userTypes', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'EmployeeID' => 'required|exists:employees,id',
            'Email' => 'required|email|unique:users,Email,' . $id,
            'UserTypeID' => 'required|exists:tblusertype,UserTypeID',
        ]);

        $user->EmployeeID = $request->EmployeeID;
        $user->Email = $request->Email;
        $user->UserTypeID = $request->UserTypeID;
        
        // Update password if provided
        if ($request->filled('Password')) {
            $request->validate(['Password' => 'string|min:6']);
            $user->Password = bcrypt($request->Password);
        }
        
        // Get employee details to update user fields
        $employee = Employee::findOrFail($request->EmployeeID);
        $user->ContactNumber = $employee->contact_number ?? null;
        
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage (Deactivate).
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->FlagDeleted = 1;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }

    /**
     * Reactivate the specified user.
     */
    public function reactivate(string $id)
    {
        $user = User::findOrFail($id);
        $user->FlagDeleted = 0;
        $user->save();

        return redirect()->route('users.index', ['show_deactivated' => '1'])->with('success', 'User reactivated successfully.');
    }

    public function showProdHead()
    {
        return view('ProdHeadPage.ProductionHeadPage');
    }

    public function showAdminEmployeeFunction()
    {
        $employees = Employee::active()->orderBy('created_at', 'desc')->paginate(15);
        return view('Admin.employees.index', compact('employees'));
    }

    public function showAdmin()
    {
        // Summary card counts
        $pendingRequests = \App\Models\InventoryRequest::where('Status', 'Pending')->count();
        
        $inProgressProjects = \App\Models\Project::whereHas('status', function($q) {
            $q->where('StatusName', 'On Going');
        })->count();
        
        $employeeCount = \App\Models\Employee::active()->count();
        
        $pendingNTPProjects = \App\Models\Project::whereHas('status', function($q) {
            $q->where('StatusName', 'Pending');
        })->whereNull('NTPStartDate')->count();
        
        // Active projects for progress bars
        $activeProjects = \App\Models\Project::with(['status', 'milestones', 'client'])
            ->whereHas('status', function($q) {
                $q->whereIn('StatusName', ['On Going', 'Pre-Construction']);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Equipment assignments - currently assigned equipment
        $equipmentAssignments = \App\Models\ProjectMilestoneEquipment::with([
                'item.resourceCatalog', 
                'milestone.project'
            ])
            ->where('Status', 'Assigned')
            ->orderBy('DateAssigned', 'desc')
            ->take(10)
            ->get();
        
        return view('AdminIndex', compact(
            'pendingRequests',
            'inProgressProjects', 
            'employeeCount',
            'pendingNTPProjects',
            'activeProjects',
            'equipmentAssignments'
        ));
    }
}
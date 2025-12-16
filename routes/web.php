<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RegularEmployeeController;
use App\Http\Controllers\OnCallEmployeeController;


Route::resource('employees', App\Http\Controllers\EmployeeController::class)->middleware('auth');
Route::patch('employees/{employee}/unarchive', [App\Http\Controllers\EmployeeController::class, 'unarchive'])->name('employees.unarchive')->middleware('auth');
Route::resource('users', App\Http\Controllers\UserController::class)->middleware('auth');
Route::patch('users/{user}/reactivate', [App\Http\Controllers\UserController::class, 'reactivate'])->name('users.reactivate')->middleware('auth');
Route::resource('clients', ClientController::class)->middleware(['auth', 'checkRole:2']);

// API route for fetching position salary
Route::get('/api/position-salary/{positionId}', function ($positionId) {
    $position = \App\Models\Position::find($positionId);
    return response()->json([
        'salary' => $position ? $position->Salary : null
    ]);
})->middleware('auth');

// Position Management Routes
Route::resource('positions', App\Http\Controllers\PositionController::class)->middleware('auth');

// Separate Employee Management Routes
Route::resource('regular-employees', RegularEmployeeController::class)->middleware('auth');
Route::resource('oncall-employees', OnCallEmployeeController::class)->middleware('auth');

Route::get('/', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');

Route::post('/page', [AuthController::class, 'login'])->name('login_now');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Attendance Management Routes (Attendant Officer only)
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/project/{projectId}', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.project');
    Route::post('/attendance/mark', [App\Http\Controllers\AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/attendance/export/csv', [App\Http\Controllers\AttendanceController::class, 'exportCsv'])->name('attendance.export.csv');
    Route::get('/attendance/employee/{employeeId}/history', [App\Http\Controllers\AttendanceController::class, 'getEmployeeHistory'])->name('attendance.employee.history');
    Route::put('/attendance/{id}', [App\Http\Controllers\AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{id}', [App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::get('/attendance/qr-codes', [App\Http\Controllers\AttendanceController::class, 'showQrCodes'])->name('attendance.qr-codes');
    Route::get('/attendance/qr-code/{employeeId}', [App\Http\Controllers\AttendanceController::class, 'generateQrCode'])->name('attendance.qr-code');
    Route::get('/attendance/qr-generator', function() {
        return view('attendance.qr-generator');
    })->name('attendance.qr-generator');
    Route::get('/attendance/camera-test', function() {
        return view('attendance.camera-test');
    })->name('attendance.camera-test');
    
    // API route for employee info (for QR scanner)
    Route::get('/api/employee/{id}', function($id) {
        $employee = App\Models\Employee::find($id);
        if ($employee) {
            // Build full name from individual name fields
            $fullName = trim($employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name);
            
            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'full_name' => $fullName,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'position' => $employee->position ?? 'N/A'
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
    });
    
    // API route for employee lookup by QR code (now project-specific)
    Route::get('/api/employee/qr/{qrCode}', function(Request $request, $qrCode) {
        $projectId = $request->get('project_id');
        
        // Find project employee by QR code
        $projectEmployee = App\Models\ProjectEmployee::where('qr_code', $qrCode)->first();
        
        if ($projectEmployee) {
            // If project ID is provided, verify it matches
            if ($projectId && $projectEmployee->ProjectID != $projectId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'QR code is not valid for this project'
                ], 403);
            }
            
            // Check if the project employee is active
            if ($projectEmployee->status !== 'Active') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Employee is not active in this project'
                ], 403);
            }
            
            $employee = $projectEmployee->employee;
            if ($employee) {
                // Build full name from individual name fields
                $fullName = trim($employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name);
                
                return response()->json([
                    'success' => true,
                    'employee' => [
                        'id' => $employee->id,
                        'full_name' => $fullName,
                        'first_name' => $employee->first_name,
                        'last_name' => $employee->last_name,
                        'position' => $employee->position ?? 'N/A',
                        'qr_code' => $projectEmployee->qr_code,
                        'project_id' => $projectEmployee->ProjectID
                    ]
                ]);
            }
        }
        
        return response()->json(['success' => false, 'message' => 'Employee not found for QR code'], 404);
    });

    // API route to check employee's current attendance status for today
    Route::get('/api/employee/{id}/attendance-status', function(Request $request, $id) {
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $employee = App\Models\Employee::find($id);
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }
        
        // Check today's attendance record
        $attendance = App\Models\Attendance::where('employee_id', $id)
            ->where('attendance_date', $date)
            ->first();
        
        $status = [
            'has_time_in' => false,
            'has_time_out' => false,
            'next_action' => 'time_in', // Default to time_in
            'time_in' => null,
            'time_out' => null,
            'status' => null,
            'can_time_in' => true,
            'can_time_out' => false,
            'is_completed' => false
        ];
        
        if ($attendance) {
            $status['has_time_in'] = !is_null($attendance->time_in);
            $status['has_time_out'] = !is_null($attendance->time_out);
            $status['time_in'] = $attendance->time_in;
            $status['time_out'] = $attendance->time_out;
            $status['status'] = $attendance->status;
            
            // Determine what actions are allowed
            $status['can_time_in'] = !$status['has_time_in'];
            $status['can_time_out'] = $status['has_time_in'] && !$status['has_time_out'];
            $status['is_completed'] = $status['has_time_in'] && $status['has_time_out'];
            
            // Determine next action
            if (!$status['has_time_in']) {
                $status['next_action'] = 'time_in';
            } elseif (!$status['has_time_out']) {
                $status['next_action'] = 'time_out';
            } else {
                $status['next_action'] = 'completed'; // Both time_in and time_out are done
            }
        }
        
        return response()->json([
            'success' => true,
            'attendance_status' => $status
        ]);
    });
});


Route::get('/production', [UserController::class, 'showProdHead'])->name('showProdHead')->middleware('auth', 'checkRole:1');
Route::get('/admin', [UserController::class, 'showAdmin'])->name('showAdmin')->middleware('auth', 'checkRole:2');

// Foreman Routes
Route::get('/foreman/projects', [App\Http\Controllers\ForemanController::class, 'myProjects'])->name('foreman.projects')->middleware('auth', 'checkRole:3');
Route::get('/foreman/projects/{project}', [App\Http\Controllers\ForemanController::class, 'show'])->name('foreman.projects.show')->middleware('auth', 'checkRole:3');

// Production Head Attendance Management Routes
Route::get('/production/attendance', [App\Http\Controllers\AttendanceController::class, 'prodHeadOverview'])->name('prodhead.attendance')->middleware('auth', 'checkRole:1,2');
Route::get('/production/attendance/export-pdf', [App\Http\Controllers\AttendanceController::class, 'exportAttendancePdf'])->name('prodhead.attendance.pdf')->middleware('auth', 'checkRole:1,2');
Route::get('/hr', [UserController::class, 'showAdmin'])->name('go_newPage')->middleware('auth');

// Admin Projects Route
Route::get('/production/projects', [AuthController::class, 'Projects'])->name('ProdHead.projects')->middleware('auth');
Route::post('/projects/{project}/onHold',
    [ProjectController::class, 'onHold']
)->name('onHold')->middleware('auth');



// Project Management Routes
Route::get('projects', [AuthController::class, 'Projects'])->name('projects.index')->middleware('auth');
Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('auth');
Route::post('projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('auth');
Route::get('projects/{project}/create-milestones', [ProjectController::class, 'createMilestones'])->name('projects.create-milestones')->middleware('auth');
Route::post('projects/{project}/complete', [ProjectController::class, 'completeCreation'])->name('projects.complete')->middleware('auth');
Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show')->middleware('auth');
Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('auth');
Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('auth');
Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('auth');
Route::patch('projects/{project}/on-hold', [ProjectController::class, 'onHold'])->name('projects.onHold')->middleware('auth');
Route::patch('projects/{project}/reactivate', [ProjectController::class, 'reactivate'])->name('projects.reactivate')->middleware('auth');
Route::post('projects/{project}/end', [ProjectController::class, 'endProject'])->name('projects.end')->middleware('auth');
Route::post('projects/{project}/proceed-ntp', [ProjectController::class, 'proceedWithNTP'])->name('projects.proceed-ntp')->middleware('auth');
Route::post('projects/{project}/assign-employees', [ProjectController::class, 'assignEmployees'])->name('projects.assignEmployees')->middleware('auth');
Route::delete('projects/{project}/employees/{employee}', [ProjectController::class, 'removeEmployee'])->name('projects.removeEmployee')->middleware('auth');
Route::post('projects/{project}/upload-attachment', [ProjectController::class, 'uploadAttachment'])->name('projects.uploadAttachment')->middleware('auth');

// Project Employee Management Routes
Route::get('projects/{project}/manage-employees', [ProjectController::class, 'manageEmployees'])->name('projects.manage-employees')->middleware('auth');
Route::get('projects/{project}/employees/qr-pdf', [ProjectController::class, 'generateEmployeeQrPdf'])->name('projects.employees.qr-pdf')->middleware('auth');
Route::post('projects/{project}/assign-employee', [ProjectController::class, 'assignSingleEmployee'])->name('projects.assign-employee')->middleware('auth');
Route::post('projects/{project}/assign-multiple-employees', [ProjectController::class, 'assignMultipleEmployees'])->name('projects.assign-multiple-employees')->middleware('auth');
Route::patch('projects/{project}/assignments/{assignment}/complete', [ProjectController::class, 'completeEmployeeJob'])->name('projects.assignments.complete')->middleware('auth');
Route::delete('projects/{project}/assignments/{assignment}', [ProjectController::class, 'removeEmployee'])->name('projects.assignments.remove')->middleware('auth');

// Project Milestone Routes
Route::post('projects/{project}/milestones', [App\Http\Controllers\ProjectMilestoneController::class, 'store'])->name('projects.milestones.store')->middleware('auth');
Route::put('projects/{project}/milestones/{milestone}', [App\Http\Controllers\ProjectMilestoneController::class, 'update'])->name('projects.milestones.update')->middleware('auth');
Route::delete('projects/{project}/milestones/{milestone}', [App\Http\Controllers\ProjectMilestoneController::class, 'destroy'])->name('projects.milestones.destroy')->middleware('auth');
Route::post('projects/{project}/milestones/{milestone}/submit', [App\Http\Controllers\ProjectMilestoneController::class, 'submitCompletion'])->name('projects.milestones.submit')->middleware('auth');
Route::post('projects/{project}/milestones/{milestone}/approve', [App\Http\Controllers\ProjectMilestoneController::class, 'approveCompletion'])->name('projects.milestones.approve')->middleware('auth');
Route::post('projects/{project}/milestones/{milestone}/reject', [App\Http\Controllers\ProjectMilestoneController::class, 'rejectCompletion'])->name('projects.milestones.reject')->middleware('auth');

// API route for fetching project milestones
Route::get('/api/projects/{project}/milestones', function($project) {
    $project = \App\Models\Project::find($project);
    if (!$project) {
        return response()->json([], 404);
    }
    $milestones = $project->milestones()->orderBy('milestone_name')->get();
    return response()->json($milestones);
})->middleware('auth');

// Inventory Routes
Route::resource('inventory', App\Http\Controllers\InventoryItemController::class)->middleware('auth');
Route::get('inventory/low-stock', [App\Http\Controllers\InventoryItemController::class, 'lowStock'])->name('inventory.low-stock')->middleware('auth');

// Milestone Inventory Routes
Route::post('projects/{project}/milestones/{milestone}/materials', [App\Http\Controllers\ProjectMilestoneMaterialController::class, 'store'])->name('milestones.materials.store')->middleware('auth');
Route::delete('projects/{project}/milestones/{milestone}/materials/{material}', [App\Http\Controllers\ProjectMilestoneMaterialController::class, 'destroy'])->name('milestones.materials.destroy')->middleware('auth');
Route::post('projects/{project}/milestones/{milestone}/equipment', [App\Http\Controllers\ProjectMilestoneEquipmentController::class, 'store'])->name('milestones.equipment.store')->middleware('auth');
Route::patch('projects/{project}/milestones/{milestone}/equipment/{equipment}/return', [App\Http\Controllers\ProjectMilestoneEquipmentController::class, 'return'])->name('milestones.equipment.return')->middleware('auth');
Route::delete('projects/{project}/milestones/{milestone}/equipment/{equipment}', [App\Http\Controllers\ProjectMilestoneEquipmentController::class, 'destroy'])->name('milestones.equipment.destroy')->middleware('auth');

// Inventory Request Routes
Route::get('inventory-requests/items/options', [App\Http\Controllers\InventoryRequestController::class, 'availableItems'])
    ->name('inventory.requests.items')
    ->middleware('auth');
Route::get('inventory-requests/history', [App\Http\Controllers\InventoryRequestController::class, 'history'])->name('inventory.requests.history')->middleware('auth');
Route::get('api/milestones/{milestone}/required-items', [App\Http\Controllers\ProjectMilestoneController::class, 'getRequiredItems'])
    ->name('api.milestones.required-items')
    ->middleware('auth');
Route::get('api/milestones/{milestone}/proof-images', [App\Http\Controllers\ProjectMilestoneController::class, 'getProofImages'])
    ->name('api.milestones.proof-images')
    ->middleware('auth');
Route::resource('inventory-requests', App\Http\Controllers\InventoryRequestController::class)->names([
    'index' => 'inventory.requests.index',
    'create' => 'inventory.requests.create',
    'store' => 'inventory.requests.store',
    'show' => 'inventory.requests.show',
    'edit' => 'inventory.requests.edit',
    'update' => 'inventory.requests.update',
    'destroy' => 'inventory.requests.destroy',
])->middleware('auth');
Route::post('inventory-requests/{inventoryRequest}/approve', [App\Http\Controllers\InventoryRequestController::class, 'approve'])->name('inventory.requests.approve')->middleware('auth');
Route::post('inventory-requests/{inventoryRequest}/reject', [App\Http\Controllers\InventoryRequestController::class, 'reject'])->name('inventory.requests.reject')->middleware('auth');
Route::get('inventory-requests/{inventoryRequest}/purchase-order', [App\Http\Controllers\InventoryRequestController::class, 'purchaseOrderForm'])->name('inventory.requests.purchase-order')->middleware('auth');
Route::post('inventory-requests/{inventoryRequest}/purchase-order', [App\Http\Controllers\InventoryRequestController::class, 'savePurchaseOrder'])->name('inventory.requests.save-purchase-order')->middleware('auth');

// Supplier Routes
Route::resource('suppliers', App\Http\Controllers\SupplierController::class)->middleware('auth');

// Resource Catalog Routes
Route::resource('resource-catalog', App\Http\Controllers\ResourceCatalogController::class)->middleware('auth');
// API endpoint for resource catalog items (for JS usage)
Route::get('api/resource-catalog/items', [App\Http\Controllers\ResourceCatalogController::class, 'items'])
    ->name('api.resource-catalog.items')
    ->middleware('auth');

// Purchase Order Routes
Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)->middleware('auth');
Route::patch('purchase-orders/{purchaseOrder}/approve', [App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve')->middleware('auth');
Route::patch('purchase-orders/{purchaseOrder}/mark-sent', [App\Http\Controllers\PurchaseOrderController::class, 'markAsSent'])->name('purchase-orders.mark-sent')->middleware('auth');
Route::patch('purchase-orders/{purchaseOrder}/cancel', [App\Http\Controllers\PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel')->middleware('auth');
Route::get('purchase-orders/{purchaseOrder}/pdf', [App\Http\Controllers\PurchaseOrderController::class, 'generatePDF'])->name('purchase-orders.pdf')->middleware('auth');

// Receiving Routes
Route::resource('receiving', App\Http\Controllers\ReceivingController::class)->middleware('auth');

// Issuance Routes
Route::resource('issuance', App\Http\Controllers\IssuanceController::class)->middleware('auth');
Route::get('issuance/{issuance}/pdf', [App\Http\Controllers\IssuanceController::class, 'generatePDF'])->name('issuance.pdf')->middleware('auth');

// Equipment Return Routes
Route::get('equipment/returns', [App\Http\Controllers\EquipmentReturnController::class, 'index'])->name('equipment.returns.index')->middleware('auth');
Route::get('equipment/returns/{id}/create', [App\Http\Controllers\EquipmentReturnController::class, 'create'])->name('equipment.returns.create')->middleware('auth');
Route::post('equipment/returns/{id}', [App\Http\Controllers\EquipmentReturnController::class, 'store'])->name('equipment.returns.store')->middleware('auth');
Route::get('equipment/incidents', [App\Http\Controllers\EquipmentReturnController::class, 'incidents'])->name('equipment.incidents.index')->middleware('auth');
Route::get('equipment/incidents/{id}', [App\Http\Controllers\EquipmentReturnController::class, 'showIncident'])->name('equipment.incidents.show')->middleware('auth');
Route::patch('equipment/incidents/{id}', [App\Http\Controllers\EquipmentReturnController::class, 'updateIncident'])->name('equipment.incidents.update')->middleware('auth');

// Milestone Resource Planning Routes
Route::get('milestones/{milestone}/resources', [App\Http\Controllers\MilestoneResourceController::class, 'index'])->name('milestones.resources.index')->middleware('auth');
Route::get('milestones/{milestone}/resources/create', [App\Http\Controllers\MilestoneResourceController::class, 'create'])->name('milestones.resources.create')->middleware('auth');
Route::post('milestones/{milestone}/resources', [App\Http\Controllers\MilestoneResourceController::class, 'store'])->name('milestones.resources.store')->middleware('auth');
Route::delete('milestones/{milestone}/resources/{plan}', [App\Http\Controllers\MilestoneResourceController::class, 'destroy'])->name('milestones.resources.destroy')->middleware('auth');
Route::get('milestones/resources/{plan}/generate-request', [App\Http\Controllers\MilestoneResourceController::class, 'generateRequest'])->name('milestones.resources.generate-request')->middleware('auth');

// Inventory Dashboard Routes
Route::get('inventory/dashboard', [App\Http\Controllers\InventoryDashboardController::class, 'index'])->name('inventory.dashboard')->middleware('auth');
Route::get('inventory/dashboard/materials', [App\Http\Controllers\InventoryDashboardController::class, 'materials'])->name('inventory.dashboard.materials')->middleware('auth');
Route::get('inventory/dashboard/equipment', [App\Http\Controllers\InventoryDashboardController::class, 'equipment'])->name('inventory.dashboard.equipment')->middleware('auth');
Route::get('inventory/dashboard/alerts', [App\Http\Controllers\InventoryDashboardController::class, 'alerts'])->name('inventory.dashboard.alerts')->middleware('auth');

// Inventory Reports Routes
Route::get('reports/inventory', [App\Http\Controllers\InventoryReportController::class, 'index'])->name('reports.inventory.index')->middleware('auth');
Route::get('reports/inventory/stock-level', [App\Http\Controllers\InventoryReportController::class, 'stockLevel'])->name('reports.inventory.stock-level')->middleware('auth');
Route::get('reports/inventory/consumption', [App\Http\Controllers\InventoryReportController::class, 'consumption'])->name('reports.inventory.consumption')->middleware('auth');
Route::get('reports/inventory/equipment-utilization', [App\Http\Controllers\InventoryReportController::class, 'equipmentUtilization'])->name('reports.inventory.equipment-utilization')->middleware('auth');
Route::get('reports/inventory/po-summary', [App\Http\Controllers\InventoryReportController::class, 'purchaseOrderSummary'])->name('reports.inventory.po-summary')->middleware('auth');
Route::get('reports/inventory/issuance-history', [App\Http\Controllers\InventoryReportController::class, 'issuanceHistory'])->name('reports.inventory.issuance-history')->middleware('auth');
Route::get('reports/inventory/damage-report', [App\Http\Controllers\InventoryReportController::class, 'damageReport'])->name('reports.inventory.damage-report')->middleware('auth');

// Milestone Reports Routes
Route::get('reports/milestones', [App\Http\Controllers\MilestoneReportController::class, 'index'])->name('reports.milestones.index')->middleware('auth');
Route::get('reports/milestones/{milestone}', [App\Http\Controllers\MilestoneReportController::class, 'show'])->name('reports.milestones.show')->middleware('auth');
Route::get('reports/milestones/{milestone}/pdf', [App\Http\Controllers\MilestoneReportController::class, 'exportPdf'])->name('reports.milestones.pdf')->middleware('auth');

// Jaspersoft Enterprise Reports
Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware('auth');
Route::get('reports/purchase-order/{id}', [App\Http\Controllers\ReportController::class, 'generatePurchaseOrder'])->name('reports.purchase-order')->middleware('auth');
Route::get('reports/inventory-jasper', [App\Http\Controllers\ReportController::class, 'generateInventory'])->name('reports.inventory')->middleware('auth');

Route::get('/admin/EmployeeManagement', [UserController::class, 'showAdminEmployeeFunction'])->name('admin.employees.index')->middleware('auth');
<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\InventoryItem;
use App\Models\InventoryItemType;
use App\Models\Position;
use App\Models\Project;
use App\Models\ProjectEmployee;
use App\Models\ProjectMilestone;
use App\Models\ProjectStatus;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventoryRequestStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_foreman_can_submit_request_even_when_stock_is_low(): void
    {
        $foremanPosition = Position::create([
            'PositionName' => 'Foreman',
        ]);

        $employeeStatus = EmployeeStatus::create([
            'StatusName' => 'Active',
            'Description' => 'Employee is currently active',
        ]);

        $employee = Employee::create([
            'first_name' => 'Juan',
            'middle_name' => 'D',
            'last_name' => 'Cruz',
            'birthday' => now()->subYears(30)->toDateString(),
            'house_number' => '123',
            'street' => 'Main St',
            'barangay' => 'Central',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'PositionID' => $foremanPosition->PositionID,
            'employee_status_id' => $employeeStatus->EmployeeStatusID,
            'base_salary' => 15000,
            'start_date' => now()->subMonth()->toDateString(),
            'contact_number' => '09123456789',
        ]);

        $userType = UserType::create([
            'UserType' => 'Foreman',
            'FlagDeleted' => 0,
        ]);

        $user = new User();
        $user->FirstName = 'Juan';
        $user->MiddleName = 'D';
        $user->LastName = 'Cruz';
        $user->Sex = 'Male';
        $user->ContactNumber = '09123456789';
        $user->Email = 'foreman@example.com';
        $user->Username = 'foreman_user';
        $user->Password = Hash::make('password');
        $user->UserTypeID = $userType->UserTypeID;
        $user->EmployeeID = $employee->id;
        $user->FlagDeleted = 0;
        $user->save();

        // Seed the minimum project statuses used by the Project model hooks.
        $pending = ProjectStatus::create(['StatusName' => 'Pending']);
        $preConstruction = ProjectStatus::create(['StatusName' => 'Pre-Construction']);
        $onGoing = ProjectStatus::create(['StatusName' => 'On Going']);
        $underWarranty = ProjectStatus::create(['StatusName' => 'Under Warranty']);
        $completed = ProjectStatus::create(['StatusName' => 'Completed']);

        $project = Project::create([
            'ProjectName' => 'Sample Project',
            'ProjectDescription' => 'Inventory request test project',
            'Client' => 'Client Corp',
            'StartDate' => now()->subWeek()->toDateString(),
            'EndDate' => now()->addWeek()->toDateString(),
            'StatusID' => $onGoing->StatusID,
        ]);

        $milestone = ProjectMilestone::create([
            'project_id' => $project->ProjectID,
            'milestone_name' => 'Initial Phase',
            'description' => 'Initial milestone for testing',
            'target_date' => now()->addDays(5)->toDateString(),
            'status' => 'Pending',
        ]);

        ProjectEmployee::create([
            'ProjectID' => $project->ProjectID,
            'EmployeeID' => $employee->id,
            'status' => 'Active',
            'assigned_date' => now()->toDateString(),
        ]);

        $equipmentType = InventoryItemType::create([
            'TypeName' => 'Equipment',
        ]);

        $inventoryItem = InventoryItem::create([
            'ItemTypeID' => $equipmentType->ItemTypeID,
            'ItemName' => 'Concrete Mixer',
            'ItemCode' => 'CM-001',
            'Unit' => 'pcs',
            'TotalQuantity' => 1,
            'AvailableQuantity' => 1,
            'MinimumStockLevel' => 1,
            'UnitPrice' => 5000,
            'Status' => 'Active',
        ]);

        $response = $this->actingAs($user)->post(route('inventory.requests.store'), [
            'ProjectID' => $project->ProjectID,
            'MilestoneID' => $milestone->milestone_id,
            'Reason' => 'Need more equipment',
            'items' => [
                [
                    'inventory_item_id' => $inventoryItem->ItemID,
                    'quantity' => 5, // More than available stock.
                ],
            ],
        ]);

        $response->assertRedirect(route('inventory.requests.index'));
        $response->assertSessionHas('warning');

        $this->assertDatabaseHas('inventory_requests', [
            'ProjectID' => $project->ProjectID,
            'EmployeeID' => $employee->id,
            'Status' => 'Pending - To Order',
        ]);

        $this->assertDatabaseHas('inventory_request_items', [
            'InventoryItemID' => $inventoryItem->ItemID,
            'QuantityRequested' => 5,
            'NeedsPurchase' => 1,
        ]);
    }
}









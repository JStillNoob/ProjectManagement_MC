<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectMilestoneEquipment;
use App\Models\InventoryItem;

class ProjectMilestoneEquipmentController extends Controller
{
    /**
     * Assign equipment to milestone and decrease available quantity.
     */
    public function store(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $request->validate([
            'ItemID' => 'required|exists:inventory_items,ItemID',
            'QuantityAssigned' => 'required|numeric|min:0.01',
            'DateAssigned' => 'nullable|date',
            'Remarks' => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($request->ItemID);

        // Validate it's equipment
        if (!$item->is_equipment) {
            return redirect()->back()
                ->with('error', 'Selected item is not equipment. Please select an equipment item.');
        }

        // Validate availability
        if ($item->AvailableQuantity < $request->QuantityAssigned) {
            return redirect()->back()
                ->with('error', "Insufficient equipment available. Available: {$item->AvailableQuantity} {$item->Unit}, Requested: {$request->QuantityAssigned} {$item->Unit}");
        }

        try {
            // Use the model's assignEquipment method which handles available quantity decrease
            $equipment = $item->assignEquipment($request->QuantityAssigned, $milestone->milestone_id);
            
            // Update additional fields if provided
            if ($request->DateAssigned) {
                $equipment->DateAssigned = $request->DateAssigned;
            }
            if ($request->Remarks) {
                $equipment->Remarks = $request->Remarks;
            }
            $equipment->save();

            return redirect()->back()
                ->with('success', "Equipment '{$item->ItemName}' assigned successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning equipment: ' . $e->getMessage());
        }
    }

    /**
     * Return equipment and increase available quantity.
     */
    public function return(Request $request, Project $project, ProjectMilestone $milestone, ProjectMilestoneEquipment $equipment)
    {
        $request->validate([
            'Status' => 'required|in:Returned,Damaged,Missing',
            'ReturnRemarks' => 'nullable|string',
        ]);

        // Verify the equipment belongs to this milestone
        if ($equipment->milestone_id != $milestone->milestone_id) {
            return redirect()->back()
                ->with('error', 'Invalid equipment assignment.');
        }

        // Check if already returned
        if ($equipment->Status == 'Returned') {
            return redirect()->back()
                ->with('error', 'Equipment is already returned.');
        }

        try {
            $item = $equipment->item;
            
            // Use the model's returnEquipment method
            $equipment->returnEquipment($request->Status, $request->ReturnRemarks);

            $statusMessage = $request->Status == 'Returned' 
                ? "Equipment '{$item->ItemName}' returned successfully. Available quantity restored."
                : "Equipment '{$item->ItemName}' marked as {$request->Status}.";

            return redirect()->back()
                ->with('success', $statusMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error returning equipment: ' . $e->getMessage());
        }
    }

    /**
     * Remove equipment assignment (reverse if not returned).
     */
    public function destroy(Project $project, ProjectMilestone $milestone, ProjectMilestoneEquipment $equipment)
    {
        // Verify the equipment belongs to this milestone
        if ($equipment->milestone_id != $milestone->milestone_id) {
            return redirect()->back()
                ->with('error', 'Invalid equipment assignment.');
        }

        $item = $equipment->item;
        $quantityAssigned = $equipment->QuantityAssigned;
        $wasReturned = $equipment->Status == 'Returned';

        // If not returned, restore available quantity
        if (!$wasReturned) {
            $item->AvailableQuantity += $quantityAssigned;
            $item->save();
        }

        // Delete the equipment assignment record
        $equipment->delete();

        return redirect()->back()
            ->with('success', "Equipment '{$item->ItemName}' removed" . ($wasReturned ? '.' : ' and available quantity restored.'));
    }
}

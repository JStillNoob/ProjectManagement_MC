<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectMilestoneMaterial;
use App\Models\InventoryItem;

class ProjectMilestoneMaterialController extends Controller
{
    /**
     * Assign material to milestone and auto-consume.
     */
    public function store(Request $request, Project $project, ProjectMilestone $milestone)
    {
        $request->validate([
            'ItemID' => 'required|exists:inventory_items,ItemID',
            'QuantityUsed' => 'required|numeric|min:0.01',
            'DateUsed' => 'nullable|date',
            'Remarks' => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($request->ItemID);

        // Validate it's a material
        if (!$item->is_material) {
            return redirect()->back()
                ->with('error', 'Selected item is not a material. Please select a material item.');
        }

        // Validate availability
        if ($item->AvailableQuantity < $request->QuantityUsed) {
            return redirect()->back()
                ->with('error', "Insufficient stock. Available: {$item->AvailableQuantity} {$item->Unit}, Requested: {$request->QuantityUsed} {$item->Unit}");
        }

        try {
            // Use the model's consumeMaterial method which handles stock decrease
            $material = $item->consumeMaterial($request->QuantityUsed, $milestone->milestone_id);
            
            // Update additional fields if provided
            if ($request->DateUsed) {
                $material->DateUsed = $request->DateUsed;
            }
            if ($request->Remarks) {
                $material->Remarks = $request->Remarks;
            }
            $material->save();

            return redirect()->back()
                ->with('success', "Material '{$item->ItemName}' assigned and consumed successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning material: ' . $e->getMessage());
        }
    }

    /**
     * Remove material assignment (reverse consumption if possible).
     */
    public function destroy(Project $project, ProjectMilestone $milestone, ProjectMilestoneMaterial $material)
    {
        // Verify the material belongs to this milestone
        if ($material->milestone_id != $milestone->milestone_id) {
            return redirect()->back()
                ->with('error', 'Invalid material assignment.');
        }

        $item = $material->item;
        $quantityUsed = $material->QuantityUsed;

        // Reverse the consumption - increase stock back
        $item->TotalQuantity += $quantityUsed;
        $item->AvailableQuantity += $quantityUsed;
        $item->save();

        // Delete the material usage record
        $material->delete();

        return redirect()->back()
            ->with('success', "Material '{$item->ItemName}' removed and stock restored.");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuanceRecord extends Model
{
    use HasFactory;

    protected $table = 'issuance_records';
    protected $primaryKey = 'IssuanceID';

    protected $fillable = [
        'IssuanceNumber',
        'RequestID',
        'ProjectID',
        'MilestoneID',
        'IssuanceDate',
        'IssuedBy',
        'ReceivedBy',
        'Status',
        'Purpose',
        'Remarks',
        'SignaturePath',
    ];

    protected $casts = [
        'IssuanceDate' => 'date',
    ];

    /**
     * Generate unique issuance number
     */
    public static function generateIssuanceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "ISS-{$year}{$month}-";
        
        $lastIssuance = self::where('IssuanceNumber', 'like', $prefix . '%')
            ->orderBy('IssuanceNumber', 'desc')
            ->first();
        
        if ($lastIssuance) {
            $lastNumber = intval(substr($lastIssuance->IssuanceNumber, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function inventoryRequest()
    {
        return $this->belongsTo(InventoryRequest::class, 'RequestID', 'RequestID');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'ProjectID', 'ProjectID');
    }

    public function milestone()
    {
        return $this->belongsTo(ProjectMilestone::class, 'MilestoneID', 'milestone_id');
    }

    public function issuer()
    {
        return $this->belongsTo(Employee::class, 'IssuedBy', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'ReceivedBy', 'id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(Employee::class, 'IssuedBy', 'id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(Employee::class, 'ReceivedBy', 'id');
    }

    public function items()
    {
        return $this->hasMany(IssuanceRecordItem::class, 'IssuanceID', 'IssuanceID');
    }

    /**
     * Process issuance and update inventory
     * For Materials: Decrease TotalQuantity and CommittedQuantity (stock is consumed)
     * For Equipment: Decrease CommittedQuantity (already reserved at approval), update status to In Use
     */
    public function processIssuance()
    {
        foreach ($this->items as $item) {
            $inventoryItem = $item->inventoryItem;
            
            if ($item->ItemType == 'Material') {
                // Permanent deduction for materials
                // TotalQuantity decreases (stock leaves warehouse)
                // CommittedQuantity decreases (reservation is fulfilled)
                // AvailableQuantity stays same (was already reduced at approval)
                $inventoryItem->TotalQuantity -= $item->QuantityIssued;
                $inventoryItem->CommittedQuantity -= $item->QuantityIssued;
            } else {
                // Equipment issuance
                // CommittedQuantity decreases (reservation is fulfilled)
                // TotalQuantity stays same (equipment is not consumed, just assigned)
                // AvailableQuantity stays same (was already reduced at approval)
                $inventoryItem->CommittedQuantity -= $item->QuantityIssued;
                // Note: inventory_items.Status only accepts 'Active' or 'Inactive', not 'In Use'
                // Equipment tracking is done via project_milestone_equipment table
                
                // Create equipment assignment record for tracking
                // Status must be one of: 'Assigned', 'Returned', 'Damaged', 'Missing'
                \App\Models\ProjectMilestoneEquipment::create([
                    'milestone_id' => $this->MilestoneID,
                    'ItemID' => $item->ItemID,
                    'QuantityAssigned' => $item->QuantityIssued,
                    'DateAssigned' => $this->IssuanceDate,
                    'Status' => 'Assigned',
                    'Remarks' => 'Issued via Issuance #' . $this->IssuanceNumber,
                ]);
            }
            
            $inventoryItem->save();
        }
    }

    /**
     * Reverse issuance (for deletion)
     */
    public function reverseIssuance()
    {
        foreach ($this->items as $item) {
            $inventoryItem = $item->inventoryItem;
            
            if ($item->ItemType == 'Material') {
                // Restore material stock
                $inventoryItem->TotalQuantity += $item->QuantityIssued;
                $inventoryItem->CommittedQuantity += $item->QuantityIssued;
            } else {
                // Restore equipment reservation
                $inventoryItem->CommittedQuantity += $item->QuantityIssued;
                
                // Check if any equipment of this type is still in use
                $stillInUse = \App\Models\ProjectMilestoneEquipment::where('ItemID', $item->ItemID)
                    ->whereNull('DateReturned')
                    ->where('Remarks', '!=', 'Issued via Issuance #' . $this->IssuanceNumber)
                    ->exists();
                    
                if (!$stillInUse) {
                    $inventoryItem->Status = 'Active';
                }
                
                // Remove equipment assignment record
                \App\Models\ProjectMilestoneEquipment::where('Remarks', 'Issued via Issuance #' . $this->IssuanceNumber)
                    ->where('ItemID', $item->ItemID)
                    ->delete();
            }
            
            $inventoryItem->save();
        }
    }
}

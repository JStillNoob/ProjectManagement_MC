<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'POID';

    protected $fillable = [
        'SupplierID',
        'RequestID',
        'OrderDate',
        'Status',
        'CreatedBy',
        'ApprovedBy',
        'ApprovedAt',
        'DateSent',
        'PDFPath',
    ];

    protected $casts = [
        'OrderDate' => 'date',
        'DateSent' => 'date',
        'ApprovedAt' => 'datetime',
    ];

    /**
     * Get the supplier for this PO
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    /**
     * Get the inventory request that triggered this PO
     */
    public function inventoryRequest()
    {
        return $this->belongsTo(InventoryRequest::class, 'RequestID', 'RequestID');
    }

    /**
     * Get the employee who created this PO
     */
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'CreatedBy', 'id');
    }

    /**
     * Get the employee who approved this PO
     */
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'ApprovedBy', 'id');
    }

    /**
     * Get all items in this PO
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'POID', 'POID');
    }

    /**
     * Get all receiving records for this PO
     */
    public function receivingRecords()
    {
        return $this->hasMany(ReceivingRecord::class, 'POID', 'POID');
    }

    /**
     * Calculate total amount from items
     */
    public function calculateTotalAmount()
    {
        return $this->items()->sum('TotalPrice');
    }

    /**
     * Check if PO is fully received
     */
    public function isFullyReceived()
    {
        $allItems = $this->items;
        
        foreach ($allItems as $item) {
            if ($item->QuantityReceived < $item->QuantityOrdered) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Mark PO as sent
     */
    public function markAsSent()
    {
        $this->update([
            'Status' => 'Sent',
            'DateSent' => now(),
        ]);
    }

    /**
     * Approve the PO
     */
    public function approve($approverEmployeeId)
    {
        $this->update([
            'ApprovedBy' => $approverEmployeeId,
            'ApprovedAt' => now(),
        ]);
    }

    /**
     * Check if PO is editable
     */
    public function isEditable()
    {
        return in_array($this->Status, ['Draft']);
    }

    /**
     * Scope to get pending POs
     */
    public function scopePending($query)
    {
        return $query->whereIn('Status', ['Draft', 'Sent', 'Partially Received']);
    }

    /**
     * Scope to get completed POs
     */
    public function scopeCompleted($query)
    {
        return $query->where('Status', 'Completed');
    }
}

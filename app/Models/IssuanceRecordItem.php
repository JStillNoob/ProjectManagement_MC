<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuanceRecordItem extends Model
{
    use HasFactory;

    protected $table = 'issuance_record_items';
    protected $primaryKey = 'IssuanceItemID';

    protected $fillable = [
        'IssuanceID',
        'ItemID',
        'QuantityIssued',
        'QuantityReturned',
        'Unit',
        'ItemType',
        'BarcodeNumber',
        'ItemRemarks',
    ];

    protected $casts = [
        'QuantityIssued' => 'decimal:2',
        'QuantityReturned' => 'decimal:2',
    ];

    public function issuanceRecord()
    {
        return $this->belongsTo(IssuanceRecord::class, 'IssuanceID', 'IssuanceID');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'ItemID', 'ItemID');
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->QuantityIssued - $this->QuantityReturned;
    }

    public function isFullyReturned()
    {
        return $this->QuantityReturned >= $this->QuantityIssued;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingRecordItem extends Model
{
    use HasFactory;

    protected $table = 'receiving_record_items';
    protected $primaryKey = 'ReceivingItemID';

    protected $fillable = [
        'ReceivingID',
        'POItemID',
        'QuantityReceived',
        'Condition',
        'QuantityDamaged',
        'ItemRemarks',
    ];

    protected $casts = [
        'QuantityReceived' => 'integer',
        'QuantityDamaged' => 'integer',
    ];

    /**
     * Get the receiving record
     */
    public function receivingRecord()
    {
        return $this->belongsTo(ReceivingRecord::class, 'ReceivingID', 'ReceivingID');
    }

    /**
     * Get the purchase order item
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'POItemID', 'POItemID');
    }

    /**
     * Get good quantity (total received minus damaged)
     */
    public function getGoodQuantityAttribute()
    {
        return $this->QuantityReceived - $this->QuantityDamaged;
    }
}

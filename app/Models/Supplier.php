<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';
    protected $primaryKey = 'SupplierID';

    protected $fillable = [
        'SupplierName',
        'ContactFirstName',
        'ContactLastName',
        'PhoneNumber',
        'Email',
        'Street',
        'City',
        'Province',
        'PostalCode',
        'Status',
    ];

    /**
     * Get the full contact name
     */
    public function getContactFullNameAttribute()
    {
        return trim($this->ContactFirstName . ' ' . $this->ContactLastName);
    }

    /**
     * Get the full address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->Street,
            $this->City,
            $this->Province,
            $this->PostalCode
        ]);
        return implode(', ', $parts);
    }

    /**
     * Get all purchase orders for this supplier
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'SupplierID', 'SupplierID');
    }

    /**
     * Scope to get only active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('Status', 'Active');
    }

}

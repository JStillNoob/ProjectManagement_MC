<?php

use App\Models\InventoryRequestItem;

$items = InventoryRequestItem::with('item.resourceCatalog')->take(5)->get();

echo "Checking Request Items:\n\n";

foreach($items as $item) {
    echo "RequestItemID: {$item->RequestItemID}\n";
    echo "InventoryItemID: {$item->InventoryItemID}\n";
    echo "Item exists: " . ($item->item ? 'YES' : 'NO') . "\n";
    
    if ($item->item) {
        echo "Item has ResourceCatalogID: {$item->item->ResourceCatalogID}\n";
        if ($item->item->resourceCatalog) {
            echo "Resource Name: {$item->item->resourceCatalog->ResourceName}\n";
        } else {
            echo "ResourceCatalog: NULL\n";
        }
    }
    echo "---\n";
}

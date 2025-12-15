<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\InventoryItem;

class ReportService
{
    /**
     * Generate Purchase Order Report
     */
    public function generatePurchaseOrderReport($purchaseOrderId, $format = 'pdf')
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items.item.type',
            'creator',
            'approver'
        ])->findOrFail($purchaseOrderId);

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'items' => $purchaseOrder->items,
            'totalQuantity' => $purchaseOrder->items->sum('QuantityOrdered'),
        ];

        if ($format === 'pdf') {
            return $this->generatePDF('reports.purchase-order-pdf', $data, "PO_" . str_pad($purchaseOrderId, 4, '0', STR_PAD_LEFT));
        } elseif ($format === 'excel') {
            return $this->generatePurchaseOrderExcel($purchaseOrder);
        } else {
            throw new \Exception('Unsupported format');
        }
    }

    /**
     * Generate Inventory Report
     */
    public function generateInventoryReport($format = 'pdf')
    {
        $items = InventoryItem::with('type')
            ->whereNull('deleted_at')
            ->orderBy('ItemName')
            ->get();

        $lowStockItems = $items->filter(function($item) {
            return $item->QuantityInStock <= $item->MinimumStockLevel;
        });

        $data = [
            'items' => $items,
            'totalItems' => $items->count(),
            'totalValue' => $items->sum('QuantityInStock'),
            'lowStockCount' => $lowStockItems->count(),
            'lowStockItems' => $lowStockItems,
        ];

        if ($format === 'pdf') {
            return $this->generatePDF('reports.inventory-pdf', $data, 'Inventory_Report');
        } elseif ($format === 'excel') {
            return $this->generateInventoryExcel($items);
        } else {
            throw new \Exception('Unsupported format');
        }
    }

    /**
     * Generate Project Summary Report
     */
    public function generateProjectReport($projectId = null, $format = 'pdf')
    {
        $query = DB::table('projects as p')
            ->leftJoin('project_statuses as ps', 'p.StatusID', '=', 'ps.StatusID')
            ->leftJoin('clients as c', 'p.ClientID', '=', 'c.ClientID')
            ->select([
                'p.*',
                'ps.StatusName',
                'c.ClientName',
                'c.ContactNumber as ClientContact'
            ])
            ->whereNull('p.deleted_at');

        if ($projectId) {
            $query->where('p.ProjectID', $projectId);
        }

        $projects = $query->get();

        $data = [
            'projects' => $projects,
            'totalProjects' => $projects->count(),
            'activeProjects' => $projects->where('StatusName', 'Active')->count(),
        ];

        return $this->generatePDF('reports.project-pdf', $data, 'Project_Report');
    }

    /**
     * Generate PDF Helper
     */
    private function generatePDF($view, $data, $filename)
    {
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        $path = storage_path('app/public/reports/' . $filename . '_' . time() . '.pdf');
        
        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);
        return $path;
    }

    /**
     * Generate Purchase Order Excel
     */
    private function generatePurchaseOrderExcel($purchaseOrder)
    {
        $filename = 'PO_' . str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT) . '_' . time() . '.xlsx';
        $path = storage_path('app/public/reports/' . $filename);

        \Excel::create($filename, function($excel) use ($purchaseOrder) {
            $excel->sheet('Purchase Order', function($sheet) use ($purchaseOrder) {
                // Header
                $sheet->mergeCells('A1:E1');
                $sheet->row(1, ['PURCHASE ORDER']);
                $sheet->row(1, function($row) {
                    $row->setFontSize(18);
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });

                // PO Details
                $sheet->row(3, ['PO Number:', str_pad($purchaseOrder->POID, 4, '0', STR_PAD_LEFT)]);
                $sheet->row(4, ['Order Date:', $purchaseOrder->OrderDate]);
                $sheet->row(5, ['Supplier:', $purchaseOrder->supplier->SupplierName ?? 'N/A']);
                $sheet->row(6, ['Status:', $purchaseOrder->Status]);

                // Headers
                $sheet->row(8, ['#', 'Item Name', 'Item Type', 'Quantity', 'Unit']);
                $sheet->row(8, function($row) {
                    $row->setBackground('#87A96B');
                    $row->setFontColor('#FFFFFF');
                    $row->setFontWeight('bold');
                });

                // Items
                $row = 9;
                foreach ($purchaseOrder->items as $index => $item) {
                    $sheet->row($row++, [
                        $index + 1,
                        $item->item->ItemName ?? 'N/A',
                        $item->item->type->ItemTypeName ?? 'N/A',
                        $item->QuantityOrdered,
                        $item->Unit
                    ]);
                }

                // Total
                $sheet->row($row + 1, ['', '', 'Total Quantity:', $purchaseOrder->items->sum('QuantityOrdered'), '']);
                $sheet->row($row + 1, function($r) {
                    $r->setFontWeight('bold');
                });
            });
        })->store('xlsx', storage_path('app/public/reports'));

        return $path;
    }

    /**
     * Generate Inventory Excel
     */
    private function generateInventoryExcel($items)
    {
        $filename = 'Inventory_Report_' . time() . '.xlsx';
        $path = storage_path('app/public/reports/' . $filename);

        \Excel::create($filename, function($excel) use ($items) {
            $excel->sheet('Inventory', function($sheet) use ($items) {
                // Header
                $sheet->mergeCells('A1:F1');
                $sheet->row(1, ['INVENTORY REPORT']);
                $sheet->row(1, function($row) {
                    $row->setFontSize(16);
                    $row->setFontWeight('bold');
                    $row->setAlignment('center');
                });

                // Column Headers
                $sheet->row(3, ['Item ID', 'Item Name', 'Type', 'In Stock', 'Unit', 'Min Level']);
                $sheet->row(3, function($row) {
                    $row->setBackground('#87A96B');
                    $row->setFontColor('#FFFFFF');
                    $row->setFontWeight('bold');
                });

                // Data
                $row = 4;
                foreach ($items as $item) {
                    $sheet->row($row++, [
                        $item->ItemID,
                        $item->ItemName,
                        $item->type->ItemTypeName ?? 'N/A',
                        $item->QuantityInStock,
                        $item->Unit,
                        $item->MinimumStockLevel
                    ]);
                }
            });
        })->store('xlsx', storage_path('app/public/reports'));

        return $path;
    }

    /**
     * Get list of available reports
     */
    public function getAvailableReports()
    {
        return [
            'purchase_order' => 'Purchase Order Report',
            'inventory' => 'Inventory Report',
            'project_summary' => 'Project Summary Report',
        ];
    }
}

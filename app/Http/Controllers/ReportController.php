<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display report generation page
     */
    public function index()
    {
        $reports = $this->reportService->getAvailableReports();
        return view('reports.index', compact('reports'));
    }

    /**
     * Generate Purchase Order Report
     */
    public function generatePurchaseOrder(Request $request, $id)
    {
        $request->validate([
            'format' => 'in:pdf,excel',
        ]);

        $format = $request->input('format', 'pdf');

        try {
            $filePath = $this->reportService->generatePurchaseOrderReport($id, $format);

            return Response::download($filePath, 'Purchase_Order_' . $id . '.' . $format, [
                'Content-Type' => $this->getContentType($format),
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Report generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate Inventory Report
     */
    public function generateInventory(Request $request)
    {
        $format = $request->input('format', 'pdf');

        try {
            $filePath = $this->reportService->generateInventoryReport($format);

            return Response::download($filePath, 'Inventory_Report.' . $format, [
                'Content-Type' => $this->getContentType($format),
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Report generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate Project Report
     */
    public function generateProject(Request $request, $id = null)
    {
        $format = $request->input('format', 'pdf');

        try {
            $filePath = $this->reportService->generateProjectReport($id, $format);
            $extension = $format === 'excel' ? '.xlsx' : '.pdf';

            return Response::download($filePath, 'Project_Report' . $extension, [
                'Content-Type' => $this->getContentType($format),
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Report generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get content type for format
     */
    private function getContentType($format)
    {
        return match($format) {
            'pdf' => 'application/pdf',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectEmployee;

class RegenerateProjectEmployeeQRCodes extends Command
{
    protected $signature = 'project-employees:regenerate-qr-codes';
    protected $description = 'Regenerates QR codes for all project employee assignments to ensure uniqueness.';

    public function handle()
    {
        $this->info('Starting QR code regeneration for project employees...');

        $projectEmployees = ProjectEmployee::with('employee', 'project')->get();
        $regenerated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($projectEmployees as $pe) {
            try {
                // Always regenerate to ensure uniqueness
                $oldQrCode = $pe->qr_code;
                $pe->generateQrCode();
                
                if ($oldQrCode !== $pe->qr_code) {
                    $regenerated++;
                    $employeeName = $pe->employee ? $pe->employee->full_name : 'N/A';
                    $this->info("Regenerated QR for Project {$pe->ProjectID}, Employee {$pe->EmployeeID} ({$employeeName}): {$pe->qr_code}");
                } else {
                    $skipped++;
                    $this->line("Skipped (already has QR): Project {$pe->ProjectID}, Employee {$pe->EmployeeID}");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error regenerating QR for Project {$pe->ProjectID}, Employee {$pe->EmployeeID}: " . $e->getMessage());
            }
        }

        $this->info("QR code regeneration complete!");
        $this->info("  Regenerated: {$regenerated}");
        $this->info("  Skipped: {$skipped}");
        $this->info("  Errors: {$errors}");
        $this->info("  Total processed: " . $projectEmployees->count());
    }
}


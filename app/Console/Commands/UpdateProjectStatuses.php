<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;

class UpdateProjectStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all project statuses based on their start and end dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating project statuses...');
        
        $updated = 0;
        $projects = Project::all();
        
        foreach ($projects as $project) {
            $newStatusId = Project::calculateStatus(
                $project->StartDate,
                $project->EndDate,
                $project->WarrantyDays,
                $project->NTPStartDate
            );
            
            if ($project->StatusID != $newStatusId) {
                // Skip if current status is On Hold or Cancelled
                $currentStatus = $project->status->StatusName ?? null;
                if (in_array($currentStatus, ['On Hold', 'Cancelled'])) {
                    continue;
                }
                
                // Update the status directly to avoid triggering the updating event
                $project->StatusID = $newStatusId;
                $project->saveQuietly(); // Use saveQuietly to skip model events
                $updated++;
            }
        }
        
        $this->info("Updated {$updated} project(s).");
        return 0;
    }
}

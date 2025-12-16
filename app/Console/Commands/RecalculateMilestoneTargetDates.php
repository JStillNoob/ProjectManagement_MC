<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use Carbon\Carbon;

class RecalculateMilestoneTargetDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'milestones:recalculate-target-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate target dates for all milestones based on project StartDate and cumulative EstimatedDays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation of milestone target dates...');
        
        $projects = Project::whereNotNull('StartDate')
            ->with('milestones')
            ->get();
        
        $totalProjects = $projects->count();
        $totalMilestones = 0;
        $updatedMilestones = 0;
        
        foreach ($projects as $project) {
            $milestones = $project->milestones()
                ->orderBy('order')
                ->orderBy('milestone_id')
                ->get();
            
            if ($milestones->isEmpty()) {
                continue;
            }
            
            $cumulativeDays = 0;
            
            foreach ($milestones as $milestone) {
                $totalMilestones++;
                
                if ($milestone->EstimatedDays) {
                    $cumulativeDays += $milestone->EstimatedDays;
                    $newTargetDate = Carbon::parse($project->StartDate)->addDays($cumulativeDays);
                    
                    $oldTargetDate = $milestone->target_date;
                    $milestone->target_date = $newTargetDate;
                    $milestone->saveQuietly();
                    
                    if ($oldTargetDate != $newTargetDate) {
                        $updatedMilestones++;
                        $this->line("  Updated: {$milestone->milestone_name} - Target Date: {$newTargetDate->format('M d, Y')}");
                    }
                }
            }
        }
        
        $this->info("Recalculation complete!");
        $this->info("  Projects processed: {$totalProjects}");
        $this->info("  Total milestones: {$totalMilestones}");
        $this->info("  Updated milestones: {$updatedMilestones}");
        
        return Command::SUCCESS;
    }
}

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
    protected $description = 'Recalculate target dates for milestones - only for In Progress milestones, clear for Pending ones';

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
            
            $lastCompletedDate = null;
            
            foreach ($milestones as $milestone) {
                $totalMilestones++;
                $oldTargetDate = $milestone->target_date;
                
                if ($milestone->status === 'Completed') {
                    // Keep existing target_date for completed milestones
                    // Update lastCompletedDate for next milestone calculation
                    $lastCompletedDate = $milestone->actual_date ?? $milestone->target_date;
                    continue;
                }
                
                if ($milestone->status === 'In Progress') {
                    // Calculate target date for in-progress milestone
                    if ($milestone->EstimatedDays) {
                        $startFrom = $lastCompletedDate ?? $project->StartDate;
                        $newTargetDate = Carbon::parse($startFrom)->addDays($milestone->EstimatedDays);
                        $milestone->target_date = $newTargetDate;
                        $milestone->saveQuietly();
                        
                        if ($oldTargetDate != $newTargetDate) {
                            $updatedMilestones++;
                            $this->line("  Updated (In Progress): {$milestone->milestone_name} - Target Date: {$newTargetDate->format('M d, Y')}");
                        }
                    }
                } elseif ($milestone->status === 'Pending') {
                    // Clear target date for pending milestones - they'll get it when they start
                    if ($milestone->target_date !== null) {
                        $milestone->target_date = null;
                        $milestone->saveQuietly();
                        $updatedMilestones++;
                        $this->line("  Cleared (Pending): {$milestone->milestone_name} - Target Date set to N/A (will be calculated when started)");
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

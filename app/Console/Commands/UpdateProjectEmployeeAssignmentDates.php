<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\ProjectEmployee;

class UpdateProjectEmployeeAssignmentDates extends Command
{
    protected $signature = 'project-employees:update-assignment-dates {project? : The project name or ID}';
    protected $description = 'Updates assigned_date for project employees to match the project start date.';

    public function handle()
    {
        $projectName = $this->argument('project');
        
        if ($projectName) {
            // Find specific project
            $project = Project::where('ProjectName', $projectName)
                ->orWhere('ProjectID', $projectName)
                ->first();
            
            if (!$project) {
                $this->error("Project '{$projectName}' not found.");
                return 1;
            }
            
            $projects = collect([$project]);
        } else {
            // Update all projects
            $projects = Project::whereNotNull('StartDate')->get();
        }

        $totalUpdatedAssignments = 0;
        $totalUpdatedEmployees = 0;

        foreach ($projects as $project) {
            $this->info("Processing project: {$project->ProjectName} (ID: {$project->ProjectID})");
            
            if (!$project->StartDate) {
                $this->warn("  Project has no StartDate. Using yesterday as assignment date.");
                $assignmentDate = now()->subDay()->toDateString();
            } else {
                $assignmentDate = $project->StartDate->toDateString();
            }
            
            $this->info("  Using assignment date: {$assignmentDate}");
            
            $assignments = ProjectEmployee::where('ProjectID', $project->ProjectID)->with('employee')->get();
            $updatedAssignments = 0;
            $updatedEmployees = 0;
            
            foreach ($assignments as $assignment) {
                // Update assigned_date in project_employees
                if ($assignment->assigned_date != $assignmentDate) {
                    $oldDate = $assignment->assigned_date ? $assignment->assigned_date->toDateString() : 'N/A';
                    $assignment->assigned_date = $assignmentDate;
                    $assignment->save();
                    $updatedAssignments++;
                    $this->info("    Updated assigned_date for Employee ID {$assignment->EmployeeID}: {$oldDate} → {$assignmentDate}");
                }
                
                // Update employee start_date to match project start date
                if ($assignment->employee && $assignment->employee->start_date != $assignmentDate) {
                    $oldStartDate = $assignment->employee->start_date ? $assignment->employee->start_date->toDateString() : 'N/A';
                    $assignment->employee->start_date = $assignmentDate;
                    $assignment->employee->save();
                    $updatedEmployees++;
                    $this->info("    Updated start_date for Employee {$assignment->employee->full_name}: {$oldStartDate} → {$assignmentDate}");
                }
            }
            
            $totalUpdatedAssignments += $updatedAssignments;
            $totalUpdatedEmployees += $updatedEmployees;
            $this->info("  Updated {$updatedAssignments} assignment(s) and {$updatedEmployees} employee start_date(s) for this project.");
        }

        $this->info("\nUpdate complete!");
        $this->info("  Total assignments updated: {$totalUpdatedAssignments}");
        $this->info("  Total employee start_dates updated: {$totalUpdatedEmployees}");
        return 0;
    }
}


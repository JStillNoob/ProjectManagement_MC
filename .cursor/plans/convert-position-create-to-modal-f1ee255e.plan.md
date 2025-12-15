<!-- f1ee255e-2046-45b3-a714-83f04d98483e 0f796d40-5ba2-43a1-a57c-3183ddc0bb90 -->
# Project Management View Updates

## Overview

Update the project details view (`resources/views/ProdHeadPage/project-show.blade.php`) to:

1. Remove the "Edit Project" button to make project details non-editable
2. Change "End Date" label to "Target End Date" to indicate it's a target, not actual
3. Add an "End Project" button that marks the project as completed
4. Remove the warranty end date display (keep only the number of days)

## Implementation Steps

### 1. Update Project Show View

**File:** `resources/views/ProdHeadPage/project-show.blade.php`

- Remove the "Edit Project" button from the card header (lines 20-23)
- Change "End Date:" label to "Target End Date:" (line 76)
- Remove the warranty end date display from the warranty row (lines 84-86), keeping only the number of days
- Add an "End Project" button in the "Project Actions" section that:
- Only shows if project status is not "Completed"
- Triggers a confirmation dialog
- Submits a POST request to end the project

### 2. Add End Project Route

**File:** `routes/web.php`

- Add a new route: `POST projects/{project}/end` that calls `ProjectController@endProject`

### 3. Add End Project Controller Method

**File:** `app/Http/Controllers/ProjectController.php`

- Add `endProject(Project $project)` method that:
- Checks if project is already completed
- Sets project status to "Completed"
- Redirects back with success message

## Files to Modify

- `resources/views/ProdHeadPage/project-show.blade.php` - Remove edit button, update labels, add end project button, remove warranty end date
- `routes/web.php` - Add end project route
- `app/Http/Controllers/ProjectController.php` - Add endProject method

### To-dos

- [ ] Update project-show.blade.php: remove Edit button, change 'End Date' to 'Target End Date', remove warranty end date display, add 'End Project' button
- [ ] Add POST route for ending projects in web.php
- [ ] Add endProject() method to ProjectController to mark project as completed
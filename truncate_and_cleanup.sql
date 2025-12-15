-- Truncate specified tables and remove PONumber column
-- Run this in your MySQL database (phpMyAdmin or MySQL command line)

SET FOREIGN_KEY_CHECKS=0;

-- Truncate tables in the correct order
TRUNCATE TABLE `inventory_request_items`;
TRUNCATE TABLE `inventory_requests`;
TRUNCATE TABLE `milestone_required_items`;
TRUNCATE TABLE `project_milestone_materials`;
TRUNCATE TABLE `project_milestone_equipment`;
TRUNCATE TABLE `milestone_proof_images`;
TRUNCATE TABLE `project_milestones`;
TRUNCATE TABLE `project_employees`;
TRUNCATE TABLE `projects`;

-- Remove PONumber column from purchase_orders
ALTER TABLE `purchase_orders` DROP COLUMN `PONumber`;

SET FOREIGN_KEY_CHECKS=1;

-- Done! All specified tables have been truncated and PONumber column removed.

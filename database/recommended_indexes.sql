-- SchoGMS recommended indexes for MySQL (schogms database)
-- Run once in phpMyAdmin or: mysql -u root schogms < database/recommended_indexes.sql
-- Skip any statement that errors with "Duplicate key name".

USE schogms;

-- users table
ALTER TABLE users ADD INDEX idx_users_email (email);
ALTER TABLE users ADD INDEX idx_users_role (role);
ALTER TABLE users ADD INDEX idx_users_status (status);
ALTER TABLE users ADD INDEX idx_users_campus (campus);
ALTER TABLE users ADD INDEX idx_users_created_at (created_at);

-- ched_masterlist (TDP)
ALTER TABLE ched_masterlist ADD INDEX idx_ched_sheet_name (sheet_name);
ALTER TABLE ched_masterlist ADD INDEX idx_ched_academic_year (academic_year);
ALTER TABLE ched_masterlist ADD INDEX idx_ched_semester (semester);
ALTER TABLE ched_masterlist ADD INDEX idx_ched_file_group (file_group);
ALTER TABLE ched_masterlist ADD INDEX idx_ched_course (course_program_enrolled(100));

-- ched_masterlist_tes (TES)
ALTER TABLE ched_masterlist_tes ADD INDEX idx_tes_campus (campus);
ALTER TABLE ched_masterlist_tes ADD INDEX idx_tes_file_group (file_group);

-- assigned_program_chairs
ALTER TABLE assigned_program_chairs ADD INDEX idx_apc_email (email);
ALTER TABLE assigned_program_chairs ADD INDEX idx_apc_status (status);
ALTER TABLE assigned_program_chairs ADD INDEX idx_apc_campus (campus);

-- assigned_dean
ALTER TABLE assigned_dean ADD INDEX idx_dean_email (email);
ALTER TABLE assigned_dean ADD INDEX idx_dean_status (status);

-- verification_attempts (if table exists)
-- ALTER TABLE verification_attempts ADD INDEX idx_va_email (email);
-- ALTER TABLE verification_attempts ADD INDEX idx_va_attempt_time (attempt_time);

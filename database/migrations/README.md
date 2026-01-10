# Database Migrations

This folder contains database migration scripts for schema changes.

## Naming Convention
Files should follow the pattern: `YYYY_MM_DD_description.php`

Example: `2026_01_10_add_room_columns.php`

## Usage
Run migrations manually via command line:
```bash
php database/migrations/2026_01_10_add_room_columns.php
```

## Current Migrations
- `2026_01_10_add_room_columns.php` - Adds room assignment columns (presentasi, tes_tulis) to user table
- `2026_01_10_add_interview_column.php` - Adds interview room column to user table

# Job Status Management System

## Overview
The jobs system includes a complete status management feature that allows you to activate and deactivate jobs from the dashboard. Inactive jobs are automatically hidden from the public shortcode listing.

## How It Works

### 1. Dashboard Status Management
- **Location**: WordPress Admin → Jobs → Overview
- **Feature**: Click on any job's status badge (Active/Inactive) to toggle the status
- **Visual Feedback**: Status badges change color and show loading state during updates
- **AJAX Updates**: Status changes are saved immediately without page refresh

### 2. Status Filtering in Shortcode
The `[jobs_listing]` shortcode automatically filters out inactive jobs:

```php
// Only shows jobs where status is:
// - 'active' 
// - Not set (defaults to active)
// - Empty (defaults to active)
```

### 3. Status Values
- **Active**: Job is visible in shortcode listings
- **Inactive**: Job is hidden from shortcode listings but remains in admin dashboard

## Usage Instructions

### To Deactivate a Job:
1. Go to **WordPress Admin → Jobs → Overview**
2. Find the job you want to deactivate
3. Click on the **"Active"** status badge
4. The status will change to **"Inactive"** 
5. The job will immediately disappear from public shortcode listings

### To Reactivate a Job:
1. Go to **WordPress Admin → Jobs → Overview**
2. Find the inactive job (status shows "Inactive")
3. Click on the **"Inactive"** status badge
4. The status will change to **"Active"**
5. The job will reappear in public shortcode listings

## Technical Details

### Database Storage
- Job status is stored as post meta: `_job_status`
- Values: `'active'` or `'inactive'`
- Default: Jobs without status are treated as active

### AJAX Handler
- **Action**: `update_job_status`
- **Security**: Nonce verification and capability checks
- **Response**: JSON success/error messages

### Status Badge Styling
- **Active**: Green background (`#d4edda`) with dark green text (`#155724`)
- **Inactive**: Red background (`#f8d7da`) with dark red text (`#721c24`)

## Testing the Feature

### Test Steps:
1. Create a test job
2. Verify it appears in the shortcode listing
3. Go to Jobs → Overview
4. Click the job's "Active" status badge
5. Verify status changes to "Inactive"
6. Check that the job no longer appears in shortcode listing
7. Click "Inactive" status badge to reactivate
8. Verify job reappears in shortcode listing

### Debug Information
The system includes debug logging to help troubleshoot issues:
- Check WordPress error logs for status update information
- Debug information is displayed in shortcode output when enabled

## Shortcode Usage

The shortcode automatically respects job status:

```php
// Basic usage - only shows active jobs
[jobs_listing]

// With filters - still only shows active jobs
[jobs_listing category="technology" location="Remote"]

// With limit - still only shows active jobs  
[jobs_listing limit="5"]
```

## Troubleshooting

### Job Not Appearing After Activation
1. Check that the job status is actually "Active" in the overview
2. Clear any caching plugins
3. Check WordPress error logs for any database issues

### Status Toggle Not Working
1. Ensure you have proper admin permissions
2. Check browser console for JavaScript errors
3. Verify AJAX is working (check Network tab in browser dev tools)

### Jobs Missing from Listing
1. Verify jobs are published (not draft)
2. Check that job status is "Active" or not set
3. Ensure shortcode is using correct post type ('jobs')

## Default Behavior
- New jobs are automatically set to "Active" status
- Existing jobs without status are treated as "Active"
- Only published jobs appear in listings (regardless of status)
- Status changes are immediate (no caching delays)

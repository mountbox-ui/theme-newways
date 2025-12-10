# Active Job Toggle Fix

## Problem
The force fix button was working to set jobs to inactive, but when trying to toggle jobs back to active, they weren't showing up in the shortcode again.

## Root Cause
The issue was in the manual filtering logic. The original code was using `while ($jobs_query->have_posts())` which was interfering with the WordPress query loop and potentially causing issues with the post data.

## Fix Applied

### 1. **Improved Manual Filtering Logic**
**Changed from:**
```php
// Old - using WordPress loop which could interfere
if ($jobs_query->have_posts()) {
    while ($jobs_query->have_posts()) {
        $jobs_query->the_post();
        $job_status = get_post_meta(get_the_ID(), '_job_status', true);
        // ... filtering logic
    }
    wp_reset_postdata();
}
```

**Changed to:**
```php
// New - direct array access without WordPress loop
if ($jobs_query->posts) {
    foreach ($jobs_query->posts as $post) {
        $job_status = get_post_meta($post->ID, '_job_status', true);
        // ... filtering logic
    }
}
```

### 2. **Added Force Activate All Jobs Button**
Added a new button in the Status Test page that allows you to force-activate all jobs at once:
- **Location**: WordPress Admin → Jobs → Status Test
- **Function**: Sets all jobs to 'active' status
- **Use case**: When jobs aren't showing up after being set to active

### 3. **Enhanced Debug Capabilities**
Added comprehensive debugging tools:
- **Debug Shortcode Output**: Tests what the shortcode would display
- **Enhanced logging**: Shows exactly which jobs are being filtered
- **Manual debug trigger**: Button to run debug tests on demand

## How to Use the Fix

### For Individual Job Issues:
1. Go to **WordPress Admin → Jobs → Status Test**
2. Find the job that's not showing up
3. Click "Toggle to Active" to set it to active
4. If it still doesn't show, use the "Force Activate All Jobs" button

### For Multiple Job Issues:
1. Go to **WordPress Admin → Jobs → Status Test**
2. Click the green "Force Activate All Jobs" button
3. This will set all jobs to active status
4. Check your frontend shortcode

### For Debugging:
1. Go to **WordPress Admin → Jobs → Status Test**
2. Click the blue "Debug Shortcode Output" button
3. Check your WordPress error logs for detailed information
4. Look for entries like:
   ```
   Shortcode would show: ID 123, Title: Job Title, Status: active
   ```

## Files Modified

1. **`theme-functions.php`**:
   - Fixed manual filtering logic in shortcode function
   - Added force activate all jobs functionality
   - Added debug shortcode output function
   - Enhanced error logging

2. **`page-jobs.php`**:
   - Fixed manual filtering logic to match shortcode
   - Improved post handling without WordPress loop interference

## Expected Results

✅ **Jobs can be toggled to active** and will appear in shortcode
✅ **Force activate all jobs** works for bulk activation
✅ **Debug tools** help identify any remaining issues
✅ **No WordPress loop interference** with post data
✅ **Consistent behavior** between shortcode and page template

## Troubleshooting Steps

### If jobs still don't show after being set to active:

1. **Use Force Activate All Jobs**:
   - Go to Jobs → Status Test
   - Click "Force Activate All Jobs" button
   - Check frontend

2. **Check Debug Output**:
   - Click "Debug Shortcode Output" button
   - Check WordPress error logs
   - Look for "Shortcode would show" entries

3. **Verify Job Status**:
   - Check that jobs show "✓ Visible" in Status Test page
   - Verify status badges show "ACTIVE" (green)

4. **Clear Caching**:
   - Clear any caching plugins
   - Hard refresh your browser

### If the issue persists:
- Check WordPress error logs for detailed debug information
- Verify that the jobs are actually published (not draft)
- Ensure the shortcode is on the correct page/post

## Technical Details

The fix addresses the core issue where WordPress's `have_posts()` loop was interfering with the manual filtering process. By switching to direct array access (`$jobs_query->posts`), we avoid any potential conflicts with WordPress's internal query state management.

This ensures that:
- Active jobs are properly displayed
- Status toggles work in both directions
- Manual filtering is reliable
- Debug information is accurate

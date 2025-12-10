# 404 Error Fix for Job Pages

## Problem
Clicking "View" button in the dashboard shows 404 error when trying to access individual job pages.

## Root Cause
The 404 error is likely caused by:
1. **Permalink Structure**: WordPress rewrite rules not properly configured
2. **Template Recognition**: WordPress not finding the single-jobs.php template
3. **Post Type Registration**: Jobs post type not properly registered

## Fixes Applied

### 1. **Enhanced Post Type Registration**
**Added to `theme-functions.php`:**
- Improved post type arguments
- Added `hierarchical => false`
- Added `exclude_from_search => false`
- Enhanced rewrite rules

### 2. **Automatic Rewrite Rules Flushing**
**Added automatic flushing:**
- Flushes rewrite rules when jobs post type is registered
- Sets database flags to track flush status
- Prevents multiple unnecessary flushes

### 3. **Template Validation**
**Enhanced `single-jobs.php`:**
- Added check for `is_singular('jobs')`
- Better error handling for non-job posts
- Improved template recognition

### 4. **Admin Notifications**
**Added helpful notices:**
- Shows warning if rewrite rules need flushing
- Provides direct link to Permalinks settings
- Guides users through the fix process

## How to Fix the 404 Error

### **Method 1: Automatic Fix (Recommended)**
The system will automatically flush rewrite rules. If you see an admin notice:

1. **Go to**: WordPress Admin → Settings → Permalinks
2. **Click**: "Save Changes" button (don't change anything)
3. **Test**: Try clicking "View" on a job again

### **Method 2: Manual Fix**
If the automatic fix doesn't work:

1. **Go to**: WordPress Admin → Settings → Permalinks
2. **Select**: Any permalink structure (not "Plain")
3. **Click**: "Save Changes"
4. **Test**: Try the "View" button again

### **Method 3: Force Flush**
If still having issues:

1. **Go to**: WordPress Admin → Jobs → Overview
2. **Look for**: Any admin notices about rewrite rules
3. **Follow**: The instructions in the notice

## Expected Results

✅ **Job Pages Work**: Individual job pages load properly
✅ **No 404 Errors**: "View" button opens job pages correctly
✅ **Proper URLs**: Job URLs follow the pattern `/jobs/job-title/`
✅ **Template Loading**: single-jobs.php template loads correctly

## Troubleshooting

### **If 404 Error Persists:**

1. **Check Template File**:
   - Ensure `single-jobs.php` is in your theme folder
   - Verify file permissions are correct

2. **Check Permalinks**:
   - Go to Settings → Permalinks
   - Make sure it's not set to "Plain"
   - Click "Save Changes"

3. **Check Post Status**:
   - Ensure jobs are published (not draft)
   - Check that job status is "active"

4. **Clear Cache**:
   - Clear any caching plugins
   - Clear browser cache
   - Hard refresh the page

### **URL Structure Should Be:**
- **Job Archive**: `yoursite.com/jobs/`
- **Individual Job**: `yoursite.com/jobs/job-title/`

### **If URLs Are Wrong:**
- Go to Settings → Permalinks
- Click "Save Changes" without changing anything
- This refreshes the URL structure

## Technical Details

### **Post Type Registration:**
```php
$args = array(
    'public' => true,
    'publicly_queryable' => true,
    'rewrite' => array('slug' => 'jobs', 'with_front' => false),
    'hierarchical' => false,
    'exclude_from_search' => false,
);
```

### **Template Check:**
```php
if (!is_singular('jobs')) {
    // Show 404 for non-job posts
}
```

### **Automatic Flush:**
```php
function auto_flush_jobs_rewrite_rules() {
    if (get_option('jobs_rewrite_rules_flushed') !== 'yes') {
        flush_rewrite_rules();
        update_option('jobs_rewrite_rules_flushed', 'yes');
    }
}
```

The 404 error should now be resolved with proper rewrite rules and template recognition!

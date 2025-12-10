# Fix 404 Error for Jobs Pages

## 🚨 **Problem**: 404 Error on Individual Job Pages

**Error**: `GET https://purple-alpaca-442178.hostingersite.com/jobs/application-developer/ 404 (Not Found)`

**Cause**: WordPress can't find the individual job pages because the permalinks need to be flushed after creating the custom post type.

## 🔧 **Solution Steps**

### **Step 1: Add Fix Code to theme-functions.php**

1. **Open your `theme-functions.php` file**
2. **Add the code** from `fix-404-error.php` at the end
3. **Save the file**

### **Step 2: Flush Permalinks**

1. **Go to WordPress Admin** → Settings → Permalinks
2. **Click "Save Changes"** (don't change anything, just save)
3. **This flushes the permalinks**

### **Step 3: Test the Fix**

1. **Go to your Jobs page** with `[jobs_listing]` shortcode
2. **Click "More Details →"** on any job
3. **Should see**: Individual job page with application form
4. **Should NOT see**: 404 error

## 🎯 **What the Fix Does**

### **Added Functions:**
- ✅ **Rewrite rules** for jobs URLs
- ✅ **404 fix handling** for job queries
- ✅ **Custom template detection** for single-jobs.php
- ✅ **Permalink flushing** on theme activation

### **How It Works:**
1. **Adds rewrite rules** so WordPress knows how to handle `/jobs/job-name/` URLs
2. **Fixes query handling** to properly load job posts
3. **Detects custom template** (single-jobs.php) for job pages
4. **Flushes permalinks** to apply the new rules

## 📋 **Complete Fix Process**

### **Step 1: Update theme-functions.php**
```php
// Add this code to the end of your theme-functions.php file
// (Copy the entire content from fix-404-error.php)
```

### **Step 2: Flush Permalinks**
1. **Go to Settings** → Permalinks
2. **Click "Save Changes"**
3. **Wait for the page to reload**

### **Step 3: Test**
1. **Visit your Jobs page**
2. **Click "More Details →"** on any job
3. **Should see**: Job detail page with application form

## 🔍 **Troubleshooting**

### **If Still Getting 404 Errors:**

1. **Check file permissions**:
   - `single-jobs.php` should be readable (644)
   - `theme-functions.php` should be readable (644)

2. **Check file locations**:
   - `single-jobs.php` should be in your theme root folder
   - `theme-functions.php` should be in your theme root folder

3. **Check for PHP errors**:
   - Go to your hosting control panel
   - Check error logs for any PHP syntax errors

4. **Try different permalink structure**:
   - Go to Settings → Permalinks
   - Try "Plain" structure temporarily
   - Save, then switch back to "Post name"

### **If Jobs Menu Doesn't Appear:**

1. **Check theme-functions.php** is being loaded:
   - Make sure the file is in your theme folder
   - Make sure the require_once line is in functions.php

2. **Check for PHP errors**:
   - Look for any syntax errors in the code
   - Check WordPress debug logs

## 🎯 **Expected Results After Fix**

### **✅ What Should Work:**
- **Jobs listing page**: `[jobs_listing]` shortcode displays jobs
- **"More Details →" links**: Clicking shows individual job pages
- **Individual job pages**: Load with MagicMyna design
- **Application forms**: Work properly on job pages
- **No 404 errors**: Job pages load correctly

### **✅ URLs That Should Work:**
- **Jobs listing**: `https://your-site.com/jobs/`
- **Individual job**: `https://your-site.com/jobs/application-developer/`
- **Jobs archive**: `https://your-site.com/jobs/`

## 🚀 **Quick Fix Commands**

### **If You Have Access to WordPress CLI:**
```bash
wp rewrite flush
wp cache flush
```

### **If You Have Access to Hosting Control Panel:**
1. **Clear all caches** (if using caching plugins)
2. **Check .htaccess file** for any conflicting rules
3. **Restart Apache/Nginx** if possible

## ✅ **Complete Fix Checklist**

- [ ] Added fix code to theme-functions.php
- [ ] Flushed permalinks (Settings → Permalinks → Save)
- [ ] Jobs menu appears in dashboard
- [ ] Jobs listing shortcode works
- [ ] "More Details →" links work
- [ ] Individual job pages load
- [ ] Application forms work
- [ ] No 404 errors

## 🎉 **Ready to Use!**

After applying the fix:
- ✅ **Jobs listing** works with `[jobs_listing]` shortcode
- ✅ **Individual job pages** load with MagicMyna design
- ✅ **Application forms** work properly
- ✅ **No 404 errors** on job pages
- ✅ **Professional job system** fully functional

The fix should resolve the 404 error and make your job pages work perfectly! 🚀

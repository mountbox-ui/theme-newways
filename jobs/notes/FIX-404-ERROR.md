# Fix 404 Error for Jobs Pages

## 🚨 **Problem**: 404 Error on Individual Job Pages

**Error**: `GET http://tov.local/jobs/web-developer/ 404 (Not Found)`

**Cause**: WordPress can't find the individual job pages because the permalinks need to be flushed after creating the custom post type.

## 🔧 **Solution Steps**

### **Step 1: Deactivate and Reactivate the Plugin**

1. **Go to WordPress Admin** → Plugins → Installed Plugins
2. **Find "Simple Jobs Plugin"**
3. **Click "Deactivate"**
4. **Click "Activate"** again

This will automatically flush the permalinks.

### **Step 2: Manual Permalink Flush (Alternative)**

If Step 1 doesn't work:

1. **Go to WordPress Admin** → Settings → Permalinks
2. **Click "Save Changes"** (don't change anything, just save)
3. **This flushes the permalinks**

### **Step 3: Clear Any Caching**

If you're using caching plugins:

1. **Clear all caches** (WP Rocket, W3 Total Cache, etc.)
2. **Clear browser cache**
3. **Try accessing the job page again**

## 🎯 **What Should Happen After Fix**

✅ **Jobs listing works**: `[jobs_listing]` shortcode displays jobs  
✅ **"More Details →" links work**: Clicking shows individual job pages  
✅ **No 404 errors**: Job pages load properly  
✅ **Application forms work**: Forms submit successfully  

## 🔍 **Testing the Fix**

### **Test 1: Jobs Listing**
1. **Go to your page** with `[jobs_listing]` shortcode
2. **Verify jobs are displayed**
3. **Check that "More Details →" links appear**

### **Test 2: Individual Job Pages**
1. **Click "More Details →"** on any job
2. **Should see**: Job details page with application form
3. **Should NOT see**: 404 error

### **Test 3: Application Form**
1. **Fill out the application form**
2. **Upload a resume** (optional)
3. **Click "Submit Application"**
4. **Should see**: Success message
5. **Check your email**: Should receive application notification

## 🛠️ **Advanced Troubleshooting**

### **If Still Getting 404 Errors:**

1. **Check .htaccess file**:
   - Make sure WordPress can write to .htaccess
   - Look for any conflicting rewrite rules

2. **Check server configuration**:
   - Ensure mod_rewrite is enabled
   - Check if your hosting has any restrictions

3. **Try different permalink structure**:
   - Go to Settings → Permalinks
   - Try "Plain" structure temporarily
   - Save, then switch back to "Post name"

### **If Jobs Menu Doesn't Appear:**

1. **Check plugin activation**:
   - Go to Plugins → Installed Plugins
   - Make sure "Simple Jobs Plugin" is active

2. **Check user permissions**:
   - Make sure you're logged in as Administrator
   - Go to Users → All Users
   - Check your role is "Administrator"

## 📋 **Complete Fix Checklist**

- [ ] Plugin is activated
- [ ] Permalinks flushed (Settings → Permalinks → Save)
- [ ] Cache cleared (if using caching plugins)
- [ ] Jobs menu appears in dashboard
- [ ] Jobs listing shortcode works
- [ ] "More Details →" links work
- [ ] Individual job pages load
- [ ] Application forms work
- [ ] Email notifications received

## 🎯 **Expected URLs After Fix**

**Jobs listing page**: `http://tov.local/your-jobs-page/`  
**Individual job**: `http://tov.local/jobs/web-developer/`  
**Jobs archive**: `http://tov.local/jobs/`  

## 🚀 **Quick Fix Commands**

If you have access to WordPress CLI:

```bash
wp rewrite flush
wp cache flush
```

## 📞 **Still Having Issues?**

If the 404 error persists:

1. **Check error logs** in your hosting control panel
2. **Try deactivating other plugins** temporarily
3. **Switch to a default theme** temporarily
4. **Contact your hosting provider** if server issues

The fix should resolve the 404 error and make your job pages work perfectly! 🎉

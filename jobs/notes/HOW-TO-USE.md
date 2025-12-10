# Jobs Plugin - Complete Usage Guide

## 🎯 **What This Plugin Does**

This WordPress plugin creates a complete job management system with:
- **Admin Dashboard**: Jobs menu for managing job postings
- **Frontend Display**: Shortcode to show jobs on your website
- **Individual Job Pages**: Custom template for job details and applications
- **Application Forms**: Complete job application system

## 📋 **Complete Setup Process**

### **Step 1: Install the Plugin**

1. **Upload the plugin**:
   - Go to WordPress Admin → Plugins → Add New → Upload Plugin
   - Upload `simple-jobs-plugin.php`
   - Click "Install Now" then "Activate"

2. **Verify installation**:
   - Look for "Jobs" menu in your WordPress dashboard sidebar
   - You should see a briefcase icon next to "Jobs"

### **Step 2: Create Your First Job**

1. **Go to Jobs menu** in WordPress dashboard
2. **Click "Add New"**
3. **Fill in the job details**:
   - **Title**: Enter job title (e.g., "Senior Web Developer")
   - **Content**: Write the job description
   - **Job Details** (in the meta box):
     - **Category**: Select from dropdown (Technology, Marketing, etc.)
     - **Job Type**: Choose Full Time, Part Time, Contract, etc.
     - **Location**: Enter location (e.g., "New York, NY" or "Remote")
4. **Click "Publish"**

### **Step 3: Display Jobs on Your Website**

1. **Create a new page** (e.g., "Careers" or "Jobs")
2. **Add the shortcode**: `[jobs_listing]`
3. **Publish the page**

## 🎨 **How It Works - Complete Flow**

### **Admin Side (WordPress Dashboard)**

```
Jobs Menu → Add New Job → Fill Details → Publish
```

**What you can do:**
- ✅ Create unlimited job postings
- ✅ Set Category, Job Type, Location for each job
- ✅ Edit existing jobs
- ✅ Delete jobs
- ✅ View all jobs in a list with custom columns

### **Frontend Side (Your Website)**

```
[jobs_listing] Shortcode → Job Cards → "More Details" → Individual Job Page
```

**What visitors see:**
- ✅ **Jobs listing page**: Clean cards with job titles and locations
- ✅ **Filter dropdowns**: Category, Job Type, Location filters
- ✅ **"More Details →" links**: Click to see full job details
- ✅ **Individual job pages**: Professional layout with application form

## 📱 **Three Main Components**

### **1. Jobs Listing (Shortcode)**

**Usage**: `[jobs_listing]`

**What it shows**:
- Filter dropdowns (Category, Job Type, Location)
- Job cards with titles and locations
- "More Details →" links
- Responsive design

**Options**:
```
[jobs_listing]                           # Show all jobs with filters
[jobs_listing show_filters="false"]      # Show all jobs without filters
[jobs_listing category="technology"]      # Show only technology jobs
[jobs_listing limit="5"]                 # Show only 5 jobs
```

### **2. Individual Job Pages**

**How it works**:
- When someone clicks "More Details →" from the jobs listing
- They see a custom job page with:
  - **Left side**: Job details, requirements, benefits
  - **Right side**: Application form

**What visitors can do**:
- ✅ Read complete job description
- ✅ See job requirements and benefits
- ✅ Fill out application form
- ✅ Upload resume
- ✅ Submit application

### **3. Application System**

**When someone applies**:
1. **Fills out the form** with their details
2. **Uploads resume** (PDF, DOC, DOCX)
3. **Writes cover letter** (optional)
4. **Clicks "Submit Application"**
5. **You receive email** with application details

## 🎯 **Perfect for Your Setup**

### **Hub Liquid Theme + Elementor**

**In Elementor**:
1. **Add Shortcode widget**
2. **Paste**: `[jobs_listing]`
3. **Update page**

**Result**: Professional job listings that match your theme

### **Customization Options**

**Shortcode Parameters**:
```
[jobs_listing category="technology"]     # Technology jobs only
[jobs_listing type="full-time"]         # Full-time jobs only
[jobs_listing location="Remote"]         # Remote jobs only
[jobs_listing limit="10"]                # Show 10 jobs maximum
[jobs_listing show_filters="false"]      # Hide filter dropdowns
```

## 📊 **Complete Workflow**

### **For You (Admin)**:
1. **Create jobs** in WordPress dashboard
2. **Set categories, types, locations**
3. **Publish jobs**
4. **Receive applications** via email
5. **Manage applications** from your inbox

### **For Visitors**:
1. **Visit your jobs page** (with `[jobs_listing]` shortcode)
2. **Browse job listings** with filters
3. **Click "More Details →"** on interesting jobs
4. **Read job details** and requirements
5. **Fill out application form**
6. **Submit application**

## 🎨 **Design Features**

### **Jobs Listing Page**:
- Clean, modern job cards
- Filter dropdowns for easy browsing
- Hover effects and smooth animations
- Mobile-responsive design

### **Individual Job Pages**:
- Two-column layout (job details + application form)
- Professional styling matching your theme
- File upload for resumes
- Complete application form

### **Admin Dashboard**:
- Custom "Jobs" menu with briefcase icon
- Easy job creation and management
- Custom columns showing Category, Type, Location
- Standard WordPress interface

## 🔧 **Technical Details**

### **Files Created**:
- `simple-jobs-plugin.php` - Main plugin file
- `single-job-template.php` - Custom job page template (auto-created)

### **Database**:
- Custom post type: `jobs`
- Custom fields: `_job_category`, `_job_type`, `_job_location`
- Standard WordPress post meta storage

### **Security**:
- Nonce verification for forms
- Input sanitization
- File upload validation
- User permission checks

## 📧 **Email Notifications**

**When someone applies for a job, you receive**:
- Applicant's name and contact info
- Job they applied for
- Resume file (if uploaded)
- Cover letter
- Experience level
- Location

**Email goes to**: Your WordPress admin email address

## 🚀 **Quick Start Checklist**

- [ ] Upload and activate the plugin
- [ ] Create your first job posting
- [ ] Add `[jobs_listing]` shortcode to a page
- [ ] Test the job listing display
- [ ] Test the "More Details" functionality
- [ ] Test the application form
- [ ] Check that you receive application emails

## 🎯 **Perfect Integration**

This plugin works seamlessly with:
- ✅ **Hub Liquid theme**
- ✅ **Elementor page builder**
- ✅ **WordPress Gutenberg editor**
- ✅ **Any WordPress theme**
- ✅ **Mobile devices**
- ✅ **All browsers**

## 📞 **Support**

If you need help:
1. **Check the shortcode**: Make sure `[jobs_listing]` is correctly placed
2. **Verify plugin activation**: Jobs menu should appear in dashboard
3. **Test with sample data**: Create a test job to verify functionality
4. **Check email settings**: Ensure WordPress can send emails

Your Jobs plugin is now ready to use! 🎉

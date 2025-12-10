# Jobs Shortcode Usage Guide

## 🎯 **Shortcode: `[jobs_listing]`**

I've added a shortcode to your plugin that displays jobs in the exact same model as the MagicMyna job listings page you showed me.

## 📋 **Basic Usage**

### **Simple Display (All Jobs)**
```
[jobs_listing]
```

### **With Filters**
```
[jobs_listing show_filters="true"]
```

### **Without Filters**
```
[jobs_listing show_filters="false"]
```

## ⚙️ **Advanced Options**

### **Filter by Category**
```
[jobs_listing category="technology"]
```

### **Filter by Job Type**
```
[jobs_listing type="full-time"]
```

### **Filter by Location**
```
[jobs_listing location="Remote"]
```

### **Limit Number of Jobs**
```
[jobs_listing limit="5"]
```

### **Multiple Filters**
```
[jobs_listing category="technology" type="full-time" location="Remote" limit="10"]
```

## 🎨 **Features Included**

✅ **Exact Model Match**: Replicates the MagicMyna design  
✅ **Filter Dropdowns**: Category, Job Type, Location  
✅ **Job Cards**: Clean card layout with hover effects  
✅ **Responsive Design**: Works on mobile and desktop  
✅ **Interactive Filtering**: Real-time filtering without page reload  
✅ **"More Details" Links**: Direct links to job details  
✅ **Professional Styling**: Modern, clean appearance  

## 📱 **How It Looks**

The shortcode creates:
- **Filter Bar**: Three dropdown menus (Category, Type, Location)
- **Job Cards**: Each job in a clean card with:
  - Job title (bold, dark blue)
  - Location (gray text below title)
  - "More Details →" link (blue, right-aligned)
- **Hover Effects**: Cards lift and highlight on hover
- **Responsive**: Stacks vertically on mobile

## 🚀 **How to Use**

### **Step 1: Upload & Activate Plugin**
1. Upload `simple-jobs-plugin.php` to WordPress
2. Activate the plugin
3. Create some jobs in the Jobs menu

### **Step 2: Add Shortcode to Page**
1. **Create a new page** (e.g., "Careers" or "Jobs")
2. **Add the shortcode**: `[jobs_listing]`
3. **Publish the page**

### **Step 3: Customize (Optional)**
- **With filters**: `[jobs_listing show_filters="true"]`
- **Without filters**: `[jobs_listing show_filters="false"]`
- **Specific category**: `[jobs_listing category="technology"]`

## 🎯 **Perfect for Elementor**

### **In Elementor:**
1. **Add a Shortcode widget**
2. **Paste**: `[jobs_listing]`
3. **Update the page**

### **In Gutenberg:**
1. **Add a Shortcode block**
2. **Paste**: `[jobs_listing]`
3. **Preview/Publish**

## 📊 **Shortcode Parameters**

| Parameter | Options | Description |
|-----------|---------|-------------|
| `category` | technology, marketing, sales, etc. | Filter by job category |
| `type` | full-time, part-time, contract, etc. | Filter by job type |
| `location` | Remote, New York, California, etc. | Filter by location |
| `limit` | Any number (e.g., 5, 10) | Limit number of jobs shown |
| `show_filters` | true, false | Show/hide filter dropdowns |

## 🎨 **Styling Features**

- **Clean Design**: Matches professional job sites
- **Hover Effects**: Cards lift and highlight
- **Responsive**: Mobile-friendly layout
- **Modern Typography**: Clean, readable fonts
- **Color Scheme**: Professional blue and gray
- **Smooth Transitions**: Elegant animations

## 🔧 **Customization**

The shortcode includes built-in CSS that matches the MagicMyna model. The styling is:
- **Self-contained**: No external CSS files needed
- **Responsive**: Works on all devices
- **Professional**: Clean, modern appearance
- **Compatible**: Works with any theme

## 📝 **Example Pages**

### **Careers Page**
```
# Join Our Team
We're always looking for talented individuals to join our growing team.

[jobs_listing]
```

### **Technology Jobs Only**
```
# Technology Positions
[jobs_listing category="technology" show_filters="true"]
```

### **Remote Jobs**
```
# Remote Opportunities
[jobs_listing location="Remote" limit="10"]
```

## ✅ **Ready to Use!**

Your shortcode is now ready! Just add `[jobs_listing]` to any page or post, and you'll get the exact same job listing model as the MagicMyna page you showed me.

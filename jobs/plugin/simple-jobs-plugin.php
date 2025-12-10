<?php
/**
 * Plugin Name: Simple Jobs Plugin
 * Description: Simple Jobs post type with Category, Job Type, and Location fields
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Create Jobs Post Type
function create_jobs_post_type() {
    $labels = array(
        'name' => 'Jobs',
        'singular_name' => 'Job',
        'menu_name' => 'Jobs',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Job',
        'edit_item' => 'Edit Job',
        'new_item' => 'New Job',
        'view_item' => 'View Job',
        'search_items' => 'Search Jobs',
        'not_found' => 'No jobs found',
        'not_found_in_trash' => 'No jobs found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'show_in_rest' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-businessman',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'jobs', 'with_front' => false),
        'query_var' => true,
        'capability_type' => 'post',
    );

    register_post_type('jobs', $args);
}
add_action('init', 'create_jobs_post_type');

// Add meta box for job details
function add_jobs_meta_box() {
    add_meta_box(
        'jobs_details',
        'Job Details',
        'jobs_meta_box_callback',
        'jobs',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_jobs_meta_box');

// Meta box callback
function jobs_meta_box_callback($post) {
    wp_nonce_field('jobs_meta_box', 'jobs_meta_box_nonce');
    
    $category = get_post_meta($post->ID, '_job_category', true);
    $job_type = get_post_meta($post->ID, '_job_type', true);
    $location = get_post_meta($post->ID, '_job_location', true);
    $job_status = get_post_meta($post->ID, '_job_status', true);
    $job_status = $job_status ? $job_status : 'active'; // Default to active
    ?>
    <table class="form-table">
        <tr>
            <th><label for="job_category">Category</label></th>
            <td>
                <select name="job_category" id="job_category" style="width: 100%;">
                    <option value="">Select Category</option>
                    <option value="technology" <?php selected($category, 'technology'); ?>>Technology</option>
                    <option value="marketing" <?php selected($category, 'marketing'); ?>>Marketing</option>
                    <option value="sales" <?php selected($category, 'sales'); ?>>Sales</option>
                    <option value="finance" <?php selected($category, 'finance'); ?>>Finance</option>
                    <option value="hr" <?php selected($category, 'hr'); ?>>Human Resources</option>
                    <option value="operations" <?php selected($category, 'operations'); ?>>Operations</option>
                    <option value="design" <?php selected($category, 'design'); ?>>Design</option>
                    <option value="customer-service" <?php selected($category, 'customer-service'); ?>>Customer Service</option>
                    <option value="other" <?php selected($category, 'other'); ?>>Other</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="job_type">Job Type</label></th>
            <td>
                <select name="job_type" id="job_type" style="width: 100%;">
                    <option value="">Select Job Type</option>
                    <option value="full-time" <?php selected($job_type, 'full-time'); ?>>Full Time</option>
                    <option value="part-time" <?php selected($job_type, 'part-time'); ?>>Part Time</option>
                    <option value="contract" <?php selected($job_type, 'contract'); ?>>Contract</option>
                    <option value="freelance" <?php selected($job_type, 'freelance'); ?>>Freelance</option>
                    <option value="internship" <?php selected($job_type, 'internship'); ?>>Internship</option>
                    <option value="temporary" <?php selected($job_type, 'temporary'); ?>>Temporary</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="job_location">Location</label></th>
            <td>
                <input type="text" name="job_location" id="job_location" value="<?php echo esc_attr($location); ?>" style="width: 100%;" placeholder="e.g., New York, NY or Remote" />
            </td>
        </tr>
        <tr>
            <th><label for="job_status">Status</label></th>
            <td>
                <select name="job_status" id="job_status" style="width: 100%;">
                    <option value="active" <?php selected($job_status, 'active'); ?>>Active</option>
                    <option value="inactive" <?php selected($job_status, 'inactive'); ?>>Inactive</option>
                </select>
                <p class="description">Active jobs will be visible to applicants, inactive jobs will be hidden.</p>
            </td>
        </tr>
        <tr>
            <th><label for="job_responsibilities">Responsibilities</label></th>
            <td>
                <textarea name="job_responsibilities" id="job_responsibilities" rows="6" style="width: 100%;" placeholder="Enter job responsibilities (one per line)"><?php echo esc_textarea(get_post_meta($post->ID, '_job_responsibilities', true)); ?></textarea>
                <p class="description">Enter each responsibility on a new line. They will be displayed as bullet points.</p>
            </td>
        </tr>
        <tr>
            <th><label for="job_requirements">Requirements</label></th>
            <td>
                <textarea name="job_requirements" id="job_requirements" rows="6" style="width: 100%;" placeholder="Enter job requirements (one per line)"><?php echo esc_textarea(get_post_meta($post->ID, '_job_requirements', true)); ?></textarea>
                <p class="description">Enter each requirement on a new line. They will be displayed as bullet points.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Save meta fields
function save_jobs_meta_fields($post_id) {
    if (!isset($_POST['jobs_meta_box_nonce']) || !wp_verify_nonce($_POST['jobs_meta_box_nonce'], 'jobs_meta_box')) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['job_category'])) {
        update_post_meta($post_id, '_job_category', sanitize_text_field($_POST['job_category']));
    }
    
    if (isset($_POST['job_type'])) {
        update_post_meta($post_id, '_job_type', sanitize_text_field($_POST['job_type']));
    }
    
    if (isset($_POST['job_location'])) {
        update_post_meta($post_id, '_job_location', sanitize_text_field($_POST['job_location']));
    }
    
    if (isset($_POST['job_status'])) {
        update_post_meta($post_id, '_job_status', sanitize_text_field($_POST['job_status']));
    }
    
    if (isset($_POST['job_responsibilities'])) {
        update_post_meta($post_id, '_job_responsibilities', sanitize_textarea_field($_POST['job_responsibilities']));
    }
    
    if (isset($_POST['job_requirements'])) {
        update_post_meta($post_id, '_job_requirements', sanitize_textarea_field($_POST['job_requirements']));
    }
}
add_action('save_post', 'save_jobs_meta_fields');

// Add custom columns
function add_jobs_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['job_category'] = 'Category';
    $new_columns['job_type'] = 'Job Type';
    $new_columns['job_location'] = 'Location';
    $new_columns['job_status'] = 'Status';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_jobs_posts_columns', 'add_jobs_columns');

// Populate custom columns
function populate_jobs_columns($column, $post_id) {
    switch ($column) {
        case 'job_category':
            $category = get_post_meta($post_id, '_job_category', true);
            echo ucfirst(str_replace('-', ' ', $category));
            break;
        case 'job_type':
            $job_type = get_post_meta($post_id, '_job_type', true);
            echo ucfirst(str_replace('-', ' ', $job_type));
            break;
        case 'job_location':
            echo get_post_meta($post_id, '_job_location', true);
            break;
        case 'job_status':
            $status = get_post_meta($post_id, '_job_status', true);
            $status = $status ? $status : 'active';
            echo '<span class="status-badge status-' . esc_attr($status) . '">' . ucfirst($status) . '</span>';
            break;
    }
}
add_action('manage_jobs_posts_custom_column', 'populate_jobs_columns', 10, 2);

// Add some basic styling
function jobs_admin_styles() {
    ?>
    <style>
        .jobs-meta-box .form-table th {
            width: 150px;
        }
        .jobs-meta-box .form-table td {
            padding: 10px;
        }
        .jobs-meta-box select,
        .jobs-meta-box input[type="text"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
    <?php
}
add_action('admin_head', 'jobs_admin_styles');

// Add shortcode for displaying jobs
function jobs_display_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'type' => '',
        'location' => '',
        'limit' => -1,
        'show_filters' => 'true'
    ), $atts);
    
    // Build meta query with proper status filtering
    $meta_query = array();
    
    // Status filter - only show active jobs (exclude inactive ones)
    $meta_query[] = array(
        'relation' => 'OR',
        array(
            'key' => '_job_status',
            'value' => 'active',
            'compare' => '='
        ),
        array(
            'key' => '_job_status',
            'compare' => 'NOT EXISTS'
        ),
        array(
            'key' => '_job_status',
            'value' => '',
            'compare' => '='
        )
    );
    
    // Add category filter if specified
    if (!empty($atts['category'])) {
        $meta_query[] = array(
            'key' => '_job_category',
            'value' => $atts['category'],
            'compare' => '='
        );
    }
    
    // Add type filter if specified
    if (!empty($atts['type'])) {
        $meta_query[] = array(
            'key' => '_job_type',
            'value' => $atts['type'],
            'compare' => '='
        );
    }
    
    // Add location filter if specified
    if (!empty($atts['location'])) {
        $meta_query[] = array(
            'key' => '_job_location',
            'value' => $atts['location'],
            'compare' => 'LIKE'
        );
    }
    
    // Set relation to AND if we have multiple conditions
    if (count($meta_query) > 1) {
        $meta_query['relation'] = 'AND';
    }
    
    // Get all jobs (only active ones)
    $args = array(
        'post_type' => 'jobs',
        'posts_per_page' => $atts['limit'],
        'post_status' => 'publish',
        'meta_query' => $meta_query
    );
    
    $jobs_query = new WP_Query($args);
    
    ob_start();
    ?>
    <div class="jobs-listing-container">
        <?php if ($atts['show_filters'] === 'true') : ?>
        <div class="jobs-filters">
            <select id="category-filter" class="job-filter">
                <option value="">All Job Category</option>
                <option value="technology">Technology</option>
                <option value="marketing">Marketing</option>
                <option value="sales">Sales</option>
                <option value="finance">Finance</option>
                <option value="hr">Human Resources</option>
                <option value="operations">Operations</option>
                <option value="design">Design</option>
                <option value="customer-service">Customer Service</option>
                <option value="other">Other</option>
            </select>
            
            <select id="type-filter" class="job-filter">
                <option value="">All Job Type</option>
                <option value="full-time">Full Time</option>
                <option value="part-time">Part Time</option>
                <option value="contract">Contract</option>
                <option value="freelance">Freelance</option>
                <option value="internship">Internship</option>
                <option value="temporary">Temporary</option>
            </select>
            
            <select id="location-filter" class="job-filter">
                <option value="">All Job Location</option>
                <option value="Remote">Remote</option>
                <option value="New York">New York</option>
                <option value="California">California</option>
                <option value="Texas">Texas</option>
                <option value="Florida">Florida</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <?php endif; ?>
        
        <div class="jobs-list">
            <?php if ($jobs_query->have_posts()) : ?>
                <?php while ($jobs_query->have_posts()) : $jobs_query->the_post(); ?>
                    <?php 
                    $category = get_post_meta(get_the_ID(), '_job_category', true);
                    $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                    $location = get_post_meta(get_the_ID(), '_job_location', true);
                    
                    // Get activation date or fallback to post date
                    $activation_date = get_post_meta(get_the_ID(), '_job_activation_date', true);
                    $display_date = $activation_date ? $activation_date : get_the_date('Y-m-d H:i:s');
                    ?>
                    <div class="job-card" 
                         data-category="<?php echo esc_attr($category); ?>"
                         data-type="<?php echo esc_attr($job_type); ?>"
                         data-location="<?php echo esc_attr($location); ?>">
                        
                        <div class="job-content">
                            <h3 class="job-title"><?php the_title(); ?></h3>
                            <p class="job-location"><?php echo esc_html($location); ?></p>
                        </div>
                        
                        <div class="job-actions">
                            <a href="<?php the_permalink(); ?>" class="more-details-link">
                                More Details →
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-jobs">
                    <p>No jobs available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
    .jobs-listing-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .jobs-filters {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px 0;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .job-filter {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #d0d0d0;
        border-radius: 6px;
        background: white;
        font-size: 14px;
        color: #333;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }
    
    .job-filter:focus {
        outline: none;
        border-color: #0073aa;
        box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
    }
    
    .jobs-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .job-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: white;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .job-card:hover {
        border-color: #0073aa;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .job-content {
        flex: 1;
    }
    
    .job-title {
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.3;
    }
    
    .job-location {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #666;
        font-weight: 400;
    }
    
    
    .job-actions {
        margin-left: 20px;
    }
    
    .more-details-link {
        color: #0073aa;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .more-details-link:hover {
        color: #005a87;
        text-decoration: none;
    }
    
    .no-jobs {
        text-align: center;
        padding: 60px 20px;
        color: #666;
        font-size: 16px;
    }
    
    /* Filter functionality */
    .job-card.hidden {
        display: none;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .jobs-filters {
            flex-direction: column;
            gap: 10px;
        }
        
        .job-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .job-actions {
            margin-left: 0;
            width: 100%;
        }
        
        .more-details-link {
            justify-content: center;
            width: 100%;
            padding: 12px;
            border: 1px solid #0073aa;
            border-radius: 6px;
            text-align: center;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryFilter = document.getElementById('category-filter');
        const typeFilter = document.getElementById('type-filter');
        const locationFilter = document.getElementById('location-filter');
        const jobCards = document.querySelectorAll('.job-card');
        
        function filterJobs() {
            const selectedCategory = categoryFilter ? categoryFilter.value : '';
            const selectedType = typeFilter ? typeFilter.value : '';
            const selectedLocation = locationFilter ? locationFilter.value : '';
            
            jobCards.forEach(card => {
                const cardCategory = card.dataset.category;
                const cardType = card.dataset.type;
                const cardLocation = card.dataset.location;
                
                const categoryMatch = !selectedCategory || cardCategory === selectedCategory;
                const typeMatch = !selectedType || cardType === selectedType;
                const locationMatch = !selectedLocation || cardLocation.toLowerCase().includes(selectedLocation.toLowerCase());
                
                if (categoryMatch && typeMatch && locationMatch) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
        
        if (categoryFilter) categoryFilter.addEventListener('change', filterJobs);
        if (typeFilter) typeFilter.addEventListener('change', filterJobs);
        if (locationFilter) locationFilter.addEventListener('change', filterJobs);
    });
    </script>
    <?php
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('jobs_listing', 'jobs_display_shortcode');

// Custom single job template
function custom_jobs_single_template($template) {
    if (is_singular('jobs')) {
        $custom_template = plugin_dir_path(__FILE__) . 'single-job-template.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'custom_jobs_single_template');

// Create single job template file
function create_single_job_template() {
    $template_content = '<?php
/**
 * Custom Single Job Template
 * This template displays individual job details in the same model as the Drone Bootcamps page
 */

get_header(); ?>

<div class="job-detail-container">
    <div class="job-content-wrapper">
        <div class="job-main-content">
            <?php while (have_posts()) : the_post(); ?>
                <?php 
                $category = get_post_meta(get_the_ID(), \'_job_category\', true);
                $job_type = get_post_meta(get_the_ID(), \'_job_type\', true);
                $location = get_post_meta(get_the_ID(), \'_job_location\', true);
                $salary = get_post_meta(get_the_ID(), \'_job_salary\', true);
                $company = get_post_meta(get_the_ID(), \'_job_company\', true);
                ?>
                
                <div class="job-header">
                    <h1 class="job-title"><?php the_title(); ?></h1>
                    <p class="job-company-location"><?php echo esc_html($company); ?> - <?php echo esc_html($location); ?></p>
                </div>
                
                <div class="job-highlights">
                    <div class="job-badge">
                        <span class="badge-icon">⏱️</span>
                        <span class="badge-text"><?php echo ucfirst(str_replace(\'-\', \' \', $job_type)); ?></span>
                    </div>
                    <div class="job-badge">
                        <span class="badge-icon">📍</span>
                        <span class="badge-text"><?php echo esc_html($location); ?></span>
                    </div>
                    <?php if ($salary) : ?>
                    <div class="job-badge">
                        <span class="badge-icon">💰</span>
                        <span class="badge-text"><?php echo esc_html($salary); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="job-description">
                    <div class="job-content">
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <?php 
                $responsibilities = get_post_meta(get_the_ID(), \'_job_responsibilities\', true);
                if (!empty($responsibilities)) : 
                ?>
                <div class="job-responsibilities">
                    <h2>Responsibilities</h2>
                    <ul class="responsibilities-list">
                        <?php 
                        $responsibility_lines = explode("\n", $responsibilities);
                        foreach ($responsibility_lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                echo \'<li>\' . esc_html($line) . \'</li>\';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php 
                $requirements = get_post_meta(get_the_ID(), \'_job_requirements\', true);
                if (!empty($requirements)) : 
                ?>
                <div class="job-requirements">
                    <h2>Requirements</h2>
                    <ul class="requirements-list">
                        <?php 
                        $requirement_lines = explode("\n", $requirements);
                        foreach ($requirement_lines as $line) {
                            $line = trim($line);
                            if (!empty($line)) {
                                echo \'<li>\' . esc_html($line) . \'</li>\';
                            }
                        }
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                
            <?php endwhile; ?>
        </div>
        
        <div class="job-application-form">
            <div class="form-container">
                <h2>Apply for this Position</h2>
                <form id="job-application-form" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field(\'job_application_nonce\', \'job_application_nonce_field\'); ?>
                    <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
                    
                    <div class="form-group">
                        <label for="applicant_name">Full Name *</label>
                        <input type="text" id="applicant_name" name="applicant_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="applicant_email">Email Address *</label>
                        <input type="email" id="applicant_email" name="applicant_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="applicant_phone">Phone Number</label>
                        <input type="tel" id="applicant_phone" name="applicant_phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="applicant_location">Current Location</label>
                        <input type="text" id="applicant_location" name="applicant_location">
                    </div>
                    
                    <div class="form-group">
                        <label for="applicant_experience">Years of Experience</label>
                        <select id="applicant_experience" name="applicant_experience">
                            <option value="">Select Experience</option>
                            <option value="0-1">0-1 years</option>
                            <option value="2-3">2-3 years</option>
                            <option value="4-5">4-5 years</option>
                            <option value="6-10">6-10 years</option>
                            <option value="10+">10+ years</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="resume_upload">Upload Resume *</label>
                        <input type="file" id="resume_upload" name="resume_upload" accept=".pdf,.doc,.docx" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cover_letter">Cover Letter</label>
                        <textarea id="cover_letter" name="cover_letter" rows="4" placeholder="Tell us why you\'re interested in this position..."></textarea>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="privacy_policy" required>
                            <span class="checkmark"></span>
                            I accept the Privacy Policy and agree to the processing of my personal data
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">Submit Application</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.job-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    background: #f8f9fa;
    min-height: 100vh;
}

.job-content-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    align-items: start;
}

.job-main-content {
    background: #f8f9fa;
    padding: 0;
}

.job-header {
    margin-bottom: 30px;
}

.job-title {
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.job-company-location {
    font-size: 16px;
    color: #4a5568;
    margin: 0 0 20px 0;
    font-weight: 500;
}

.job-highlights {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
}

.job-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #e6f3ff;
    color: #2d3748;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    border: 1px solid #cbd5e0;
}

.badge-icon {
    font-size: 16px;
}

.job-description,
.job-responsibilities,
.job-requirements {
    margin-bottom: 30px;
}

.job-description h2,
.job-responsibilities h2,
.job-requirements h2 {
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 15px 0;
    text-transform: none;
}

.job-content {
    font-size: 16px;
    line-height: 1.6;
    color: #333;
}

.responsibilities-list,
.requirements-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.responsibilities-list li,
.requirements-list li {
    padding: 8px 0;
    font-size: 16px;
    color: #2d3748;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    line-height: 1.5;
}

.responsibilities-list li::before,
.requirements-list li::before {
    content: "✓";
    color: #4a90e2;
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0;
    margin-top: 2px;
}

.job-application-form {
    position: sticky;
    top: 20px;
}

.form-container {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
}

.form-container h2 {
    font-size: 24px;
    font-weight: 600;
    color: #1a365d;
    margin: 0 0 25px 0;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d0d0d0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s ease;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1a365d;
    box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.1);
}

.checkbox-group {
    margin-bottom: 25px;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    line-height: 1.4;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.submit-btn {
    width: 100%;
    background: #1a365d;
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.submit-btn:hover {
    background: #153a5e;
}

.submit-btn:active {
    transform: translateY(1px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .job-content-wrapper {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .job-application-form {
        position: static;
    }
    
    .job-title {
        font-size: 28px;
    }
    
    .job-highlights {
        flex-direction: column;
        gap: 10px;
    }
}

/* Success/Error Messages */
.application-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.application-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.application-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php get_footer(); ?>';

    $template_file = plugin_dir_path(__FILE__) . 'single-job-template.php';
    file_put_contents($template_file, $template_content);
}

// Handle job application form submission
function handle_job_application() {
    if (isset($_POST['job_application_nonce_field']) && wp_verify_nonce($_POST['job_application_nonce_field'], 'job_application_nonce')) {
        $job_id = intval($_POST['job_id']);
        $applicant_name = sanitize_text_field($_POST['applicant_name']);
        $applicant_email = sanitize_email($_POST['applicant_email']);
        $applicant_phone = sanitize_text_field($_POST['applicant_phone']);
        $applicant_location = sanitize_text_field($_POST['applicant_location']);
        $applicant_experience = sanitize_text_field($_POST['applicant_experience']);
        $cover_letter = sanitize_textarea_field($_POST['cover_letter']);
        
        // Handle file upload
        if (!empty($_FILES['resume_upload']['name'])) {
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'] . '/' . sanitize_file_name($_FILES['resume_upload']['name']);
            
            if (move_uploaded_file($_FILES['resume_upload']['tmp_name'], $upload_path)) {
                $resume_url = $upload_dir['url'] . '/' . sanitize_file_name($_FILES['resume_upload']['name']);
            }
        }
        
        // Send email notification to admin
        $admin_email = get_option('admin_email');
        $admin_subject = 'New Job Application: ' . get_the_title($job_id);
        $admin_message = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2d3748; border-bottom: 2px solid #4a90e2; padding-bottom: 10px;'>New Job Application Received</h2>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #2d3748; margin-top: 0;'>Job Details</h3>
                    <p><strong>Position:</strong> " . get_the_title($job_id) . "</p>
                    <p><strong>Application Date:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                </div>
                
                <div style='background: #e6f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #2d3748; margin-top: 0;'>Applicant Information</h3>
                    <p><strong>Name:</strong> $applicant_name</p>
                    <p><strong>Email:</strong> $applicant_email</p>
                    <p><strong>Phone:</strong> " . ($applicant_phone ? $applicant_phone : 'Not provided') . "</p>
                    <p><strong>Location:</strong> " . ($applicant_location ? $applicant_location : 'Not provided') . "</p>
                    <p><strong>Experience:</strong> " . ($applicant_experience ? $applicant_experience : 'Not specified') . "</p>
                    <p><strong>Resume:</strong> " . (isset($resume_url) ? '<a href="' . $resume_url . '">Download Resume</a>' : 'No resume uploaded') . "</p>
                </div>
                
                " . ($cover_letter ? "
                <div style='background: #fff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #4a90e2;'>
                    <h3 style='color: #2d3748; margin-top: 0;'>Cover Letter</h3>
                    <p style='white-space: pre-wrap;'>$cover_letter</p>
                </div>
                " : "") . "
                
                <div style='margin-top: 30px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;'>
                    <p style='margin: 0; color: #155724;'><strong>Next Steps:</strong> Review the application and contact the candidate if they meet your requirements.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $admin_headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);
        
        // Send confirmation email to applicant
        $applicant_subject = 'Application Submitted Successfully - ' . get_the_title($job_id);
        $applicant_message = "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2d3748; text-align: center;'>Application Submitted Successfully!</h2>
                
                <div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <h3 style='color: #155724; margin-top: 0;'>✅ Thank You for Your Application</h3>
                    <p style='color: #155724; margin-bottom: 0;'>Your application has been received and is being reviewed by our team.</p>
                </div>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #2d3748; margin-top: 0;'>Application Details</h3>
                    <p><strong>Position Applied For:</strong> " . get_the_title($job_id) . "</p>
                    <p><strong>Application Date:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                    <p><strong>Applicant Name:</strong> $applicant_name</p>
                </div>
                
                <div style='background: #e6f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #2d3748; margin-top: 0;'>What Happens Next?</h3>
                    <ul style='color: #4a5568; padding-left: 20px;'>
                        <li>Our team will review your application and resume</li>
                        <li>If you are shortlisted, we will contact you within 5-7 business days</li>
                        <li>We may reach out for additional information or to schedule an interview</li>
                        <li>If you don't hear from us within 2 weeks, please feel free to follow up</li>
                    </ul>
                </div>
                
                <div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    <h3 style='color: #856404; margin-top: 0;'>Important Note</h3>
                    <p style='color: #856404; margin-bottom: 0;'>If you are shortlisted, our team will contact you directly. Please ensure your contact information is up to date.</p>
                </div>
                
                <div style='text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                    <p style='color: #6c757d; margin: 0;'>Thank you for your interest in joining our team!</p>
                    <p style='color: #6c757d; margin: 5px 0 0 0;'>Best regards,<br>Hiring Team</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $applicant_headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($applicant_email, $applicant_subject, $applicant_message, $applicant_headers);
        
        // Store application data for overview
        $application_data = array(
            'job_id' => $job_id,
            'job_title' => get_the_title($job_id),
            'applicant_name' => $applicant_name,
            'applicant_email' => $applicant_email,
            'applicant_phone' => $applicant_phone,
            'applicant_location' => $applicant_location,
            'applicant_experience' => $applicant_experience,
            'cover_letter' => $cover_letter,
            'resume_url' => isset($resume_url) ? $resume_url : '',
            'application_date' => current_time('mysql'),
            'status' => 'pending'
        );
        
        // Store in custom table or options
        $applications = get_option('job_applications', array());
        $applications[] = $application_data;
        update_option('job_applications', $applications);
        
        // Show success message
        add_action('wp_footer', function() {
            echo '<div id="application-success-message" style="position: fixed; top: 20px; right: 20px; background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #28a745; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; max-width: 400px;">
                <strong>✅ Application Submitted Successfully!</strong><br>
                Thank you for your application. We will review it and contact you if you are shortlisted.
            </div>
            <script>
                setTimeout(function() {
                    var message = document.getElementById("application-success-message");
                    if (message) {
                        message.style.opacity = "0";
                        message.style.transition = "opacity 0.5s ease";
                        setTimeout(function() {
                            message.remove();
                        }, 500);
                    }
                }, 5000);
            </script>';
        });
    }
}
add_action('init', 'handle_job_application');

// Set default status for existing jobs without status
function set_default_job_status_plugin() {
    $jobs_without_status = get_posts(array(
        'post_type' => 'jobs',
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_query' => array(
            array(
                'key' => '_job_status',
                'compare' => 'NOT EXISTS'
            )
        )
    ));
    
    foreach ($jobs_without_status as $job) {
        update_post_meta($job->ID, '_job_status', 'active');
    }
}

// Flush permalinks on plugin activation
function flush_jobs_permalinks() {
    create_jobs_post_type();
    create_single_job_template();
    set_default_job_status_plugin();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'flush_jobs_permalinks');

// Add rewrite rules for jobs
function add_jobs_rewrite_rules() {
    add_rewrite_rule('^jobs/([^/]+)/?$', 'index.php?post_type=jobs&name=$matches[1]', 'top');
}
add_action('init', 'add_jobs_rewrite_rules');

// Fix 404 issues by ensuring proper query handling
function handle_jobs_404_fix($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'jobs') {
            $query->set('post_type', 'jobs');
        }
    }
}
add_action('pre_get_posts', 'handle_jobs_404_fix');

// Add Overview submenu
function add_jobs_overview_submenu() {
    add_submenu_page(
        'edit.php?post_type=jobs',
        'Overview',
        'Overview',
        'manage_options',
        'jobs-overview',
        'jobs_overview_page'
    );
}
add_action('admin_menu', 'add_jobs_overview_submenu');

// Overview page content
function jobs_overview_page() {
    // Get statistics
    $total_jobs = wp_count_posts('jobs');
    $published_jobs = $total_jobs->publish;
    $draft_jobs = $total_jobs->draft;
    
    $applications = get_option('job_applications', array());
    $total_applications = count($applications);
    
    // Get recent applications (last 10)
    $recent_applications = array_slice(array_reverse($applications), 0, 10);
    
    // Get applications by job
    $applications_by_job = array();
    foreach ($applications as $app) {
        $job_title = $app['job_title'];
        if (!isset($applications_by_job[$job_title])) {
            $applications_by_job[$job_title] = 0;
        }
        $applications_by_job[$job_title]++;
    }
    
    ?>
    <div class="wrap">
        <h1>Jobs Overview Dashboard</h1>
        
        <div class="jobs-overview-dashboard">
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-content">
                        <h3><?php echo $published_jobs; ?></h3>
                        <p>Published Jobs</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📝</div>
                    <div class="stat-content">
                        <h3><?php echo $draft_jobs; ?></h3>
                        <p>Draft Jobs</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📨</div>
                    <div class="stat-content">
                        <h3><?php echo $total_applications; ?></h3>
                        <p>Total Applications</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3><?php echo count($applications_by_job); ?></h3>
                        <p>Jobs with Applications</p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="overview-section">
                <h2>Recent Applications (Last 10)</h2>
                <?php if (!empty($recent_applications)) : ?>
                    <div class="applications-table">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Experience</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_applications as $index => $app) : ?>
                                    <tr>
                                        <td><strong><?php echo esc_html($app['applicant_name']); ?></strong></td>
                                        <td><?php echo esc_html($app['job_title']); ?></td>
                                        <td><?php echo esc_html($app['applicant_email']); ?></td>
                                        <td><?php echo esc_html($app['applicant_phone'] ? $app['applicant_phone'] : 'N/A'); ?></td>
                                        <td><?php echo esc_html($app['applicant_experience'] ? $app['applicant_experience'] : 'N/A'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo esc_attr($app['status']); ?>">
                                                <?php echo ucfirst($app['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($app['resume_url']) : ?>
                                                <a href="<?php echo esc_url($app['resume_url']); ?>" class="button button-small" target="_blank">Resume</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p>No applications received yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- Applications by Job -->
            <div class="overview-section">
                <h2>Applications by Job Position</h2>
                <?php if (!empty($applications_by_job)) : ?>
                    <div class="job-stats">
                        <?php foreach ($applications_by_job as $job_title => $count) : ?>
                            <div class="job-stat-item">
                                <div class="job-title"><?php echo esc_html($job_title); ?></div>
                                <div class="job-count"><?php echo $count; ?> application<?php echo $count > 1 ? 's' : ''; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>No applications received yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="overview-section">
                <h2>Quick Actions</h2>
                <div class="quick-actions">
                    <a href="post-new.php?post_type=jobs" class="button button-primary">Add New Job</a>
                    <a href="edit.php?post_type=jobs" class="button">Manage Jobs</a>
                    <a href="edit.php?post_type=jobs&post_status=draft" class="button">View Drafts</a>
                    <a href="edit.php?post_type=jobs&post_status=publish" class="button">View Published</a>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .jobs-overview-dashboard {
        margin-top: 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        font-size: 32px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f0f0;
        border-radius: 50%;
    }
    
    .stat-content h3 {
        margin: 0;
        font-size: 28px;
        font-weight: bold;
        color: #2d3748;
    }
    
    .stat-content p {
        margin: 5px 0 0 0;
        color: #666;
        font-size: 14px;
    }
    
    .overview-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .overview-section h2 {
        margin-top: 0;
        color: #2d3748;
        border-bottom: 2px solid #4a90e2;
        padding-bottom: 10px;
    }
    
    .applications-table {
        overflow-x: auto;
    }
    
    .applications-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .applications-table th,
    .applications-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .applications-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #2d3748;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-reviewed {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-shortlisted {
        background: #d4edda;
        color: #155724;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .job-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .job-stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #4a90e2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .job-title {
        font-weight: 500;
        color: #2d3748;
    }
    
    .job-count {
        background: #4a90e2;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .quick-actions .button {
        margin-right: 10px;
        margin-bottom: 10px;
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            flex-direction: column;
        }
        
        .quick-actions .button {
            width: 100%;
            margin-right: 0;
        }
    }
    </style>
    <?php
}
?>

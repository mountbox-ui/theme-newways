<?php
/**
 * Jobs Functionality for Theme
 * Add this code to your theme's functions.php file
 * 
 * INSTRUCTIONS:
 * 1. Go to WordPress Admin → Appearance → Theme Editor
 * 2. Select functions.php
 * 3. Add this code at the end of the file
 * 4. Save the file
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Create Jobs Post Type
function tov_create_jobs_post_type() {
    $labels = array(
        'name' => 'Careers',
        'singular_name' => 'Career',
        'menu_name' => 'Careers',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Career',
        'edit_item' => 'Edit Career',
        'new_item' => 'New Career',
        'view_item' => 'View Career',
        'search_items' => 'Search Careers',
        'not_found' => 'No careers found',
        'not_found_in_trash' => 'No careers found in Trash',
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
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'rewrite' => array('slug' => 'careers', 'with_front' => false),
        'query_var' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'exclude_from_search' => false,
    );

    register_post_type('jobs', $args);
}
add_action('init', 'tov_create_jobs_post_type', 10);

// Redirect career post type to jobs (for backward compatibility)
function tov_redirect_career_to_jobs() {
    if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'career') {
        $url = admin_url('edit.php?post_type=jobs');
        wp_redirect($url);
        exit;
    }
}
add_action('admin_init', 'tov_redirect_career_to_jobs', 1);

// Flush rewrite rules on theme activation
function tov_flush_jobs_rewrite_rules_on_activation() {
    tov_create_jobs_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'tov_flush_jobs_rewrite_rules_on_activation');

// Enable template selection for jobs post type
function tov_add_jobs_template_support() {
    add_post_type_support('jobs', 'page-attributes');
}
add_action('init', 'tov_add_jobs_template_support');

// Add template dropdown for jobs post type
function tov_add_jobs_template_meta_box() {
    if (!post_type_exists('jobs')) {
        return;
    }
    
    add_meta_box(
        'jobs_template_meta_box',
        'Template',
        'tov_jobs_template_meta_box_callback',
        'jobs',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'tov_add_jobs_template_meta_box');

// Template meta box callback
function tov_jobs_template_meta_box_callback($post) {
    if ($post->post_type !== 'jobs') {
        return;
    }
    
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    $templates = array(
        '' => 'Default Template (single-jobs.php)',
        'jobs/single-jobs.php' => 'Single Job Template',
    );
    
    wp_nonce_field('jobs_template_nonce', 'jobs_template_nonce_field');
    ?>
    <label for="page_template"><?php _e('Template'); ?></label>
    <select name="page_template" id="page_template">
        <?php foreach ($templates as $key => $label) : ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($template, $key); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php _e('Select a template for this job post.'); ?></p>
    <?php
}

// Save template selection
function tov_save_jobs_template($post_id) {
    if (get_post_type($post_id) !== 'jobs') {
        return;
    }
    
    if (!isset($_POST['jobs_template_nonce_field']) || !wp_verify_nonce($_POST['jobs_template_nonce_field'], 'jobs_template_nonce')) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['page_template'])) {
        update_post_meta($post_id, '_wp_page_template', sanitize_text_field($_POST['page_template']));
    }
}
add_action('save_post', 'tov_save_jobs_template');

// Ensure single-jobs.php template is used for jobs post type
function tov_jobs_template_include($template) {
    if (is_singular('jobs')) {
        // First check in jobs folder
        $jobs_template = get_template_directory() . '/jobs/single-jobs.php';
        if (file_exists($jobs_template)) {
            return $jobs_template;
        }
        // Fallback to root theme directory
        $custom_template = locate_template('single-jobs.php');
        if ($custom_template) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'tov_jobs_template_include', 99);

// Auto-flush rewrite rules for jobs post type
function auto_flush_jobs_rewrite_rules() {
    if (get_option('jobs_rewrite_rules_flushed') !== 'yes') {
        flush_rewrite_rules();
        update_option('jobs_rewrite_rules_flushed', 'yes');
        update_option('jobs_flush_rewrite_rules', 'no');
    }
}
add_action('init', 'auto_flush_jobs_rewrite_rules', 20);

// Flush rewrite rules when needed
function tov_flush_jobs_rewrite_rules() {
    tov_create_jobs_post_type();
    flush_rewrite_rules();
}

// Add admin notice to flush rewrite rules if needed
function jobs_rewrite_rules_notice() {
    if (get_option('jobs_flush_rewrite_rules') === 'yes') {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>Careers System:</strong> Please go to <a href="' . admin_url('options-permalink.php') . '">Settings → Permalinks</a> and click "Save Changes" to update the URL structure for career pages.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'jobs_rewrite_rules_notice');

// Check if rewrite rules need flushing
function check_jobs_rewrite_rules() {
    if (!get_option('jobs_rewrite_rules_flushed')) {
        update_option('jobs_flush_rewrite_rules', 'yes');
    }
}
add_action('init', 'check_jobs_rewrite_rules');

// Add meta box for job details
function tov_add_jobs_meta_box() {
    // Only add meta boxes if the post type exists
    if (!post_type_exists('jobs')) {
        return;
    }
    
    add_meta_box(
        'jobs_details',
        'Job Details',
        'tov_jobs_meta_box_callback',
        'jobs',
        'normal',
        'high'
    );
    
    add_meta_box(
        'jobs_dynamic_options',
        'Dynamic Options',
        'tov_jobs_dynamic_options_callback',
        'jobs',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'tov_add_jobs_meta_box');

// Meta box callback
function tov_jobs_meta_box_callback($post) {
    wp_nonce_field('jobs_meta_box', 'jobs_meta_box_nonce');
    
    $category = get_post_meta($post->ID, '_job_category', true);
    $job_type = get_post_meta($post->ID, '_job_type', true);
    $location = get_post_meta($post->ID, '_job_location', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="job_category">Category</label></th>
            <td>
                <select name="job_category" id="job_category" style="width: 100%;">
                    <option value="">Select Category</option>
                    <?php
                    $categories = get_option('job_categories', array('Technology', 'Marketing', 'Sales', 'Finance', 'Human Resources', 'Operations', 'Design', 'Customer Service', 'Other'));
                    foreach($categories as $cat) {
                        echo '<option value="' . esc_attr($cat) . '" ' . selected($category, $cat, false) . '>' . esc_html($cat) . '</option>';
                    }
                    ?>
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
                <select name="job_location" id="job_location" style="width: 100%;">
                    <option value="">Select Location</option>
                    <?php
                    $locations = get_option('job_locations', array('Remote', 'New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ', 'Philadelphia, PA', 'San Antonio, TX', 'San Diego, CA', 'Dallas, TX', 'San Jose, CA', 'Austin, TX', 'Jacksonville, FL', 'Fort Worth, TX', 'Columbus, OH', 'Charlotte, NC', 'San Francisco, CA', 'Indianapolis, IN', 'Seattle, WA', 'Denver, CO'));
                    foreach($locations as $loc) {
                        echo '<option value="' . esc_attr($loc) . '" ' . selected($location, $loc, false) . '>' . esc_html($loc) . '</option>';
                    }
                    ?>
                </select>
                <p class="description">Add new locations in the Dynamic Options panel.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Dynamic options meta box callback
function tov_jobs_dynamic_options_callback($post) {
    wp_nonce_field('jobs_dynamic_options', 'jobs_dynamic_options_nonce');
    
    $categories = get_option('job_categories', array('Technology', 'Marketing', 'Sales', 'Finance', 'Human Resources', 'Operations', 'Design', 'Customer Service', 'Other'));
    $locations = get_option('job_locations', array('Remote', 'New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ', 'Philadelphia, PA', 'San Antonio, TX', 'San Diego, CA', 'Dallas, TX', 'San Jose, CA', 'Austin, TX', 'Jacksonville, FL', 'Fort Worth, TX', 'Columbus, OH', 'Charlotte, NC', 'San Francisco, CA', 'Indianapolis, IN', 'Seattle, WA', 'Denver, CO'));
    ?>
    <div style="margin-bottom: 20px;">
        <h4>Manage Categories</h4>
        <div id="categories-list">
            <?php foreach($categories as $index => $category): ?>
                <div class="dynamic-option-item" style="margin-bottom: 5px;">
                    <input type="text" name="job_categories[]" value="<?php echo esc_attr($category); ?>" style="width: 80%;" />
                    <button type="button" class="button remove-option" style="margin-left: 5px;">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-category" class="button">Add Category</button>
    </div>
    
    <div>
        <h4>Manage Locations</h4>
        <div id="locations-list">
            <?php foreach($locations as $index => $location): ?>
                <div class="dynamic-option-item" style="margin-bottom: 5px;">
                    <input type="text" name="job_locations[]" value="<?php echo esc_attr($location); ?>" style="width: 80%;" />
                    <button type="button" class="button remove-option" style="margin-left: 5px;">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-location" class="button">Add Location</button>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add category
        $('#add-category').click(function() {
            $('#categories-list').append('<div class="dynamic-option-item" style="margin-bottom: 5px;"><input type="text" name="job_categories[]" value="" style="width: 80%;" /><button type="button" class="button remove-option" style="margin-left: 5px;">Remove</button></div>');
        });
        
        // Add location
        $('#add-location').click(function() {
            $('#locations-list').append('<div class="dynamic-option-item" style="margin-bottom: 5px;"><input type="text" name="job_locations[]" value="" style="width: 80%;" /><button type="button" class="button remove-option" style="margin-left: 5px;">Remove</button></div>');
        });
        
        // Remove option
        $(document).on('click', '.remove-option', function() {
            $(this).parent().remove();
        });
    });
    </script>
    <?php
}

// Save meta fields
function tov_save_jobs_meta_fields($post_id) {
    // Only save for jobs post type
    if (get_post_type($post_id) !== 'jobs') {
        return;
    }
    
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
    
    
    // Save dynamic options
    if (isset($_POST['jobs_dynamic_options_nonce']) && wp_verify_nonce($_POST['jobs_dynamic_options_nonce'], 'jobs_dynamic_options')) {
        if (isset($_POST['job_categories'])) {
            $categories = array_filter(array_map('sanitize_text_field', $_POST['job_categories']));
            update_option('job_categories', $categories);
        }
        
        if (isset($_POST['job_locations'])) {
            $locations = array_filter(array_map('sanitize_text_field', $_POST['job_locations']));
            update_option('job_locations', $locations);
        }
    }
}
add_action('save_post', 'tov_save_jobs_meta_fields');

// Add custom columns
function tov_add_jobs_columns($columns) {
    // Only add columns if post type exists
    if (!post_type_exists('jobs')) {
        return $columns;
    }
    
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['job_category'] = 'Category';
    $new_columns['job_type'] = 'Job Type';
    $new_columns['job_location'] = 'Location';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_jobs_posts_columns', 'tov_add_jobs_columns');

// Populate custom columns
function tov_populate_jobs_columns($column, $post_id) {
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
    }
}
add_action('manage_jobs_posts_custom_column', 'tov_populate_jobs_columns', 10, 2);

// Add shortcode for displaying jobs
function jobs_display_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => '',
        'type' => '',
        'location' => '',
        'limit' => -1,
        'show_filters' => 'true'
    ), $atts);
    
    // Clear any stale cache before querying
    wp_cache_flush();
    
    // Build meta query with proper status filtering
    $meta_query = array();
    
    // Status filter - only show active jobs (exclude inactive ones)
    // Use a more explicit approach to ensure inactive jobs are excluded
    $meta_query[] = array(
        'relation' => 'AND',
        array(
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
    
    // Set relation to AND for all conditions
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
    
    // Manual filtering as backup - remove any inactive jobs that might have slipped through
    $filtered_posts = array();
    if ($jobs_query->posts) {
        foreach ($jobs_query->posts as $post) {
            $job_status = get_post_meta($post->ID, '_job_status', true);
            
            // Only include jobs that are not explicitly inactive
            if ($job_status !== 'inactive') {
                $filtered_posts[] = $post;
            }
        }
    }
    
    // Replace the query posts with our filtered results
    $jobs_query->posts = $filtered_posts;
    $jobs_query->post_count = count($filtered_posts);
    
    ob_start();
    
    // Add cache busting parameter
    $cache_buster = time();
    ?>
    <div class="jobs-listing-container" data-cache-buster="<?php echo $cache_buster; ?>">
        
        <?php if ($atts['show_filters'] === 'true') : ?>
        <div class="jobs-filters">
            <?php 
            // Build unique filter values from the posts actually returned
            $used_categories = array();
            $used_job_types = array();
            $used_locations = array();
            foreach ($jobs_query->posts as $p) {
                $c = get_post_meta($p->ID, '_job_category', true);
                $t = get_post_meta($p->ID, '_job_type', true);
                $l = get_post_meta($p->ID, '_job_location', true);
                if ($c && !in_array($c, $used_categories, true)) { $used_categories[] = $c; }
                if ($t && !in_array($t, $used_job_types, true)) { $used_job_types[] = $t; }
                if ($l && !in_array($l, $used_locations, true)) { $used_locations[] = $l; }
            }
            sort($used_categories);
            sort($used_job_types);
            sort($used_locations);
            ?>
            <select id="category-filter" class="job-filter">
                <option value="">All Job Category</option>
                <?php foreach ($used_categories as $label) : $val = sanitize_title($label); ?>
                    <option value="<?php echo esc_attr($val); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="type-filter" class="job-filter">
                <option value="">All Job Type</option>
                <?php foreach ($used_job_types as $label) : ?>
                    <option value="<?php echo esc_attr($label); ?>"><?php echo esc_html(ucfirst(str_replace('-', ' ', $label))); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="location-filter" class="job-filter">
                <option value="">All Job Location</option>
                <?php foreach ($used_locations as $label) : ?>
                    <option value="<?php echo esc_attr($label); ?>"><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
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
                    <?php $category_slug = $category ? sanitize_title($category) : ''; ?>
                    <div class="job-card" 
                         data-category="<?php echo esc_attr($category_slug); ?>"
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
        justify-content: flex-start;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px 0;
    }
    
    .job-filter {
        width: 191px;
        height: 48px;
        padding: 12px 16px;
        border: 1px solid #d0d0d0;
        border-radius: 6px;
        background: white;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 16px;
        font-weight: 400;
        color: #333;
        cursor: pointer;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
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
        /*border: 1px solid #e0e0e0;*/
        border: 1px solid rgba(60, 194, 217, 0.60);
        border-radius: 8px;
        background: #F3FAFB;
        transition: all 0.2s ease;
        box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.05);
        cursor: pointer;
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
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0 0 8px 0;
        font-size: 18px;
        font-weight: 600;
        color: #00455E;
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
        font-size: 16px;
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
            align-items: stretch;
        }
        
        .job-filter {
            width: 100%;
            max-width: 100%;
            min-width: 0;
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
    
    /* Extra small mobile devices */
    @media (max-width: 480px) {
        .jobs-listing-container {
            padding: 10px;
        }
        
        .jobs-filters {
            padding: 15px 0;
            flex-direction: column;
            gap: 8px;
        }
        
        .job-filter {
            height: 44px;
            font-size: 14px;
            width: 100%;
            max-width: 100%;
        }
        
        .job-card {
            padding: 15px;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        
        .job-title {
            font-size: 16px;
            line-height: 1.3;
        }
        
        .job-meta {
            font-size: 12px;
            margin-bottom: 8px;
        }
        
        .job-actions {
            width: 100%;
            margin-left: 0;
        }
        
        .more-details-link {
            padding: 10px;
            font-size: 14px;
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Tablet landscape */
    @media (max-width: 1024px) and (min-width: 769px) {
        .jobs-listing-container {
            padding: 20px;
        }
        
        .jobs-filters {
            gap: 15px;
        }
        
        .job-filter {
            width: 180px;
        }
        
        .job-card {
            padding: 20px;
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
        
        // Add click handler to job cards
        jobCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on the "More Details" link
                if (e.target.classList.contains('more-details-link') || e.target.closest('.more-details-link')) {
                    return;
                }
                
                // Get the job URL from the "More Details" link
                const moreDetailsLink = card.querySelector('.more-details-link');
                if (moreDetailsLink) {
                    window.location.href = moreDetailsLink.href;
                }
            });
        });
    });
    </script>
    <?php
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('jobs_listing', 'jobs_display_shortcode');

// Handle job application form submission
function tov_handle_job_application() {
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
        
        $admin_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $applicant_email
        );
        
        $email_sent = wp_mail($admin_email, $admin_subject, $admin_message, $admin_headers);
        
        // Debug email sending
        if (!$email_sent) {
            error_log('Job application email failed to send to: ' . $admin_email);
        }
        
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
        
        $applicant_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        $applicant_email_sent = wp_mail($applicant_email, $applicant_subject, $applicant_message, $applicant_headers);
        
        // Debug applicant email
        if (!$applicant_email_sent) {
            error_log('Job application confirmation email failed to send to: ' . $applicant_email);
        }
        
        // Store application data
        $applications = get_option('job_applications', array());
        $new_application = array(
            'id' => uniqid(),
            'job_id' => $job_id,
            'job_title' => get_the_title($job_id),
            'name' => $applicant_name,
            'email' => $applicant_email,
            'phone' => $applicant_phone,
            'location' => $applicant_location,
            'experience' => $applicant_experience,
            'resume_url' => isset($resume_url) ? $resume_url : '',
            'cover_letter' => $cover_letter,
            'date' => current_time('Y-m-d H:i:s'),
            'status' => 'new'
        );
        
        $applications[] = $new_application;
        update_option('job_applications', $applications);
        
        // Show success modal instead of popup
        add_action('wp_footer', function() {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof showThankYouModal === "function") {
                        showThankYouModal();
                    }
                });
            </script>';
        });
    }
}
add_action('init', 'tov_handle_job_application');

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
        .jobs-meta-box input[type="text"],
        .jobs-meta-box textarea {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    <?php
}
add_action('admin_head', 'jobs_admin_styles');

// Add Overview submenu to Jobs
function tov_add_jobs_overview_submenu() {
    // Only add menu if post type exists
    if (!post_type_exists('jobs')) {
        return;
    }
    
    add_submenu_page(
        'edit.php?post_type=jobs',
        'Overview',
        'Overview',
        'manage_options',
        'jobs-overview',
        'tov_jobs_overview_page'
    );
}
add_action('admin_menu', 'tov_add_jobs_overview_submenu');

// Add Dynamic Options admin page
function tov_add_jobs_dynamic_options_page() {
    // Only add menu if post type exists
    if (!post_type_exists('jobs')) {
        return;
    }
    
    add_submenu_page(
        'edit.php?post_type=jobs',
        'Dynamic Options',
        'Dynamic Options',
        'manage_options',
        'jobs-dynamic-options',
        'tov_jobs_dynamic_options_page_callback'
    );
}
add_action('admin_menu', 'tov_add_jobs_dynamic_options_page');


// Dynamic Options page callback
function tov_jobs_dynamic_options_page_callback() {
    if (isset($_POST['submit_dynamic_options'])) {
        if (wp_verify_nonce($_POST['dynamic_options_nonce'], 'save_dynamic_options')) {
            // Handle categories - process submitted data or set empty array
            $categories = array();
            if (isset($_POST['job_categories']) && is_array($_POST['job_categories'])) {
                $categories = array_filter(array_map('sanitize_text_field', $_POST['job_categories']));
            }
            update_option('job_categories', $categories);
            
            // Handle locations - process submitted data or set empty array
            $locations = array();
            if (isset($_POST['job_locations']) && is_array($_POST['job_locations'])) {
                $locations = array_filter(array_map('sanitize_text_field', $_POST['job_locations']));
            }
            update_option('job_locations', $locations);
            
            // Clear caches to ensure changes are reflected immediately
            wp_cache_delete('job_categories', 'options');
            wp_cache_delete('job_locations', 'options');
            wp_cache_flush();
            
            $category_count = count($categories);
            $location_count = count($locations);
            echo '<div class="notice notice-success"><p>Dynamic options saved successfully! Categories: ' . $category_count . ', Locations: ' . $location_count . '</p></div>';
        }
    }
    
    $categories = get_option('job_categories', array('Technology', 'Marketing', 'Sales', 'Finance', 'Human Resources', 'Operations', 'Design', 'Customer Service', 'Other'));
    $locations = get_option('job_locations', array('Remote', 'New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Houston, TX', 'Phoenix, AZ', 'Philadelphia, PA', 'San Antonio, TX', 'San Diego, CA', 'Dallas, TX', 'San Jose, CA', 'Austin, TX', 'Jacksonville, FL', 'Fort Worth, TX', 'Columbus, OH', 'Charlotte, NC', 'San Francisco, CA', 'Indianapolis, IN', 'Seattle, WA', 'Denver, CO'));
    ?>
    <div class="wrap">
        <h1>Dynamic Options Management</h1>
        <p>Manage the available categories and locations for job postings.</p>
        
        
        <form method="post" action="" id="dynamic-options-form">
            <?php wp_nonce_field('save_dynamic_options', 'dynamic_options_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Job Categories</th>
                    <td>
                        <div id="categories-management">
                            <?php foreach($categories as $index => $category): ?>
                                <div class="dynamic-option-row" style="margin-bottom: 10px;">
                                    <input type="text" name="job_categories[]" value="<?php echo esc_attr($category); ?>" style="width: 300px;" />
                                    <button type="button" class="button remove-row" style="margin-left: 10px;">Remove</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="add-category-row" class="button">Add Category</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Job Locations</th>
                    <td>
                        <div id="locations-management">
                            <?php foreach($locations as $index => $location): ?>
                                <div class="dynamic-option-row" style="margin-bottom: 10px;">
                                    <input type="text" name="job_locations[]" value="<?php echo esc_attr($location); ?>" style="width: 300px;" />
                                    <button type="button" class="button remove-row" style="margin-left: 10px;">Remove</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="add-location-row" class="button">Add Location</button>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save Dynamic Options', 'primary', 'submit_dynamic_options'); ?>
        </form>
        
        
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add category row
        $('#add-category-row').click(function() {
            $('#categories-management').append('<div class="dynamic-option-row" style="margin-bottom: 10px;"><input type="text" name="job_categories[]" value="" style="width: 300px;" /><button type="button" class="button remove-row" style="margin-left: 10px;">Remove</button></div>');
        });
        
        // Add location row
        $('#add-location-row').click(function() {
            $('#locations-management').append('<div class="dynamic-option-row" style="margin-bottom: 10px;"><input type="text" name="job_locations[]" value="" style="width: 300px;" /><button type="button" class="button remove-row" style="margin-left: 10px;">Remove</button></div>');
        });
        
        // Remove row
        $(document).on('click', '.remove-row', function() {
            $(this).parent().remove();
        });
        
        // Handle form submission to ensure proper data is sent
        $('#dynamic-options-form').on('submit', function(e) {
            // Ensure empty values are not submitted
            var categoryInputs = $('#categories-management input[name="job_categories[]"]');
            var locationInputs = $('#locations-management input[name="job_locations[]"]');
            
            categoryInputs.each(function() {
                if ($(this).val().trim() === '') {
                    $(this).remove();
                }
            });
            
            locationInputs.each(function() {
                if ($(this).val().trim() === '') {
                    $(this).remove();
                }
            });
        });
        
    });
    </script>
    <?php
}

// Overview page content
function tov_jobs_overview_page() {
    // Get statistics
    $total_jobs = wp_count_posts('jobs');
    $published_jobs = $total_jobs->publish;
    
    // Get applications data
    $applications = get_option('job_applications', array());
    $total_applications = count($applications);
    
    // Count recent applications (last 30 days)
    $recent_applications = 0;
    $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
    
    foreach ($applications as $application) {
        if (isset($application['date']) && $application['date'] >= $thirty_days_ago) {
            $recent_applications++;
        }
    }
    
    // Get recent applicants (last 10)
    $recent_applicants = array_slice(array_reverse($applications), 0, 10);
    
    // Get all jobs for listing (including inactive for admin view)
    $all_jobs = get_posts(array(
        'post_type' => 'jobs',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    ?>
    
    <div class="wrap">
        <h1>Careers Overview Dashboard</h1>
        
        
        <!-- Statistics Cards -->
        <div class="jobs-overview-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $published_jobs; ?></div>
                <div class="stat-label">Open Positions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $recent_applications; ?></div>
                <div class="stat-label">New Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_applications; ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="jobs-overview-section">
            <h2>Recent Applications</h2>
            <?php if (!empty($recent_applicants)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_applicants as $application): ?>
                            <tr>
                                <td><strong><?php echo esc_html($application['name']); ?></strong></td>
                                <td><?php echo esc_html($application['job_title']); ?></td>
                                <td><?php echo esc_html($application['email']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($application['date'])); ?></td>
                                <td>
                                    <?php if (!empty($application['resume_url'])): ?>
                                        <a href="<?php echo esc_url($application['resume_url']); ?>" target="_blank" class="button button-small">View Resume</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No applications yet.</p>
            <?php endif; ?>
        </div>
        
        <!-- Posted Jobs -->
        <div class="jobs-overview-section">
            <h2>Posted Jobs</h2>
            <?php if (!empty($all_jobs)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Date Posted</th>
                            <th>Applications</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_jobs as $job): 
                            $category = get_post_meta($job->ID, '_job_category', true);
                            $job_type = get_post_meta($job->ID, '_job_type', true);
                            $location = get_post_meta($job->ID, '_job_location', true);
                            $job_status = get_post_meta($job->ID, '_job_status', true);
                            $job_status = $job_status ? $job_status : 'active'; // Default to active
                            
                            // Count applications for this job
                            $job_applications = 0;
                            foreach ($applications as $application) {
                                if (isset($application['job_id']) && $application['job_id'] == $job->ID) {
                                    $job_applications++;
                                }
                            }
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($job->post_title); ?></strong></td>
                                <td><?php echo esc_html($category); ?></td>
                                <td><?php echo esc_html(ucfirst(str_replace('-', ' ', $job_type))); ?></td>
                                <td><?php echo esc_html($location); ?></td>
                                <?php 
                                $activation_date = get_post_meta($job->ID, '_job_activation_date', true);
                                $display_date = $activation_date ? $activation_date : $job->post_date;
                                ?>
                                <td><?php echo date('M j, Y', strtotime($display_date)); ?></td>
                                <td><span class="application-count"><?php echo $job_applications; ?></span></td>
                                <td>
                                    <a href="<?php echo get_edit_post_link($job->ID); ?>" class="button button-small">Edit</a>
                                    <a href="<?php echo get_permalink($job->ID); ?>" target="_blank" class="button button-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No jobs posted yet. <a href="<?php echo admin_url('post-new.php?post_type=jobs'); ?>">Create your first job</a></p>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="jobs-overview-section">
            <h2>Quick Actions</h2>
            <p>
                <a href="<?php echo admin_url('post-new.php?post_type=jobs'); ?>" class="button button-primary">Add New Job</a>
                <a href="<?php echo admin_url('edit.php?post_type=jobs'); ?>" class="button">Manage Jobs</a>
                <a href="<?php echo admin_url('edit.php?post_type=jobs&page=jobs-overview'); ?>" class="button">Refresh Overview</a>
            </p>
        </div>
        
    </div>
    
    <style>
    .jobs-overview-stats {
        display: flex;
        gap: 20px;
        margin: 20px 0;
    }
    
    .stat-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        flex: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: bold;
        color: #2c5aa0;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 14px;
    }
    
    .jobs-overview-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .jobs-overview-section h2 {
        margin-top: 0;
        color: #2c5aa0;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 10px;
    }
    
    .application-count {
        background: #2c5aa0;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .wp-list-table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    .wp-list-table td {
        vertical-align: middle;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        display: inline-block;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .jobs-overview-stats {
            flex-direction: column;
        }
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status toggle functionality removed
    });
    
    </script>
    <?php
}


// AJAX handler for updating job status
function handle_job_status_update() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'update_job_status')) {
        wp_send_json_error('Security check failed');
    }

    // Check user permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
    }

    $job_id = intval($_POST['job_id']);
    $status = sanitize_text_field($_POST['status']);

    // Validate status
    if (!in_array($status, ['active', 'inactive'])) {
        wp_send_json_error('Invalid status');
    }

    // Validate job ID
    if (!$job_id || !get_post($job_id)) {
        wp_send_json_error('Invalid job ID');
    }

    // Update the job status meta
    $updated = update_post_meta($job_id, '_job_status', $status);

    // If job is being activated, update the activation date
    if ($status === 'active') {
        update_post_meta($job_id, '_job_activation_date', current_time('mysql'));
    }
    
    // Comprehensive cache clearing when status changes
    clear_all_job_caches($job_id);

    if ($updated !== false) {
        wp_send_json_success(array(
            'message' => 'Job status updated successfully and cache cleared',
            'status' => $status,
            'job_id' => $job_id
        ));
    } else {
        wp_send_json_error('Failed to update job status');
    }
}

// Comprehensive cache clearing function
function clear_all_job_caches($job_id = null) {
    // Clear WordPress object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear post-specific cache if job ID provided
    if ($job_id) {
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete($job_id, 'posts');
            wp_cache_delete($job_id, 'post_meta');
        }
        clean_post_cache($job_id);
    }
    
    // Clear post meta cache
    if (function_exists('wp_cache_delete_group')) {
        wp_cache_delete_group('posts');
        wp_cache_delete_group('post_meta');
        wp_cache_delete_group('terms');
    }
    
    // Clear transients
    delete_transient('jobs_active_count');
    delete_transient('jobs_listing_cache');
    delete_transient('jobs_overview_cache');
    
    // Clear popular caching plugins
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all(); // W3 Total Cache
    }
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache(); // WP Super Cache
    }
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain(); // WP Rocket
    }
    if (function_exists('litespeed_purge_all')) {
        litespeed_purge_all(); // LiteSpeed Cache
    }
    if (function_exists('cache_enabler_clear_complete_cache')) {
        cache_enabler_clear_complete_cache(); // Cache Enabler
    }
    if (function_exists('wp_fastest_cache_purge_all')) {
        wp_fastest_cache_purge_all(); // WP Fastest Cache
    }
    
    // Clear any custom caches
    global $wp_object_cache;
    if (is_object($wp_object_cache)) {
        $wp_object_cache->flush();
    }
    
    // Force garbage collection
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
    
    // Add cache busting header
    if (!headers_sent()) {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}

// Hook the AJAX handler
add_action('wp_ajax_update_job_status', 'handle_job_status_update');

// Clear cache when job post is updated
function clear_job_cache_on_update($post_id, $post, $update) {
    if ($post->post_type === 'jobs') {
        clear_all_job_caches($post_id);
    }
}
add_action('save_post', 'clear_job_cache_on_update', 10, 3);

// Clear cache when job meta is updated
function clear_job_cache_on_meta_update($meta_id, $post_id, $meta_key, $meta_value) {
    if (get_post_type($post_id) === 'jobs' && $meta_key === '_job_status') {
        clear_all_job_caches($post_id);
    }
}
add_action('updated_post_meta', 'clear_job_cache_on_meta_update', 10, 4);

// Set default status for existing jobs without status
function set_default_job_status() {
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

// Run this once on admin init to set default status for existing jobs
add_action('admin_init', function() {
    if (get_option('jobs_status_default_set') !== 'yes') {
        set_default_job_status();
        update_option('jobs_status_default_set', 'yes');
    }
});



?>

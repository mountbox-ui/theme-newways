<?php
/**
 * Comprehensive Fix for Jobs 404 Error
 * Add this code to your theme-functions.php file
 * 
 * INSTRUCTIONS:
 * 1. Add this code to the end of your theme-functions.php file
 * 2. Save the file
 * 3. Go to Settings → Permalinks → Save Changes
 * 4. Clear any caches
 */

// Enhanced Jobs Post Type Registration with proper rewrite rules
function create_jobs_post_type_enhanced() {
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
        'rewrite' => array(
            'slug' => 'jobs',
            'with_front' => false,
            'hierarchical' => false
        ),
        'query_var' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
    );

    register_post_type('jobs', $args);
}

// Remove the old registration and add the enhanced one
remove_action('init', 'create_jobs_post_type');
add_action('init', 'create_jobs_post_type_enhanced');

// Add rewrite rules for jobs
function add_jobs_rewrite_rules() {
    add_rewrite_rule('^jobs/([^/]+)/?$', 'index.php?post_type=jobs&name=$matches[1]', 'top');
    add_rewrite_rule('^jobs/?$', 'index.php?post_type=jobs', 'top');
}
add_action('init', 'add_jobs_rewrite_rules');

// Fix 404 issues by ensuring proper query handling
function handle_jobs_404_fix($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Check if this is a jobs request
        if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'jobs') {
            $query->set('post_type', 'jobs');
            $query->set('post_status', 'publish');
        }
        
        // Handle jobs archive
        if (is_home() && isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'jobs') {
            $query->set('post_type', 'jobs');
        }
    }
}
add_action('pre_get_posts', 'handle_jobs_404_fix');

// Custom single job template
function custom_jobs_single_template($template) {
    if (is_singular('jobs')) {
        $custom_template = get_template_directory() . '/single-jobs.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('single_template', 'custom_jobs_single_template');

// Flush permalinks on theme activation
function flush_jobs_permalinks() {
    create_jobs_post_type_enhanced();
    add_jobs_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_jobs_permalinks');

// Force flush permalinks on admin init
function force_flush_permalinks() {
    if (get_option('jobs_permalinks_flushed') !== 'yes') {
        flush_rewrite_rules();
        update_option('jobs_permalinks_flushed', 'yes');
    }
}
add_action('admin_init', 'force_flush_permalinks');

// Add custom query vars
function add_jobs_query_vars($vars) {
    $vars[] = 'post_type';
    return $vars;
}
add_filter('query_vars', 'add_jobs_query_vars');

// Debug function to check if jobs are being found
function debug_jobs_query() {
    if (isset($_GET['debug_jobs']) && current_user_can('manage_options')) {
        $jobs = get_posts(array(
            'post_type' => 'jobs',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        echo '<pre>Jobs found: ' . count($jobs) . '</pre>';
        foreach ($jobs as $job) {
            echo '<pre>Job: ' . $job->post_title . ' - ' . get_permalink($job->ID) . '</pre>';
        }
    }
}
add_action('wp_footer', 'debug_jobs_query');
?>

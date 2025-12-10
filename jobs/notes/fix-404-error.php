<?php
/**
 * Fix 404 Error for Jobs Pages
 * Add this code to your theme-functions.php file
 * 
 * INSTRUCTIONS:
 * 1. Add this code to the end of your theme-functions.php file
 * 2. Save the file
 * 3. Go to Settings → Permalinks → Save Changes (to flush permalinks)
 */

// Fix 404 issues by ensuring proper query handling
function handle_jobs_404_fix($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'jobs') {
            $query->set('post_type', 'jobs');
        }
    }
}
add_action('pre_get_posts', 'handle_jobs_404_fix');

// Add rewrite rules for jobs
function add_jobs_rewrite_rules() {
    add_rewrite_rule('^jobs/([^/]+)/?$', 'index.php?post_type=jobs&name=$matches[1]', 'top');
}
add_action('init', 'add_jobs_rewrite_rules');

// Flush permalinks on theme activation
function flush_jobs_permalinks() {
    add_jobs_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_jobs_permalinks');

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
?>

<?php
/**
 * Neways Theme functions and definitions - Minimal Version
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Theme version
define('NEWAYS_THEME_VERSION', '1.0.0');

/**
 * Theme setup
 */
function neways_theme_setup() {
    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'neways-theme'),
        'footer'  => esc_html__('Footer Menu', 'neways-theme'),
        'location_pages' => esc_html__('Location Pages', 'neways-theme'),
    ));

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'style',
        'script',
    ));
}
add_action('after_setup_theme', 'neways_theme_setup');
/**
 * Enqueue scripts and styles
 */
function neways_theme_scripts() {
    // Main stylesheet
    wp_enqueue_style(
        'neways-theme-style',
        get_template_directory_uri() . '/assets/css/neways.css',
        array(),
        NEWAYS_THEME_VERSION
    );

    // Main JavaScript file
    wp_enqueue_script(
        'neways-theme-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        NEWAYS_THEME_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'neways_theme_scripts');

/**
 * Ensure global custom overrides load after all theme/styles
 */

/**
 * Add custom classes to navigation menu
 */
function neways_theme_nav_menu_css_class($classes, $item, $args) {
    if ($args->theme_location == 'primary') {
        $classes[] = 'text-white hover:text-navy-200 px-3 py-2 text-sm font-medium transition-colors duration-200';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'neways_theme_nav_menu_css_class', 10, 3);


/**
 * Load Shortcodes
 */
require_once get_template_directory() . '/shortcodes/loader.php';

// Force clear caches for blog updates
function neways_force_clear_blog_cache() {
    if (current_user_can('manage_options') && isset($_GET['clear_blog_cache'])) {
        // Clear WordPress object cache
        wp_cache_flush();
        
        // Clear any plugin caches
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // Clear rewrite rules
        flush_rewrite_rules();
        
        wp_redirect(remove_query_arg('clear_blog_cache'));
        exit;
    }
}
add_action('init', 'neways_force_clear_blog_cache');

/**
 * Load Custom Post Types
 */
require_once get_template_directory() . '/inc/post-types/events.php';
require_once get_template_directory() . '/inc/post-types/news.php';
require_once get_template_directory() . '/inc/post-types/news-highlight-admin.php';
require_once get_template_directory() . '/inc/post-types/blog.php';
require_once get_template_directory() . '/inc/post-types/team.php';

/**
 * Load Gallery Management System
 */
require_once get_template_directory() . '/inc/gallery/gallery-management.php';
require_once get_template_directory() . '/shortcodes/gallery-section.php';

/**
 * Load FAQ Shortcode System
 */
require_once get_template_directory() . '/shortcodes/faq-section.php';

/**
 * Load ACF Fields for Awards Template
 */
require_once get_template_directory() . '/inc/acf/awards-fields.php';

/**
 * Load ACF Fields for Events
 */
require_once get_template_directory() . '/inc/acf/events-fields.php';

/**
 * Load Simple Awards Height Control
 */
require_once get_template_directory() . '/inc/acf/simple-awards-height.php';

/**
 * Load Direct Height Field
 */
require_once get_template_directory() . '/inc/acf/direct-height-field.php';

/**
 * Load Manual Height Control
 */
require_once get_template_directory() . '/inc/acf/manual-height.php';

/**
 * Load Services Admin Menu
 */
require_once get_template_directory() . '/inc/post-types/services.php';
require_once get_template_directory() . '/inc/post-types/locations.php';


/**
 * Debug function to check ACF fields for events
 * Add ?debug_acf_fields=1 to any URL
 */
function neways_debug_acf_fields() {
    if (isset($_GET['debug_acf_fields']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 600px;">';
        echo '<h3>ACF Fields Debug</h3>';
        
        // Check if ACF is available
        if (function_exists('get_field')) {
            echo '<p><strong>✅ ACF is available</strong></p>';
            
            // Get first event to test
            $events = get_posts(array(
                'post_type' => 'event',
                'numberposts' => 1,
                'post_status' => 'publish'
            ));
            
            if (!empty($events)) {
                $event = $events[0];
                echo '<p><strong>Testing Event:</strong> ' . $event->post_title . ' (ID: ' . $event->ID . ')</p>';
                
                // Test each field
                $start_date = get_field('event_start_date', $event->ID);
                $end_date = get_field('event_end_date', $event->ID);
                $time = get_field('event_time', $event->ID);
                $location = get_field('event_location', $event->ID);
                
                echo '<p><strong>ACF Fields:</strong></p>';
                echo '<p>- Start Date: ' . ($start_date ? $start_date : 'EMPTY') . '</p>';
                echo '<p>- End Date: ' . ($end_date ? $end_date : 'EMPTY') . '</p>';
                echo '<p>- Time: ' . ($time ? $time : 'EMPTY') . '</p>';
                echo '<p>- Location: ' . ($location ? $location : 'EMPTY') . '</p>';
                
                // Test meta fields
                $meta_start_date = get_post_meta($event->ID, '_event_start_date', true);
                $meta_end_date = get_post_meta($event->ID, '_event_end_date', true);
                $meta_time = get_post_meta($event->ID, '_event_time', true);
                $meta_location = get_post_meta($event->ID, '_event_location', true);
                
                echo '<p><strong>Meta Fields:</strong></p>';
                echo '<p>- _event_start_date: ' . ($meta_start_date ? $meta_start_date : 'EMPTY') . '</p>';
                echo '<p>- _event_end_date: ' . ($meta_end_date ? $meta_end_date : 'EMPTY') . '</p>';
                echo '<p>- _event_time: ' . ($meta_time ? $meta_time : 'EMPTY') . '</p>';
                echo '<p>- _event_location: ' . ($meta_location ? $meta_location : 'EMPTY') . '</p>';
                
            } else {
                echo '<p><strong>❌ No events found</strong></p>';
            }
            
        } else {
            echo '<p><strong>❌ ACF is NOT available</strong></p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_acf_fields');

/**
 * Debug function to check if events system is working
 * Add ?debug_events_system=1 to any URL
 */
function neways_debug_events_system() {
    if (isset($_GET['debug_events_system']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 600px;">';
        echo '<h3>Events System Debug</h3>';
        
        // Check if event post type exists
        $post_type_obj = get_post_type_object('event');
        if ($post_type_obj) {
            echo '<p><strong>✅ Event post type is registered</strong></p>';
        } else {
            echo '<p><strong>❌ Event post type is NOT registered</strong></p>';
        }
        
        // Check if ACF is available
        if (function_exists('get_field')) {
            echo '<p><strong>✅ ACF is available</strong></p>';
        } else {
            echo '<p><strong>❌ ACF is NOT available</strong></p>';
        }
        
        // Check events
        $events = get_posts(array(
            'post_type' => 'event',
            'numberposts' => 5,
            'post_status' => 'publish'
        ));
        
        if (!empty($events)) {
            echo '<p><strong>Events Found:</strong></p>';
            foreach ($events as $event) {
                echo '<p>- ' . $event->post_title . ' (ID: ' . $event->ID . ')</p>';
                
                // Check ACF fields
                $start_date = get_field('event_start_date', $event->ID);
                $end_date = get_field('event_end_date', $event->ID);
                $location = get_field('event_location', $event->ID);
                
                echo '<p>  Start Date: ' . ($start_date ? $start_date : 'Not set') . '</p>';
                echo '<p>  End Date: ' . ($end_date ? $end_date : 'Not set') . '</p>';
                echo '<p>  Location: ' . ($location ? $location : 'Not set') . '</p>';
                echo '<p>  URL: <a href="' . get_permalink($event->ID) . '" target="_blank">' . get_permalink($event->ID) . '</a></p>';
            }
        } else {
            echo '<p><strong>❌ No events found</strong></p>';
        }
        
        // Check events page
        $events_page = get_page_by_path('events');
        if ($events_page) {
            echo '<p><strong>✅ Events Page Found:</strong> ' . $events_page->post_title . ' (ID: ' . $events_page->ID . ')</p>';
            echo '<p>URL: <a href="' . get_permalink($events_page->ID) . '" target="_blank">' . get_permalink($events_page->ID) . '</a></p>';
        } else {
            echo '<p><strong>❌ Events Page Not Found</strong></p>';
        }
        
        // Check if events-section shortcode exists
        if (shortcode_exists('events_section')) {
            echo '<p><strong>✅ Events section shortcode exists</strong></p>';
        } else {
            echo '<p><strong>❌ Events section shortcode does NOT exist</strong></p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_events_system');

/**
 * Debug function to check if blog post type is working
 * Add ?debug_blog_system=1 to any URL
 */
function neways_debug_blog_system() {
    if (isset($_GET['debug_blog_system']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 600px;">';
        echo '<h3>Blog System Debug</h3>';
        
        // Check if blog post type exists
        $post_type_obj = get_post_type_object('blog');
        if ($post_type_obj) {
            echo '<p><strong>✅ Blog post type is registered</strong></p>';
        } else {
            echo '<p><strong>❌ Blog post type is NOT registered</strong></p>';
        }
        
        // Check blog posts
        $blog_posts = get_posts(array(
            'post_type' => 'blog',
            'numberposts' => 3,
            'post_status' => 'publish'
        ));
        
        if (!empty($blog_posts)) {
            echo '<p><strong>Blog Posts Found:</strong></p>';
            foreach ($blog_posts as $post) {
                echo '<p>- ' . $post->post_title . ' (ID: ' . $post->ID . ')</p>';
                echo '<p>  URL: <a href="' . get_permalink($post->ID) . '" target="_blank">' . get_permalink($post->ID) . '</a></p>';
            }
        } else {
            echo '<p><strong>❌ No blog posts found</strong></p>';
        }
        
        // Check blog page
        $blog_page = get_page_by_path('blog');
        if ($blog_page) {
            echo '<p><strong>✅ Blog Page Found:</strong> ' . $blog_page->post_title . ' (ID: ' . $blog_page->ID . ')</p>';
            echo '<p>URL: <a href="' . get_permalink($blog_page->ID) . '" target="_blank">' . get_permalink($blog_page->ID) . '</a></p>';
        } else {
            echo '<p><strong>❌ Blog Page Not Found</strong></p>';
        }
        
        // Check if blog-section shortcode exists
        if (shortcode_exists('blog_section')) {
            echo '<p><strong>✅ Blog section shortcode exists</strong></p>';
        } else {
            echo '<p><strong>❌ Blog section shortcode does NOT exist</strong></p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_blog_system');
/**
 * Add templates folder to WordPress template hierarchy
 */
function neways_add_template_directory($template) {
    // Check if it's a page template request
    if (is_page_template()) {
        $templates_dir = get_template_directory() . '/templates/';
        
        // Look for page templates in the templates folder
        if (is_page()) {
            $page_template = get_page_template_slug();
            
            // Handle templates from templates/ folder
            if ($page_template && strpos($page_template, 'templates/') === 0) {
                $template_file = $page_template;
                if (file_exists(get_template_directory() . '/' . $template_file)) {
                    return get_template_directory() . '/' . $template_file;
                }
            }
            
            // Also check direct filename in templates folder
            if ($page_template && file_exists($templates_dir . $page_template)) {
                return $templates_dir . $page_template;
            }
        }
    }
    
    return $template;
}
add_filter('page_template', 'neways_add_template_directory');

/**
 * Register page templates from templates folder
 */
function neways_get_page_templates() {
    $templates = array();
    $templates_dir = get_template_directory() . '/templates/';
    
    if (is_dir($templates_dir)) {
        $files = glob($templates_dir . '*.php');
        foreach ($files as $file) {
            $filename = basename($file);
            if ($filename !== 'page.php') {
                $template_data = get_file_data($file, array('Template Name' => 'Template Name'));
                if (!empty($template_data['Template Name'])) {
                    $templates['templates/' . $filename] = $template_data['Template Name'];
                }
            }
        }
    }
    
    return $templates;
}

/**
 * Add page templates to the template dropdown
 */
function neways_add_page_templates($templates) {
    $custom_templates = neways_get_page_templates();
    return array_merge($templates, $custom_templates);
}
add_filter('theme_page_templates', 'neways_add_page_templates');

/**
 * Debug function to check if templates are being registered
 */
function neways_debug_templates() {
    if (current_user_can('manage_options') && isset($_GET['debug_templates'])) {
        $templates = neways_get_page_templates();
        echo '<pre>';
        echo "Registered Templates:\n";
        print_r($templates);
        echo '</pre>';
    }
}
add_action('admin_init', 'neways_debug_templates');

/**
 * AJAX handler for loading more events
 */
function neways_load_more_events() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'neways_ajax_nonce')) {
        wp_die('Security check failed');
    }
    
    $page = intval($_POST['page']);
    $today = date('Y-m-d');
    
    // Query past events for the specific page
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => 6,
        'paged' => $page,
        'meta_key' => 'event_start_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => 'event_start_date',
                'value' => $today,
                'compare' => '<',
                'type' => 'DATE'
            )
        )
    );
    
    $events_query = new WP_Query($args);
    
    if ($events_query->have_posts()) {
        ob_start();
        while ($events_query->have_posts()) {
            $events_query->the_post();
            
            // Get event details from ACF fields
            $event_date = '';
            $event_end_date = '';
            $event_location = '';
            
            if (function_exists('get_field')) {
                $event_date = get_field('event_start_date', get_the_ID());
                $event_end_date = get_field('event_end_date', get_the_ID());
                $event_location = get_field('event_location', get_the_ID());
            }
            
            // Fallback to WordPress meta fields if ACF fields are empty
            if (empty($event_date)) {
                $event_date = get_post_meta(get_the_ID(), '_event_start_date', true);
            }
            if (empty($event_end_date)) {
                $event_end_date = get_post_meta(get_the_ID(), '_event_end_date', true);
            }
            if (empty($event_location)) {
                $event_location = get_post_meta(get_the_ID(), '_event_location', true);
            }
            
            neways_render_event_card(get_the_ID(), $event_date, $event_end_date, $event_location);
        }
        wp_reset_postdata();
        
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error('No more events found');
    }
}
add_action('wp_ajax_load_more_events', 'neways_load_more_events');
add_action('wp_ajax_nopriv_load_more_events', 'neways_load_more_events');

/**
 * AJAX handler for loading more news
 */
function neways_load_more_news() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'neways_ajax_nonce')) {
        wp_die('Security check failed');
    }
    
    $page = intval($_POST['page']);
    $today = date('Y-m-d');
    
    // Query older news for the specific page
    $args = array(
        'post_type' => 'news',
        'posts_per_page' => 6,
        'paged' => $page,
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'before' => $today,
                'inclusive' => true,
            ),
        ),
    );
    
    $news_query = new WP_Query($args);
    
    if ($news_query->have_posts()) {
        ob_start();
        while ($news_query->have_posts()) {
            $news_query->the_post();
            $news_date = get_the_date('Y-m-d', get_the_ID());
            neways_render_news_card(get_the_ID(), $news_date);
        }
        wp_reset_postdata();
        
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error('No more news found');
    }
}
add_action('wp_ajax_load_more_news', 'neways_load_more_news');
add_action('wp_ajax_nopriv_load_more_news', 'neways_load_more_news');

/**
 * AJAX handler for loading more blog posts
 */
function neways_load_more_blog() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'neways_ajax_nonce')) {
        wp_die('Security check failed');
    }
    
    $page = intval($_POST['page']);
    $today = date('Y-m-d');
    
    // Query older blog posts for the specific page
    $args = array(
        'post_type' => 'blog',
        'posts_per_page' => 6,
        'paged' => $page,
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'before' => $today,
                'inclusive' => true,
            ),
        ),
    );
    
    $blog_query = new WP_Query($args);
    
    if ($blog_query->have_posts()) {
        ob_start();
        while ($blog_query->have_posts()) {
            $blog_query->the_post();
            $blog_date = get_the_date('Y-m-d', get_the_ID());
            neways_render_blog_card(get_the_ID(), $blog_date);
        }
        wp_reset_postdata();
        
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    } else {
        wp_send_json_error('No more blog posts found');
    }
}
add_action('wp_ajax_load_more_blog', 'neways_load_more_blog');
add_action('wp_ajax_nopriv_load_more_blog', 'neways_load_more_blog');

/**
 * Enqueue AJAX script with nonce
 */
function neways_enqueue_ajax_script() {
    wp_localize_script('neways-theme-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('neways_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'neways_enqueue_ajax_script');


// template for jobs
require_once get_template_directory() . '/jobs/theme-functions.php';

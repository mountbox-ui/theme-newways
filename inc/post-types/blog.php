<?php
/**
 * Register Blog Custom Post Type
 */

if (!defined('ABSPATH')) exit;

function neways_register_blog_post_type() {
    $labels = array(
        'name'               => _x('Blog Posts', 'post type general name', 'neways'),
        'singular_name'      => _x('Blog Post', 'post type singular name', 'neways'),
        'menu_name'          => _x('Blog', 'admin menu', 'neways'),
        'name_admin_bar'     => _x('Blog Post', 'add new on admin bar', 'neways'),
        'add_new'           => _x('Add New', 'blog post', 'neways'),
        'add_new_item'      => __('Add New Blog Post', 'neways'),
        'new_item'          => __('New Blog Post', 'neways'),
        'edit_item'         => __('Edit Blog Post', 'neways'),
        'view_item'         => __('View Blog Post', 'neways'),
        'all_items'         => __('All Blog Posts', 'neways'),
        'search_items'      => __('Search Blog Posts', 'neways'),
        'not_found'         => __('No blog posts found.', 'neways'),
        'not_found_in_trash'=> __('No blog posts found in Trash.', 'neways')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => false, // Disable direct access to single posts
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => false, // Disable rewrite rules for single posts
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 7,
        'menu_icon'         => 'dashicons-edit',
        'supports'          => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'      => true,
    );

    register_post_type('blog', $args);

    // Register Blog Category Taxonomy
    $cat_labels = array(
        'name'              => _x('Blog Categories', 'taxonomy general name', 'neways'),
        'singular_name'     => _x('Blog Category', 'taxonomy singular name', 'neways'),
        'search_items'      => __('Search Blog Categories', 'neways'),
        'all_items'         => __('All Blog Categories', 'neways'),
        'parent_item'       => __('Parent Blog Category', 'neways'),
        'parent_item_colon' => __('Parent Blog Category:', 'neways'),
        'edit_item'         => __('Edit Blog Category', 'neways'),
        'update_item'       => __('Update Blog Category', 'neways'),
        'add_new_item'      => __('Add New Blog Category', 'neways'),
        'new_item_name'     => __('New Blog Category Name', 'neways'),
        'menu_name'         => __('Categories', 'neways'),
    );

    register_taxonomy('blog_category', array('blog'), array(
        'hierarchical'      => true,
        'labels'           => $cat_labels,
        'show_ui'          => true,
        'show_admin_column'=> true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'blog-category'),
        'show_in_rest'     => true,
        'public'           => true,
        'publicly_queryable' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'    => true,
    ));
}
add_action('init', 'neways_register_blog_post_type');

/**
 * Flush rewrite rules on theme activation
 */
function neways_flush_blog_rewrite_rules() {
    neways_register_blog_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'neways_flush_blog_rewrite_rules');

/**
 * Flush rewrite rules when blog post type is updated
 */
function neways_flush_blog_rules_on_save() {
    if (get_post_type() == 'blog') {
        flush_rewrite_rules();
    }
}
add_action('save_post', 'neways_flush_blog_rules_on_save');

// Remove custom meta boxes - we'll use ACF instead
// The ACF fields will be registered in neways_register_blog_acf_fields()

// Meta box functions removed - using ACF fields instead
// ACF will handle saving automatically

/**
 * Custom function to render blog card (similar to events)
 */
function neways_render_blog_card($post_id, $blog_date = null, $use_acf = false) {
    // Debug: Force clear any caching
    if (current_user_can('manage_options')) {
        echo '<!-- Blog Card Debug: Post ID ' . $post_id . ' -->';
    }
    $categories = get_the_terms($post_id, 'blog_category');
    
    // Get read time from ACF or fallback to WordPress meta field (only if ACF is enabled)
    $read_time = '';
    if ($use_acf && function_exists('get_field')) {
        $read_time = get_field('blog_read_time', $post_id);
    }
    if (empty($read_time)) {
        $read_time = get_post_meta($post_id, '_blog_read_time', true);
    }
    // Get custom author info from ACF or fallback to WordPress meta fields (only if ACF is enabled)
    $custom_author_name = '';
    $author_bio = '';
    $author_image = '';
    
    // Try ACF fields first (only if ACF is enabled)
    if ($use_acf && function_exists('get_field')) {
        $custom_author_name = get_field('blog_author_name', $post_id);
        $author_bio = get_field('blog_author_bio', $post_id);
        $author_image = get_field('blog_author_image', $post_id);
        
    }
    
    // Fallback to WordPress meta fields if ACF fields are empty
    if (empty($custom_author_name)) {
        $custom_author_name = get_post_meta($post_id, '_blog_author_name', true);
    }
    if (empty($author_bio)) {
        $author_bio = get_post_meta($post_id, '_blog_author_bio', true);
    }
    
    // Always get the WordPress author ID
    $author_id = get_post_field('post_author', $post_id);
    
    // Use custom author name or fallback to WordPress author
    if (!empty($custom_author_name)) {
        $author_name = $custom_author_name;
    } else {
        $author_name = get_the_author_meta('display_name', $author_id);
    }
    
    // Use ACF author image or fallback to WordPress avatar
    if (!empty($author_image)) {
        if (is_array($author_image)) {
            // ACF Image field returns array
            $avatar_url = $author_image['sizes']['thumbnail'] ?? $author_image['url'];
            $avatar_alt = $author_image['alt'] ?? 'Author Avatar';
        } else {
            // ACF Image field returns URL (if return format is URL)
            $avatar_url = $author_image;
            $avatar_alt = 'Author Avatar';
        }
        $author_avatar = '<img src="' . esc_url($avatar_url) . '" alt="' . esc_attr($avatar_alt) . '" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 object-cover border border-gray-200 dark:border-gray-700 flex-shrink-0" />';
    } else {
        // Fallback to WordPress avatar
        $author_avatar = get_avatar($author_id, 32, '', '', ['class'=>'w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700']);
    }
    ?>
    <article class="flex flex-col items-start justify-between">
        <div class="relative w-full">
            <?php if (has_post_thumbnail($post_id)) : ?>
                <a href="<?php echo get_permalink($post_id); ?>" class="block overflow-hidden rounded-2xl">
                    <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'w-full h-64 bg-gray-100 object-cover object-center transition-transform duration-300 hover:scale-105 dark:bg-gray-800')); ?>
                </a>
            <?php else : ?>
                <div class="w-full h-64 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </div>
            <?php endif; ?>
            <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10 dark:ring-white/10"></div>
        </div>
        
        <div class="flex max-w-xl grow flex-col justify-between">
            <div class="mt-8 flex items-center gap-x-4 text-xs">
                <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>" class="text-gray-500 dark:text-gray-400">
                    <?php echo esc_html(get_the_date('M j, Y', $post_id)); ?>
                </time>
                <?php if ($categories && !is_wp_error($categories)) : ?>
                    <a href="<?php echo esc_url(get_term_link($categories[0])); ?>"
                       class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:bg-gray-800/60 dark:text-gray-300 dark:hover:bg-gray-800">
                        <?php echo esc_html($categories[0]->name); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="group relative grow">
                <h3 class="mt-3 text-lg font-semibold text-gray-900 group-hover:text-gray-600 dark:text-white dark:group-hover:text-gray-300">
                    <a href="<?php echo get_permalink($post_id); ?>">
                        <span class="absolute inset-0"></span>
                        <?php echo get_the_title($post_id); ?>
                    </a>
                </h3>
                <p class="mt-5 text-sm text-gray-600 dark:text-gray-400">
                    <?php 
                    // Use custom excerpt from ACF if available, otherwise use WordPress excerpt (only if ACF is enabled)
                    $custom_excerpt = '';
                    if ($use_acf && function_exists('get_field')) {
                        $custom_excerpt = get_field('blog_custom_excerpt', $post_id);
                    }
                    
                    if (!empty($custom_excerpt)) {
                        echo wp_trim_words($custom_excerpt, 25, '...');
                    } else {
                        echo wp_trim_words(get_the_excerpt($post_id), 25, '...');
                    }
                    ?>
                </p>
            </div>
            
            <div class="relative mt-8 flex items-center gap-x-4 justify-self-end">
                <?php echo $author_avatar; ?>
                <div class="text-sm">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        <?php if (!empty($custom_author_name)) : ?>
                            <?php echo esc_html($author_name); ?>
                        <?php else : ?>
                            <a href="<?php echo get_author_posts_url($author_id); ?>">
                                <span class="absolute inset-0"></span>
                                <?php echo esc_html($author_name); ?>
                            </a>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($author_bio)) : ?>
                        <p class="text-gray-600 dark:text-gray-400"><?php echo esc_html($author_bio); ?></p>
                    <?php elseif ($read_time) : ?>
                        <p class="text-gray-600 dark:text-gray-400"><?php echo esc_html($read_time); ?></p>
                    <?php else : ?>
                        <p class="text-gray-600 dark:text-gray-400">Author</p>
                    <?php endif; ?>
                    
                    <?php 
                    // Display blog tags if available (only if ACF is enabled)
                    $blog_tags = '';
                    if ($use_acf && function_exists('get_field')) {
                        $blog_tags = get_field('blog_tags', $post_id);
                    }
                    
                    if (!empty($blog_tags)) : 
                        $tags_array = array_map('trim', explode(',', $blog_tags));
                        ?>
                        <div class="mt-2 flex flex-wrap gap-1">
                            <?php foreach ($tags_array as $tag) : ?>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/20 dark:text-blue-200 dark:ring-blue-700/30">
                                    <?php echo esc_html($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Custom single blog handler - redirect to blog page with modal or custom display
 */
function neways_handle_single_blog() {
    // Check if we're on a single blog post page
    if (is_singular('blog') || (is_single() && get_post_type() === 'blog')) {
        // Get the blog post
        global $post;
        
        // Additional check to make sure we have a valid blog post
        if (!$post || $post->post_type !== 'blog') {
            return;
        }
        
        // Debug mode (only show if requested)
        if (isset($_GET['debug_blog_redirect']) && current_user_can('manage_options')) {
            echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 500px;">';
            echo '<h3>Blog Redirect Debug</h3>';
            echo '<p><strong>Post ID:</strong> ' . $post->ID . '</p>';
            echo '<p><strong>Post Title:</strong> ' . $post->post_title . '</p>';
            echo '<p><strong>Post Type:</strong> ' . $post->post_type . '</p>';
            echo '<p><strong>Current URL:</strong> ' . $_SERVER['REQUEST_URI'] . '</p>';
            
            // Check for blog page
            $blog_page = get_page_by_path('blog');
            if ($blog_page) {
                echo '<p><strong>Blog Page Found:</strong> ' . $blog_page->post_title . ' (ID: ' . $blog_page->ID . ')</p>';
                echo '<p><strong>Blog Page URL:</strong> ' . get_permalink($blog_page->ID) . '</p>';
            } else {
                echo '<p><strong>Blog Page:</strong> Not found</p>';
            }
            echo '</div>';
            return;
        }
        
        // Get the blog page URL - we know it exists from error check
        $blog_page = get_page_by_path('blog');
        $blog_page_url = null;
        
        if ($blog_page && $blog_page->post_status === 'publish') {
            $blog_page_url = get_permalink($blog_page->ID);
        } else {
            // Fallback: try to find any page with blog template
            $pages = get_posts(array(
                'post_type' => 'page',
                'meta_key' => '_wp_page_template',
                'meta_value' => 'templates/page-blog.php',
                'posts_per_page' => 1,
                'post_status' => 'publish'
            ));
            if (!empty($pages)) {
                $blog_page_url = get_permalink($pages[0]->ID);
            }
        }
        
        // If still no URL, use home as fallback
        if (!$blog_page_url) {
            $blog_page_url = home_url();
        }
        
        // Debug: Show redirect URL if requested
        if (isset($_GET['debug_redirect_url']) && current_user_can('manage_options')) {
            $redirect_url = add_query_arg('blog_id', $post->ID, $blog_page_url);
            echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999;">';
            echo '<h3>Blog Redirect Debug</h3>';
            echo '<p><strong>Blog Page ID:</strong> ' . ($blog_page ? $blog_page->ID : 'Not found') . '</p>';
            echo '<p><strong>Blog Page URL:</strong> ' . $blog_page_url . '</p>';
            echo '<p><strong>Redirect URL:</strong> ' . $redirect_url . '</p>';
            echo '<p><strong>Blog Post ID:</strong> ' . $post->ID . '</p>';
            echo '<p><a href="' . $redirect_url . '" target="_blank">Test Redirect URL</a></p>';
            echo '</div>';
            return;
        }
        
        // Redirect with blog_id parameter
        wp_redirect(add_query_arg('blog_id', $post->ID, $blog_page_url));
        exit;
    }
}
add_action('template_redirect', 'neways_handle_single_blog');

/**
 * Additional hook to catch blog post URLs that might bypass the main redirect
 */
function neways_catch_blog_urls() {
    // Check if we have a blog parameter in the URL
    if (isset($_GET['blog']) && !empty($_GET['blog'])) {
        $post_name = sanitize_text_field($_GET['blog']);
        
        // Find the blog post by name
        $blog_post = get_page_by_path($post_name, OBJECT, 'blog');
        
        if ($blog_post && $blog_post->post_type === 'blog') {
            // Redirect to blog page with blog_id parameter
            $blog_page = get_page_by_path('blog');
            if ($blog_page) {
                $blog_page_url = get_permalink($blog_page->ID);
                wp_redirect(add_query_arg('blog_id', $blog_post->ID, $blog_page_url));
                exit;
            }
        }
    }
    
    // Also check for /blog/post-name/ pattern (fallback)
    $request_uri = $_SERVER['REQUEST_URI'];
    if (preg_match('#^/blog/[^/]+/?$#', $request_uri)) {
        // Extract post name from URL
        $post_name = basename(trim($request_uri, '/'));
        
        // Find the blog post by name
        $blog_post = get_page_by_path($post_name, OBJECT, 'blog');
        
        if ($blog_post && $blog_post->post_type === 'blog') {
            // Redirect to blog page with blog_id parameter
            $blog_page = get_page_by_path('blog');
            if ($blog_page) {
                $blog_page_url = get_permalink($blog_page->ID);
                wp_redirect(add_query_arg('blog_id', $blog_post->ID, $blog_page_url));
                exit;
            }
        }
    }
}
add_action('init', 'neways_catch_blog_urls', 1);

/**
 * Debug function to check blog post URLs
 */
function neways_debug_blog_urls() {
    if (isset($_GET['debug_blog_urls']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 600px;">';
        echo '<h3>Blog URLs Debug</h3>';
        
        // Get blog posts
        $blog_posts = get_posts(array(
            'post_type' => 'blog',
            'numberposts' => 3,
            'post_status' => 'publish'
        ));
        
        if (!empty($blog_posts)) {
            echo '<p><strong>Blog Post URLs:</strong></p>';
            foreach ($blog_posts as $post) {
                $permalink = get_permalink($post->ID);
                echo '<p><strong>' . $post->post_title . ':</strong></p>';
                echo '<p>- Permalink: <a href="' . $permalink . '" target="_blank">' . $permalink . '</a></p>';
                echo '<p>- Post Name: ' . $post->post_name . '</p>';
                echo '<p>- Expected Redirect: <a href="' . home_url('/blog/?blog_id=' . $post->ID) . '" target="_blank">' . home_url('/blog/?blog_id=' . $post->ID) . '</a></p>';
                echo '<hr>';
            }
        } else {
            echo '<p><strong>No blog posts found</strong></p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_blog_urls');

/**
 * Test redirect function - add ?test_blog_redirect=post-name to test
 */
function neways_test_blog_redirect() {
    if (isset($_GET['test_blog_redirect']) && current_user_can('manage_options')) {
        $post_name = sanitize_text_field($_GET['test_blog_redirect']);
        
        // Find the blog post by name
        $blog_post = get_page_by_path($post_name, OBJECT, 'blog');
        
        if ($blog_post && $blog_post->post_type === 'blog') {
            // Get blog page
            $blog_page = get_page_by_path('blog');
            if ($blog_page) {
                $blog_page_url = get_permalink($blog_page->ID);
                $redirect_url = add_query_arg('blog_id', $blog_post->ID, $blog_page_url);
                
                echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999;">';
                echo '<h3>Blog Redirect Test</h3>';
                echo '<p><strong>Post Name:</strong> ' . $post_name . '</p>';
                echo '<p><strong>Blog Post ID:</strong> ' . $blog_post->ID . '</p>';
                echo '<p><strong>Blog Post Title:</strong> ' . $blog_post->post_title . '</p>';
                echo '<p><strong>Redirect URL:</strong> <a href="' . $redirect_url . '" target="_blank">' . $redirect_url . '</a></p>';
                echo '<p><a href="' . $redirect_url . '" target="_blank">Test Redirect</a></p>';
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999;">';
            echo '<h3>Blog Redirect Test - Error</h3>';
            echo '<p><strong>Post Name:</strong> ' . $post_name . '</p>';
            echo '<p><strong>Status:</strong> Blog post not found</p>';
            echo '</div>';
        }
    }
}
add_action('wp_head', 'neways_test_blog_redirect');

/**
 * Auto-create blog page if it doesn't exist
 */
function neways_auto_create_blog_page() {
    // Check if blog page exists
    $blog_page = get_page_by_path('blog');
    
    if (!$blog_page) {
        // Create the blog page
        $page_data = array(
            'post_title'   => 'Blog',
            'post_name'    => 'blog',
            'post_content' => 'This page displays all blog posts.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', 'templates/page-blog.php');
            
            // Flush rewrite rules
            flush_rewrite_rules();
        }
    }
}
add_action('init', 'neways_auto_create_blog_page');

/**
 * Force flush rewrite rules for blog post type changes
 */
function neways_flush_blog_rewrite_rules_now() {
    if (isset($_GET['flush_blog_rules']) && current_user_can('manage_options')) {
        flush_rewrite_rules();
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999;">';
        echo '<h3>Blog Rewrite Rules Flushed</h3>';
        echo '<p>Blog post type rewrite rules have been flushed. Blog posts should now redirect properly.</p>';
        echo '</div>';
    }
}
add_action('wp_head', 'neways_flush_blog_rewrite_rules_now');

/**
 * Debug blog page access
 */
function neways_debug_blog_page_access() {
    if (isset($_GET['debug_blog_page']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 600px;">';
        echo '<h3>Blog Page Access Debug</h3>';
        
        // Check blog page by slug
        $blog_page = get_page_by_path('blog');
        if ($blog_page) {
            echo '<p><strong>✅ Blog Page Found by Slug:</strong></p>';
            echo '<p>- Title: ' . $blog_page->post_title . '</p>';
            echo '<p>- ID: ' . $blog_page->ID . '</p>';
            echo '<p>- Status: ' . $blog_page->post_status . '</p>';
            echo '<p>- URL: <a href="' . get_permalink($blog_page->ID) . '" target="_blank">' . get_permalink($blog_page->ID) . '</a></p>';
        } else {
            echo '<p><strong>❌ Blog Page Not Found by Slug</strong></p>';
        }
        
        // Check blog page by template
        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => 'templates/page-blog.php',
            'posts_per_page' => 1,
            'post_status' => 'publish'
        ));
        
        if (!empty($pages)) {
            echo '<p><strong>✅ Blog Page Found by Template:</strong></p>';
            foreach ($pages as $page) {
                echo '<p>- Title: ' . $page->post_title . '</p>';
                echo '<p>- ID: ' . $page->ID . '</p>';
                echo '<p>- URL: <a href="' . get_permalink($page->ID) . '" target="_blank">' . get_permalink($page->ID) . '</a></p>';
            }
        } else {
            echo '<p><strong>❌ No Blog Page Found by Template</strong></p>';
        }
        
        // Check archive link
        $archive_url = get_post_type_archive_link('blog');
        if ($archive_url) {
            echo '<p><strong>✅ Blog Archive URL:</strong> <a href="' . $archive_url . '" target="_blank">' . $archive_url . '</a></p>';
        } else {
            echo '<p><strong>❌ Blog Archive URL Not Available</strong></p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_blog_page_access');

/**
 * Check for common blog errors and fix them
 */
function neways_check_and_fix_blog_errors() {
    if (isset($_GET['fix_blog_errors']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 700px;">';
        echo '<h3>Blog Error Check & Fix</h3>';
        
        $errors_found = false;
        $fixes_applied = array();
        
        // Check 1: Blog post type registration
        $post_type_obj = get_post_type_object('blog');
        if (!$post_type_obj) {
            echo '<p><strong>❌ ERROR:</strong> Blog post type not registered</p>';
            $errors_found = true;
        } else {
            echo '<p><strong>✅ Blog post type is registered</strong></p>';
        }
        
        // Check 2: Blog page exists
        $blog_page = get_page_by_path('blog');
        if (!$blog_page) {
            echo '<p><strong>❌ ERROR:</strong> Blog page does not exist</p>';
            $errors_found = true;
            
            // Fix: Create blog page
            $page_data = array(
                'post_title'   => 'Blog',
                'post_name'    => 'blog',
                'post_content' => 'This page displays all blog posts.',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
            );
            
            $page_id = wp_insert_post($page_data);
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', 'templates/page-blog.php');
                $fixes_applied[] = 'Created blog page (ID: ' . $page_id . ')';
                echo '<p><strong>🔧 FIXED:</strong> Created blog page</p>';
            } else {
                echo '<p><strong>❌ FAILED:</strong> Could not create blog page</p>';
            }
        } else {
            echo '<p><strong>✅ Blog page exists</strong> (ID: ' . $blog_page->ID . ')</p>';
            
            // Check if it has the right template
            $template = get_post_meta($blog_page->ID, '_wp_page_template', true);
            if ($template !== 'templates/page-blog.php') {
                echo '<p><strong>❌ ERROR:</strong> Blog page has wrong template (' . $template . ')</p>';
                $errors_found = true;
                
                // Fix: Set correct template
                update_post_meta($blog_page->ID, '_wp_page_template', 'templates/page-blog.php');
                $fixes_applied[] = 'Set correct template for blog page';
                echo '<p><strong>🔧 FIXED:</strong> Set correct template</p>';
            } else {
                echo '<p><strong>✅ Blog page has correct template</strong></p>';
            }
        }
        
        // Check 3: Blog posts exist
        $blog_posts = get_posts(array(
            'post_type' => 'blog',
            'numberposts' => 1,
            'post_status' => 'publish'
        ));
        
        if (empty($blog_posts)) {
            echo '<p><strong>❌ ERROR:</strong> No blog posts found</p>';
            $errors_found = true;
            echo '<p><strong>💡 SOLUTION:</strong> Create some blog posts in WordPress Admin</p>';
        } else {
            echo '<p><strong>✅ Blog posts exist</strong> (' . count($blog_posts) . ' found)</p>';
        }
        
        // Check 4: Blog shortcode exists
        if (!shortcode_exists('blog_section')) {
            echo '<p><strong>❌ ERROR:</strong> Blog section shortcode not registered</p>';
            $errors_found = true;
        } else {
            echo '<p><strong>✅ Blog section shortcode exists</strong></p>';
        }
        
        // Check 5: Template file exists
        $template_file = get_template_directory() . '/templates/page-blog.php';
        if (!file_exists($template_file)) {
            echo '<p><strong>❌ ERROR:</strong> Blog template file missing</p>';
            $errors_found = true;
        } else {
            echo '<p><strong>✅ Blog template file exists</strong></p>';
        }
        
        // Apply fixes
        if (!empty($fixes_applied)) {
            flush_rewrite_rules();
            echo '<p><strong>🔧 APPLIED FIXES:</strong></p>';
            foreach ($fixes_applied as $fix) {
                echo '<p>- ' . $fix . '</p>';
            }
            echo '<p><strong>✅ Rewrite rules flushed</strong></p>';
        }
        
        if (!$errors_found) {
            echo '<p><strong>🎉 NO ERRORS FOUND!</strong> Blog system should be working correctly.</p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_check_and_fix_blog_errors');

/**
 * Debug function to check blog author system
 */
function neways_debug_blog_author_system() {
    if (!current_user_can('manage_options') || !isset($_GET['debug_blog_authors'])) {
        return;
    }
    
    echo '<div style="background: #fff; border: 2px solid #0073aa; padding: 20px; margin: 20px; position: fixed; top: 50px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">';
    echo '<h3 style="margin: 0 0 10px 0; color: #0073aa;">🔍 Blog Author System Debug</h3>';
    
    // Check if blog posts exist
    $blog_posts = get_posts(array(
        'post_type' => 'blog',
        'posts_per_page' => 5,
        'post_status' => 'publish'
    ));
    
    echo '<p><strong>Blog Posts Found:</strong> ' . count($blog_posts) . '</p>';
    
    if (!empty($blog_posts)) {
        echo '<p><strong>Sample Blog Posts:</strong></p>';
        echo '<ul style="margin: 10px 0; padding-left: 20px;">';
        foreach ($blog_posts as $post) {
            $custom_author = get_post_meta($post->ID, '_blog_author_name', true);
            $author_bio = get_post_meta($post->ID, '_blog_author_bio', true);
            $author_image = get_post_meta($post->ID, '_blog_author_image', true);
            
            echo '<li>';
            echo '<strong>' . esc_html($post->post_title) . '</strong><br>';
            echo 'Custom Author: ' . ($custom_author ? esc_html($custom_author) : 'None') . '<br>';
            echo 'Author Bio: ' . ($author_bio ? esc_html($author_bio) : 'None') . '<br>';
            echo 'Custom Image: ' . ($author_image ? 'Yes' : 'No') . '<br>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p style="color: #d63638;"><strong>❌ No published blog posts found!</strong></p>';
        echo '<p><a href="' . admin_url('post-new.php?post_type=blog') . '" style="color: #0073aa;">Create a blog post</a></p>';
    }
    
    echo '<p><a href="' . remove_query_arg('debug_blog_authors') . '" style="background: #0073aa; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Close Debug</a></p>';
    echo '</div>';
}
add_action('wp_head', 'neways_debug_blog_author_system');

/**
 * Debug function to check blog post type registration
 * Add ?debug_blog=1 to any URL to see debug info
 */
function neways_debug_blog_registration() {
    if (isset($_GET['debug_blog']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Blog Post Type Debug Info</h3>';
        
        // Check if post type is registered
        $post_type_obj = get_post_type_object('blog');
        if ($post_type_obj) {
            echo '<p><strong>✅ Blog post type is registered</strong></p>';
            echo '<pre>' . print_r($post_type_obj, true) . '</pre>';
        } else {
            echo '<p><strong>❌ Blog post type is NOT registered</strong></p>';
        }
        
        // Check if taxonomy is registered
        $taxonomy_obj = get_taxonomy('blog_category');
        if ($taxonomy_obj) {
            echo '<p><strong>✅ Blog category taxonomy is registered</strong></p>';
            echo '<pre>' . print_r($taxonomy_obj, true) . '</pre>';
        } else {
            echo '<p><strong>❌ Blog category taxonomy is NOT registered</strong></p>';
        }
        
        // Check existing blog posts
        $blog_posts = get_posts(array(
            'post_type' => 'blog',
            'numberposts' => 5,
            'post_status' => 'publish'
        ));
        
        if (!empty($blog_posts)) {
            echo '<p><strong>Existing Blog Posts:</strong></p>';
            foreach ($blog_posts as $post) {
                echo '<p>- ' . $post->post_title . ' (ID: ' . $post->ID . ') - <a href="' . get_permalink($post->ID) . '">View</a></p>';
            }
        } else {
            echo '<p><strong>No blog posts found</strong></p>';
        }
        
        // Check blog page
        $blog_page = get_page_by_path('blog');
        if ($blog_page) {
            echo '<p><strong>Blog Page:</strong> ' . $blog_page->post_title . ' (ID: ' . $blog_page->ID . ') - <a href="' . get_permalink($blog_page->ID) . '">View</a></p>';
        } else {
            echo '<p><strong>Blog Page:</strong> Not found</p>';
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_blog_registration');
add_action('admin_head', 'neways_debug_blog_registration');

/**
 * ACF Field Groups - Create these manually in ACF plugin
 * 
 * ========================================
 * STEP-BY-STEP ACF SETUP GUIDE
 * ========================================
 * 
 * 1. CREATE OPTIONS PAGE:
 *    - Go to: WordPress Admin → Custom Fields → Options Pages
 *    - Click "Add New"
 *    - Page Title: "Blog Settings"
 *    - Menu Title: "Blog Settings"
 *    - Menu Slug: "blog-settings"
 *    - Parent Menu: "Blog" (or leave empty)
 *    - Save
 * 
 * 2. CREATE FIELD GROUP 1: "Blog Section Settings"
 *    - Go to: WordPress Admin → Custom Fields → Field Groups
 *    - Click "Add New"
 *    - Title: "Blog Section Settings"
 *    - Location Rules: Options Page is equal to blog-settings
 *    - Add these fields:
 * 
 *    Field 1: Blog Section Title
 *    - Field Label: "Blog Section Title"
 *    - Field Name: "blog_section_title"
 *    - Field Type: Text
 *    - Default Value: "From the blog"
 *    - Placeholder: "Enter section title"
 * 
 *    Field 2: Blog Section Subtitle
 *    - Field Label: "Blog Section Subtitle"
 *    - Field Name: "blog_section_subtitle"
 *    - Field Type: Textarea
 *    - Default Value: "Learn how to grow your business with our expert advice."
 *    - Placeholder: "Enter section subtitle"
 * 
 *    Field 3: Latest Blog Section Title
 *    - Field Label: "Latest Blog Section Title"
 *    - Field Name: "latest_blog_section_title"
 *    - Field Type: Text
 *    - Default Value: "Latest Blog Posts"
 * 
 *    Field 4: Latest Blog Section Subtitle
 *    - Field Label: "Latest Blog Section Subtitle"
 *    - Field Name: "latest_blog_section_subtitle"
 *    - Field Type: Textarea
 *    - Default Value: "Stay updated with our latest blog posts and articles."
 * 
 *    Field 5: Past Blog Section Title
 *    - Field Label: "Past Blog Section Title"
 *    - Field Name: "past_blog_section_title"
 *    - Field Type: Text
 *    - Default Value: "Past Blog Posts"
 * 
 *    Field 6: Past Blog Section Subtitle
 *    - Field Label: "Past Blog Section Subtitle"
 *    - Field Name: "past_blog_section_subtitle"
 *    - Field Type: Textarea
 *    - Default Value: "Browse through our previous blog posts and articles."
 * 
 * 3. CREATE FIELD GROUP 2: "Blog Post Details"
 *    - Go to: WordPress Admin → Custom Fields → Field Groups
 *    - Click "Add New"
 *    - Title: "Blog Post Details"
 *    - Location Rules: Post Type is equal to Blog
 *    - Add these fields:
 * 
 *    Field 1: Custom Author Name
 *    - Field Label: "Custom Author Name"
 *    - Field Name: "blog_author_name"
 *    - Field Type: Text
 *    - Instructions: "Leave empty to use WordPress author"
 *    - Placeholder: "Enter custom author name"
 *    - Width: 50%
 * 
 *    Field 2: Author Bio/Role
 *    - Field Label: "Author Bio/Role"
 *    - Field Name: "blog_author_bio"
 *    - Field Type: Text
 *    - Placeholder: "e.g., Marketing Director, Guest Writer, CEO"
 *    - Width: 50%
 * 
 *    Field 3: Author Image
 *    - Field Label: "Author Image"
 *    - Field Name: "blog_author_image"
 *    - Field Type: Image
 *    - Instructions: "Upload custom author image or leave empty to use WordPress avatar"
 *    - Return Format: Array
 *    - Preview Size: Thumbnail
 *    - Width: 50%
 * 
 *    Field 4: Estimated Read Time
 *    - Field Label: "Estimated Read Time"
 *    - Field Name: "blog_read_time"
 *    - Field Type: Text
 *    - Placeholder: "e.g., 5 min read"
 *    - Width: 50%
 * 
 *    Field 5: Featured Blog Post
 *    - Field Label: "Featured Blog Post"
 *    - Field Name: "blog_featured"
 *    - Field Type: True / False
 *    - Default Value: No
 *    - Stylised UI: Yes
 *    - Width: 50%
 * 
 *    Field 6: Custom Excerpt
 *    - Field Label: "Custom Excerpt"
 *    - Field Name: "blog_custom_excerpt"
 *    - Field Type: Textarea
 *    - Instructions: "Override the default excerpt with custom text"
 *    - Placeholder: "Enter custom excerpt..."
 *    - Rows: 3
 * 
 *    Field 7: Blog Tags
 *    - Field Label: "Blog Tags"
 *    - Field Name: "blog_tags"
 *    - Field Type: Text
 *    - Instructions: "Enter tags separated by commas"
 *    - Placeholder: "tag1, tag2, tag3"
 * 
 *    Field 8: Custom Blog Image
 *    - Field Label: "Custom Blog Image"
 *    - Field Name: "blog_custom_image"
 *    - Field Type: Image
 *    - Instructions: "Upload a custom image for this blog post"
 *    - Return Format: Array
 *    - Preview Size: Medium
 *    - Width: 50%
 * 
 *    Field 9: Blog Image Gallery
 *    - Field Label: "Blog Image Gallery"
 *    - Field Name: "blog_image_gallery"
 *    - Field Type: Gallery
 *    - Instructions: "Upload multiple images for this blog post"
 *    - Return Format: Array
 *    - Preview Size: Medium
 *    - Width: 50%
 * 
 * 4. SAVE AND PUBLISH:
 *    - Save both field groups
 *    - Publish them
 *    - The fields will now appear in the appropriate locations
 * 
 * ========================================
 * END OF SETUP GUIDE
 * ========================================
 */

/**
 * ACF Options Page - Create manually in ACF plugin
 * 
 * To create the Blog Settings options page:
 * 1. Go to WordPress Admin → Custom Fields → Options Pages
 * 2. Click "Add New"
 * 3. Set Page Title: "Blog Settings"
 * 4. Set Menu Title: "Blog Settings" 
 * 5. Set Menu Slug: "blog-settings"
 * 6. Set Parent Menu: "Blog" (or leave empty for top-level)
 * 7. Save
 */

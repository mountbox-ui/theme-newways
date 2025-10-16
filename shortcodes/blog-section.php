<?php
/**
 * Blog Section Shortcode
 * [blog_section limit="6" category="" show_past="false"]
 */

if (!defined('ABSPATH')) exit;

function neways_blog_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 3,
        'category' => '',
        'show_past' => 'false',
        'debug' => 'false',
        'title' => '',
        'subtitle' => '',
        'use_acf' => 'true'
    ), $atts, 'blog_section');

    // Convert show_past to boolean
    $show_past = filter_var($atts['show_past'], FILTER_VALIDATE_BOOLEAN);

    // Get title and subtitle - ACF fields are default for blog_section
    $section_title = '';
    $section_subtitle = '';
    
    // Use ACF fields by default (unless explicitly disabled)
    if (function_exists('get_field') && $atts['use_acf'] !== 'false') {
        $section_title = get_field('blog_section_title', 'option');
        $section_subtitle = get_field('blog_section_subtitle', 'option');
    }
    
    // Fallback to shortcode attributes if ACF fields are empty
    if (empty($section_title) && !empty($atts['title'])) {
        $section_title = $atts['title'];
    }
    if (empty($section_subtitle) && !empty($atts['subtitle'])) {
        $section_subtitle = $atts['subtitle'];
    }
    
    // Final fallback to default values
    if (empty($section_title)) {
        $section_title = 'From the blog';
    }
    if (empty($section_subtitle)) {
        $section_subtitle = 'Learn how to grow your business with our expert advice.';
    }

    $args = array(
        'post_type' => 'blog',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'date',
        'order' => 'DESC',
    );

    // Add category if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'blog_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    // Filter past blog posts if show_past is false (show only recent blog posts)
    if (!$show_past) {
        $args['date_query'] = array(
            array(
                'after' => date('Y-m-d', strtotime('-30 days')), // Show blog posts from last 30 days
                'inclusive' => true,
            )
        );
    }

    $blog_query = new WP_Query($args);
    
    // Debug mode
    $debug_mode = filter_var($atts['debug'], FILTER_VALIDATE_BOOLEAN);
    if ($debug_mode && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>Blog Shortcode Debug Info</h3>';
        echo '<p><strong>Shortcode Attributes:</strong></p>';
        echo '<pre>' . print_r($atts, true) . '</pre>';
        echo '<p><strong>Query Args:</strong></p>';
        echo '<pre>' . print_r($args, true) . '</pre>';
        echo '<p><strong>Query Found Posts:</strong> ' . $blog_query->found_posts . '</p>';
        echo '<p><strong>Query Post Count:</strong> ' . $blog_query->post_count . '</p>';
        if ($blog_query->have_posts()) {
            echo '<p><strong>Posts Found:</strong></p>';
            while ($blog_query->have_posts()) {
                $blog_query->the_post();
                echo '<p>- ' . get_the_title() . ' (ID: ' . get_the_ID() . ')</p>';
            }
            wp_reset_postdata();
        }
        echo '</div>';
    }
    
    ob_start();
    ?>

    <div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white"><?php echo esc_html($section_title); ?></h2>
          <p class="mt-2 text-lg/8 text-gray-600 dark:text-gray-300"><?php echo esc_html($section_subtitle); ?></p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
          <?php if ($blog_query->have_posts()) : ?>
            <?php while ($blog_query->have_posts()) : $blog_query->the_post();
                $blog_date = get_the_date('Y-m-d', get_the_ID());
                neways_render_blog_card(get_the_ID(), $blog_date, $atts['use_acf'] !== 'false');
            endwhile; ?>
          <?php else : ?>
            <div class="col-span-full text-center text-gray-600 dark:text-gray-400 py-12">
              <?php echo esc_html__('No blog posts available at the moment.', 'neways'); ?>
            </div>
          <?php endif; ?>
          <?php wp_reset_postdata(); ?>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('blog_section', 'neways_blog_section_shortcode');

/**
 * Latest Blog Shortcode
 * [latest_blog limit="6" category=""]
 */
function neways_latest_blog_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => '',
        'title' => '',
        'subtitle' => '',
        'use_acf' => 'false'
    ), $atts);
    
    // Get title and subtitle - ACF fields only work within shortcode context
    $section_title = '';
    $section_subtitle = '';
    
    // Only use ACF fields when shortcode is explicitly called (not on main pages)
    if (function_exists('get_field') && isset($atts['use_acf']) && $atts['use_acf'] === 'true') {
        $section_title = get_field('latest_blog_section_title', 'option');
        $section_subtitle = get_field('latest_blog_section_subtitle', 'option');
    }
    
    // Fallback to shortcode attributes if ACF fields are empty or not requested
    if (empty($section_title) && !empty($atts['title'])) {
        $section_title = $atts['title'];
    }
    if (empty($section_subtitle) && !empty($atts['subtitle'])) {
        $section_subtitle = $atts['subtitle'];
    }
    
    // Final fallback to default values
    if (empty($section_title)) {
        $section_title = 'Latest Blog Posts';
    }
    if (empty($section_subtitle)) {
        $section_subtitle = 'Stay updated with our latest blog posts and articles.';
    }
    
    // Query args for latest blog posts only
    $args = array(
        'post_type' => 'blog',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'after' => date('Y-m-d', strtotime('-30 days')), // Last 30 days
                'inclusive' => true,
            )
        )
    );

    // Add category if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'blog_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    $blog_query = new WP_Query($args);
    
    ob_start();
    ?>

    <div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white"><?php echo esc_html($section_title); ?></h2>
          <p class="mt-2 text-lg/8 text-gray-600 dark:text-gray-300"><?php echo esc_html($section_subtitle); ?></p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
          <?php if ($blog_query->have_posts()) : ?>
            <?php while ($blog_query->have_posts()) : $blog_query->the_post();
                $blog_date = get_the_date('Y-m-d', get_the_ID());
                neways_render_blog_card(get_the_ID(), $blog_date, $atts['use_acf'] !== 'false');
            endwhile; ?>
          <?php else : ?>
            <div class="col-span-full text-center text-gray-600 dark:text-gray-400 py-12">
              <?php echo esc_html__('No latest blog posts available.', 'neways'); ?>
            </div>
          <?php endif; ?>
          <?php wp_reset_postdata(); ?>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('latest_blog', 'neways_latest_blog_shortcode');

/**
 * Past Blog Shortcode
 * [past_blog limit="6" category=""]
 */
function neways_past_blog_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => '',
        'title' => '',
        'subtitle' => '',
        'use_acf' => 'false'
    ), $atts);
    
    // Get title and subtitle - ACF fields only work within shortcode context
    $section_title = '';
    $section_subtitle = '';
    
    // Only use ACF fields when shortcode is explicitly called (not on main pages)
    if (function_exists('get_field') && isset($atts['use_acf']) && $atts['use_acf'] === 'true') {
        $section_title = get_field('past_blog_section_title', 'option');
        $section_subtitle = get_field('past_blog_section_subtitle', 'option');
    }
    
    // Fallback to shortcode attributes if ACF fields are empty or not requested
    if (empty($section_title) && !empty($atts['title'])) {
        $section_title = $atts['title'];
    }
    if (empty($section_subtitle) && !empty($atts['subtitle'])) {
        $section_subtitle = $atts['subtitle'];
    }
    
    // Final fallback to default values
    if (empty($section_title)) {
        $section_title = 'Past Blog Posts';
    }
    if (empty($section_subtitle)) {
        $section_subtitle = 'Browse through our previous blog posts and articles.';
    }
    
    // Query args for past blog posts only
    $args = array(
        'post_type' => 'blog',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
            array(
                'before' => date('Y-m-d'),
                'inclusive' => true,
            )
        )
    );

    // Add category if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'blog_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    $blog_query = new WP_Query($args);
    
    ob_start();
    ?>

    <div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white"><?php echo esc_html($section_title); ?></h2>
          <p class="mt-2 text-lg/8 text-gray-600 dark:text-gray-300"><?php echo esc_html($section_subtitle); ?></p>
        </div>
        <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
          <?php if ($blog_query->have_posts()) : ?>
            <?php while ($blog_query->have_posts()) : $blog_query->the_post();
                $blog_date = get_the_date('Y-m-d', get_the_ID());
                neways_render_blog_card(get_the_ID(), $blog_date, $atts['use_acf'] !== 'false');
            endwhile; ?>
          <?php else : ?>
            <div class="col-span-full text-center text-gray-600 dark:text-gray-400 py-12">
              <?php echo esc_html__('No past blog posts found.', 'neways'); ?>
            </div>
          <?php endif; ?>
          <?php wp_reset_postdata(); ?>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('past_blog', 'neways_past_blog_shortcode');

/**
 * Test shortcode to verify shortcode system is working
 * [test_blog_shortcode]
 */
function neways_test_blog_shortcode($atts) {
    return '<div style="background: #e7f3ff; padding: 20px; margin: 20px; border: 1px solid #2196F3; border-radius: 4px;">
        <h3 style="color: #1976D2; margin-top: 0;">✅ Blog Shortcode System is Working!</h3>
        <p>This confirms that the blog shortcode system is functioning properly.</p>
        <p><strong>Available Blog Shortcodes:</strong></p>
        <ul>
            <li><code>[blog_section]</code> - Main blog section</li>
            <li><code>[blog_section debug="true"]</code> - With debug info</li>
            <li><code>[latest_blog]</code> - Latest blog posts (last 30 days)</li>
            <li><code>[past_blog]</code> - Past blog posts</li>
        </ul>
    </div>';
}
add_shortcode('test_blog_shortcode', 'neways_test_blog_shortcode');

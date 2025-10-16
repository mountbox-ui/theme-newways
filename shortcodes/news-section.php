<?php
if (!defined('ABSPATH')) exit;

function neways_news_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => '',
        'show_past' => 'false',
        'debug' => 'false'
    ), $atts, 'news_section');

    // Convert show_past to boolean
    $show_past = filter_var($atts['show_past'], FILTER_VALIDATE_BOOLEAN);

    // First, get manually selected homepage news
    $selected_args = array(
        'post_type' => 'news',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => '_show_on_homepage',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    
    $selected_news = new WP_Query($selected_args);
    $selected_count = $selected_news->found_posts;
    
    // Limit selected news to maximum 3
    $display_limit = min(3, intval($atts['limit']));
    $selected_posts = array();
    
    if ($selected_news->have_posts()) {
        $count = 0;
        while ($selected_news->have_posts() && $count < $display_limit) {
            $selected_news->the_post();
            $selected_posts[] = get_the_ID();
            $count++;
        }
        wp_reset_postdata();
    }
    
    // If we need more posts to reach the limit, get latest news
    $remaining_needed = $display_limit - count($selected_posts);
    $all_posts = $selected_posts;
    
    if ($remaining_needed > 0) {
        $latest_args = array(
            'post_type' => 'news',
            'posts_per_page' => $remaining_needed,
            'orderby' => 'date',
            'order' => 'DESC',
            'post__not_in' => $selected_posts, // Exclude already selected posts
        );
        
        $latest_news = new WP_Query($latest_args);
        if ($latest_news->have_posts()) {
            while ($latest_news->have_posts()) {
                $latest_news->the_post();
                $all_posts[] = get_the_ID();
            }
            wp_reset_postdata();
        }
    }
    
    // Create final query with the selected post IDs
    $args = array(
        'post_type' => 'news',
        'post__in' => $all_posts,
        'orderby' => 'post__in', // Maintain the order we created
        'posts_per_page' => count($all_posts),
    );

    // Add category if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    // Filter past news if show_past is false (show only recent news)
    if (!$show_past) {
        $args['date_query'] = array(
            array(
                'after' => date('Y-m-d', strtotime('-30 days')), // Show news from last 30 days
                'inclusive' => true,
            )
        );
    }

    $news_query = new WP_Query($args);
    
    // Debug mode
    $debug_mode = filter_var($atts['debug'], FILTER_VALIDATE_BOOLEAN);
    if ($debug_mode && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>News Shortcode Debug Info</h3>';
        echo '<p><strong>Shortcode Attributes:</strong></p>';
        echo '<pre>' . print_r($atts, true) . '</pre>';
        echo '<p><strong>Query Args:</strong></p>';
        echo '<pre>' . print_r($args, true) . '</pre>';
        echo '<p><strong>Query Found Posts:</strong> ' . $news_query->found_posts . '</p>';
        echo '<p><strong>Query Post Count:</strong> ' . $news_query->post_count . '</p>';
        if ($news_query->have_posts()) {
            echo '<p><strong>Posts Found:</strong></p>';
            while ($news_query->have_posts()) {
                $news_query->the_post();
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
        <div class="mx-auto max-w-2xl lg:max-w-4xl">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">From the news</h2>
          <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-400">Stay informed with our latest news and updates.</p>
          <div class="mt-16 space-y-20 lg:mt-20">
            <?php if ($news_query->have_posts()) : ?>
              <?php while ($news_query->have_posts()) : $news_query->the_post();
                  neways_render_horizontal_news_card(get_the_ID());
              endwhile; ?>
            <?php else : ?>
              <div class="text-center text-gray-600 dark:text-gray-400 py-12">
                <?php echo esc_html__('No news available at the moment.', 'neways'); ?>
              </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
          </div>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}

add_shortcode('news_section', 'neways_news_section_shortcode');

/**
 * Latest News Shortcode
 * [latest_news limit="6" category=""]
 */
function neways_latest_news_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => ''
    ), $atts);
    
    // Query args for latest news only
    $args = array(
        'post_type' => 'news',
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
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    $news_query = new WP_Query($args);
    
    ob_start();
    ?>

    <div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:max-w-4xl">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">Latest News</h2>
          <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-400">Stay updated with our latest news and announcements.</p>
          <div class="mt-16 space-y-20 lg:mt-20">
            <?php if ($news_query->have_posts()) : ?>
              <?php while ($news_query->have_posts()) : $news_query->the_post();
                  neways_render_horizontal_news_card(get_the_ID());
              endwhile; ?>
            <?php else : ?>
              <div class="text-center text-gray-600 dark:text-gray-400 py-12">
                <?php echo esc_html__('No latest news available.', 'neways'); ?>
              </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
          </div>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('latest_news', 'neways_latest_news_shortcode');

/**
 * Past News Shortcode
 * [past_news limit="6" category=""]
 */
function neways_past_news_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'category' => ''
    ), $atts);
    
    // Query args for past news only
    $args = array(
        'post_type' => 'news',
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
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    $news_query = new WP_Query($args);
    
    ob_start();
    ?>

    <div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:max-w-4xl">
          <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">Past News</h2>
          <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-400">Browse through our previous news and updates.</p>
          <div class="mt-16 space-y-20 lg:mt-20">
            <?php if ($news_query->have_posts()) : ?>
              <?php while ($news_query->have_posts()) : $news_query->the_post();
                  neways_render_horizontal_news_card(get_the_ID());
              endwhile; ?>
            <?php else : ?>
              <div class="text-center text-gray-600 dark:text-gray-400 py-12">
                <?php echo esc_html__('No past news found.', 'neways'); ?>
              </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
          </div>
        </div>
      </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('past_news', 'neways_past_news_shortcode');

/**
 * Test shortcode to verify shortcode system is working
 * [test_news_shortcode]
 */
function neways_test_news_shortcode($atts) {
    return '<div style="background: #e7f3ff; padding: 20px; margin: 20px; border: 1px solid #2196F3; border-radius: 4px;">
        <h3 style="color: #1976D2; margin-top: 0;">✅ News Shortcode System is Working!</h3>
        <p>This confirms that the shortcode system is functioning properly.</p>
        <p><strong>Available News Shortcodes:</strong></p>
        <ul>
            <li><code>[news_section]</code> - Main news section</li>
            <li><code>[news_section debug="true"]</code> - With debug info</li>
            <li><code>[latest_news]</code> - Latest news (last 30 days)</li>
            <li><code>[past_news]</code> - Past news</li>
        </ul>
    </div>';
}
add_shortcode('test_news_shortcode', 'neways_test_news_shortcode');

/**
 * Verify shortcode registration
 */
function neways_verify_news_shortcodes() {
    if (isset($_GET['verify_shortcodes']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>News Shortcode Verification</h3>';
        
        global $shortcode_tags;
        
        $news_shortcodes = array(
            'news_section',
            'latest_news', 
            'past_news',
            'test_news_shortcode'
        );
        
        foreach ($news_shortcodes as $shortcode) {
            if (isset($shortcode_tags[$shortcode])) {
                echo '<p>✅ <strong>' . $shortcode . '</strong> is registered</p>';
            } else {
                echo '<p>❌ <strong>' . $shortcode . '</strong> is NOT registered</p>';
            }
        }
        
        echo '</div>';
    }
}
add_action('wp_head', 'neways_verify_news_shortcodes');
add_action('admin_head', 'neways_verify_news_shortcodes');

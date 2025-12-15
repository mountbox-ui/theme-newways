<?php
if (!defined('ABSPATH')) exit;

function neways_news_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 3,
        'category' => '',
        'show_past' => 'false',
        'debug' => 'false'
    ), $atts, 'news_section');

    // Convert show_past to boolean
    $show_past = filter_var($atts['show_past'], FILTER_VALIDATE_BOOLEAN);

    // Always show exactly 3 news items
    $display_limit = 3;
    $selected_posts = array();
    $all_posts = array();

    // Step 1: Get manually selected homepage news (up to 3)
    $selected_args = array(
        'post_type' => 'news',
        'posts_per_page' => 3, // Limit to 3
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
    
    // Add category filter to selected args if specified
    if (!empty($atts['category'])) {
        $selected_args['tax_query'] = array(
            array(
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }
    
    $selected_news = new WP_Query($selected_args);
    
    // Collect selected homepage posts
    if ($selected_news->have_posts()) {
        while ($selected_news->have_posts()) {
            $selected_news->the_post();
            $selected_posts[] = get_the_ID();
        }
        wp_reset_postdata();
    }
    
    // Step 2: Calculate how many more posts we need to reach 3
    $remaining_needed = $display_limit - count($selected_posts);
    $all_posts = $selected_posts; // Start with selected posts
    
    // Store for debug output
    $selected_count = count($selected_posts);
    
    // Step 3: If we need more posts, get latest news (excluding already selected)
    if ($remaining_needed > 0) {
        $latest_args = array(
            'post_type' => 'news',
            'posts_per_page' => $remaining_needed,
            'orderby' => 'date',
            'order' => 'DESC',
            'post__not_in' => $selected_posts, // Exclude already selected posts
        );
        
        // Add category filter if specified
        if (!empty($atts['category'])) {
            $latest_args['tax_query'] = array(
                array(
                    'taxonomy' => 'news_category',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['category']),
                )
            );
        }
        
        // Filter past news if show_past is false (show only recent news)
        if (!$show_past) {
            $latest_args['date_query'] = array(
                array(
                    'after' => date('Y-m-d', strtotime('-30 days')), // Show news from last 30 days
                    'inclusive' => true,
                )
            );
        }
        
        $latest_news = new WP_Query($latest_args);
        if ($latest_news->have_posts()) {
            while ($latest_news->have_posts()) {
                $latest_news->the_post();
                $all_posts[] = get_the_ID();
            }
            wp_reset_postdata();
        }
    }
    
    // Step 4: Create final query with the combined post IDs
    // If no posts found, create empty query
    if (empty($all_posts)) {
        $args = array(
            'post_type' => 'news',
            'posts_per_page' => 0, // No posts
            'post__in' => array(0), // Force no results
        );
    } else {
        $args = array(
            'post_type' => 'news',
            'post__in' => $all_posts,
            'orderby' => 'post__in', // Maintain the order: selected first, then latest
            'posts_per_page' => count($all_posts),
        );
    }

    // Add category if specified (for final query)
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            )
        );
    }

    // Note: We don't apply date_query to final query because we want to preserve
    // homepage posts even if they're older than 30 days. Date filtering is already
    // applied to the "latest news" query above.

    $news_query = new WP_Query($args);
    
    // Debug mode
    $debug_mode = filter_var($atts['debug'], FILTER_VALIDATE_BOOLEAN);
    if ($debug_mode && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc;">';
        echo '<h3>News Shortcode Debug Info</h3>';
        echo '<p><strong>Shortcode Attributes:</strong></p>';
        echo '<pre>' . print_r($atts, true) . '</pre>';
        echo '<p><strong>Display Limit:</strong> ' . $display_limit . '</p>';
        echo '<p><strong>Selected Homepage Posts:</strong> ' . $selected_count . '</p>';
        if (!empty($selected_posts)) {
            echo '<p><strong>Selected Post IDs:</strong> ' . implode(', ', $selected_posts) . '</p>';
        }
        echo '<p><strong>Remaining Needed:</strong> ' . ($display_limit - $selected_count) . '</p>';
        echo '<p><strong>Total Posts to Display:</strong> ' . count($all_posts) . '</p>';
        echo '<p><strong>Final Query Args:</strong></p>';
        echo '<pre>' . print_r($args, true) . '</pre>';
        echo '<p><strong>Query Found Posts:</strong> ' . $news_query->found_posts . '</p>';
        echo '<p><strong>Query Post Count:</strong> ' . $news_query->post_count . '</p>';
        if ($news_query->have_posts()) {
            echo '<p><strong>Posts Found:</strong></p>';
            while ($news_query->have_posts()) {
                $news_query->the_post();
                $is_homepage = get_post_meta(get_the_ID(), '_show_on_homepage', true) === '1';
                echo '<p>- ' . get_the_title() . ' (ID: ' . get_the_ID() . ') ' . ($is_homepage ? '<strong>[Homepage]</strong>' : '[Latest]') . '</p>';
            }
            wp_reset_postdata();
        }
        echo '</div>';
    }
    
    ob_start();
    ?>

    <div class="bg-white py-8 sm:py-12 lg:py-20">
      <div class="mx-auto max-w-7xl px-0 sm:px-0 md:px-6 lg:px-8 py-8 sm:py-12 lg:py-16">
        <div class="flex flex-row sm:flex-row justify-between sm:items-center sm:justify-between gap-12 sm:gap-12 md:gap-6 pb-6 sm:pb-8 px-[12px]">
          <div class="text-center sm:text-left">
            <h2 class="text-balance text-3xl sm:text-4xl md:text-5xl lg:text-4xl font-normal font-marcellus tracking-tight text-gray-900">Latest news</h2>
          </div>
          <?php 
          // Get news page URL
          $news_page = get_page_by_path('news');
          $all_news_url = $news_page ? get_permalink($news_page->ID) : get_post_type_archive_link('news');
          if (!$all_news_url) {
              $all_news_url = '#';
          }
          ?>
          <div class="text-center sm:text-left">
            <a href="<?php echo esc_url($all_news_url); ?>" 
               class="btn-view group">
            <span>View all news</span>
            <span class="btn-readmore-arrow">
            <svg viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <!-- Arrow head -->
                            <g class="btn-hero-arrow-head">
                                <path d="M1 1C4.5 4 5 4.38484 5 4.5C5 4.61516 4.5 5 1 8" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <!-- Arrow body -->
                            <g class="btn-hero-arrow-body">
                                <path d="M3.5 4.5H0" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </g>
                        </svg>
            </span>
            </a>
          </div>
        </div>
        <div class="mx-auto grid max-w-2xl grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 sm:gap-x-8 gap-y-12 sm:gap-y-16 lg:gap-y-20 lg:mx-0 lg:max-w-none">
          <?php if ($news_query->have_posts()) : ?>
            <?php while ($news_query->have_posts()) : $news_query->the_post();
                $post_id = get_the_ID();
                $categories = get_the_terms($post_id, 'news_category');
                
                // Get category for badge
                $category_name = '';
                $category_link = '#';
                if ($categories && !is_wp_error($categories) && !empty($categories)) {
                    $first_category = $categories[0];
                    $category_name = $first_category->name;
                    $category_link = get_term_link($first_category);
                }
                
            ?>
            <article class="flex flex-col items-start justify-between">
              <div class="relative w-full overflow-hidden rounded-2xl">
                <?php if (has_post_thumbnail($post_id)) : ?>
                  <a href="<?php echo get_permalink($post_id); ?>" class="block w-full">
                    <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'w-full h-48 sm:h-56 md:h-64 lg:h-auto object-cover rounded-2xl bg-gray-100 sm:aspect-[2/1] lg:aspect-[3/2]')); ?>
                  </a>
                <?php else : ?>
                  <div class="w-full h-48 sm:h-56 md:h-64 lg:h-auto rounded-2xl bg-gray-100 sm:aspect-[2/1] lg:aspect-[3/2] flex items-center justify-center">
                    <span class="text-gray-400 text-xs">No Image</span>
                  </div>
                <?php endif; ?>
                <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10 pointer-events-none"></div>
              </div>
              <div class="flex max-w-xl grow flex-col justify-between w-full">
                <div class="mt-6 sm:mt-8 flex items-center flex-wrap gap-x-3 sm:gap-x-4 gap-y-2 text-xs">
                  <time datetime="<?php echo esc_attr(get_the_date('c', $post_id)); ?>" class="text-gray-500">
                    <?php echo esc_html(get_the_date('M j, Y', $post_id)); ?>
                  </time>
                  <?php if (!empty($category_name)) : ?>
                    <a href="<?php echo esc_url($category_link); ?>" class="relative z-10 rounded-full bg-gray-50 px-2.5 sm:px-3 py-1 sm:py-1.5 font-medium text-gray-600 hover:bg-gray-100 text-xs">
                      <?php echo esc_html($category_name); ?>
                    </a>
                  <?php endif; ?>
                </div>
                <div class="group relative grow">
                  <h3 class="mt-3 sm:mt-4 text-gray-900 group-hover:text-gray-600">
                    <a href="<?php echo get_permalink($post_id); ?>">
                      <span class="absolute inset-0"></span>
                      <?php echo get_the_title($post_id); ?>
                    </a>
                  </h3>
                  <p class="mt-4 sm:mt-5 line-clamp-3 paragraph-text font-normal text-gray-600">
                    <?php echo wp_trim_words(get_the_excerpt($post_id) ?: get_the_content($post_id), 20, '...'); ?>
                  </p>
                </div>
                <div class="mt-5 sm:mt-6">
                  <a href="<?php echo get_permalink($post_id); ?>" 
                     class="btn-readmore group pl-0 text-black opacity-60 font-normal">
                    <span>Read more</span>
                    <span class="btn-readmore-arrow opacity-60">
                            <svg viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <!-- Arrow head -->
                            <g class="btn-readmore-arrow-head">
                                <path d="M1 1C4.5 4 5 4.38484 5 4.5C5 4.61516 4.5 5 1 8" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <!-- Arrow body -->
                            <g class="btn-readmore-arrow-body">
                                <path d="M3.5 4.5H0" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </g>
                        </svg>
                            </span>
                  </a>
                </div>
              </div>
            </article>
            <?php endwhile; ?>
          <?php else : ?>
            <div class="col-span-3 text-center text-gray-600 py-12">
              <?php echo esc_html__('No news available at the moment.', 'neways'); ?>
            </div>
          <?php endif; ?>
          <?php wp_reset_postdata(); ?>
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

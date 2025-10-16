<?php
/**
 * Template Name: Events Page
 */

if (!defined('ABSPATH')) exit;

get_header(); ?>

<main class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 lg:py-16">
        <header class="mb-8">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl dark:text-white"><?php esc_html_e('Events & webinars', 'neways'); ?></h1>
        </header>



        <?php
        $today = date('Y-m-d');
        
        // Function to format event date range
        function neways_format_event_range($start, $end) {
            $s = $start ? strtotime($start) : null;
            $e = $end ? strtotime($end) : null;
            if ($s && $e) {
                if (date('Y-m', $s) === date('Y-m', $e)) {
                    return date_i18n('M j', $s) . ' - ' . date_i18n('j, Y', $e);
                } elseif (date('Y', $s) === date('Y', $e)) {
                    return date_i18n('M j', $s) . ' - ' . date_i18n('M j, Y', $e);
                }
                return date_i18n('M j, Y', $s) . ' - ' . date_i18n('M j, Y', $e);
            }
            if ($s) return date_i18n('M j, Y', $s);
            return '';
        }
        
        // Function to render event card
        function neways_render_event_card($post_id, $event_date, $event_end_date, $event_location) {
            $range = neways_format_event_range($event_date, $event_end_date);
            $categories = get_the_terms($post_id, 'event_category');
            $badge_text = 'EVENT';
            $badge_color = 'bg-blue-600';
            
            if ($categories && !is_wp_error($categories)) {
                $badge_text = strtoupper($categories[0]->name);
                if (stripos($badge_text, 'WEBINAR') !== false) {
                    $badge_color = 'bg-pink-600';
                } elseif (stripos($badge_text, 'EXPO') !== false) {
                    $badge_color = 'bg-purple-600';
                }
            }
            
            ?>
            <article class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
                <?php if (has_post_thumbnail($post_id)) : ?>
                    <div class="relative h-48 overflow-hidden">
                        <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'w-full h-full object-cover')); ?>
                        <div class="absolute top-3 left-3">
                            <span class="<?php echo $badge_color; ?> text-white px-2 py-1 text-xs font-semibold rounded"><?php echo esc_html($badge_text); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        <a href="<?php echo get_permalink($post_id); ?>" class="hover:text-blue-600 transition-colors duration-200"><?php echo get_the_title($post_id); ?></a>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <?php echo esc_html($range); ?>
                        <?php if ($event_location) : ?>
                            <br><?php echo esc_html($event_location); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </article>
            <?php
        }
        
        // Get upcoming events - only events with dates >= today
        $upcoming_args = array(
            'post_type' => 'event',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_key' => 'event_start_date',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'event_start_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE'
                ),
                array(
                    'key' => 'event_end_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE'
                )
            )
        );
        $upcoming_events = new WP_Query($upcoming_args);
        
        ?>

        <?php if ($upcoming_events->have_posts()) : ?>
        <!-- Upcoming Events Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><?php esc_html_e('Upcoming', 'neways'); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($upcoming_events->have_posts()) : $upcoming_events->the_post();
                    // Get event details from ACF fields first
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
                endwhile; ?>
            </div>
        </section>
        <?php endif; wp_reset_postdata(); ?>

        <?php
        // Get past events with pagination
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $past_args = array(
            'post_type' => 'event',
            'posts_per_page' => 6,
            'paged' => $paged,
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
        $past_events = new WP_Query($past_args);
        ?>

        <?php if ($past_events->have_posts()) : ?>
        <!-- Past Events Section -->
        <section>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6"><?php esc_html_e('Past', 'neways'); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($past_events->have_posts()) : $past_events->the_post();
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
                endwhile; ?>
            </div>
            
            <?php if ($past_events->max_num_pages > 1 && $paged < $past_events->max_num_pages) : ?>
                <div class="text-center mt-8">
                    <button id="load-more-events" 
                            data-page="<?php echo $paged; ?>" 
                            data-max-pages="<?php echo $past_events->max_num_pages; ?>"
                            class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span class="button-text"><?php esc_html_e('Load more', 'neways'); ?></span>
                        <span class="loading-text hidden"><?php esc_html_e('Loading...', 'neways'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; wp_reset_postdata(); ?>

        <?php if (!$upcoming_events->have_posts() && !$past_events->have_posts()) : ?>
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400"><?php esc_html_e('No events found.', 'neways'); ?></p>
            <p class="mt-4">
                <a href="<?php echo admin_url('post-new.php?post_type=event'); ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <?php esc_html_e('Create Event', 'neways'); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

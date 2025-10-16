<?php
/**
 * Template for displaying individual event posts
 */

if (!defined('ABSPATH')) exit;

get_header(); ?>

<main class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 lg:py-16">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('event-single'); ?>>
                <header class="mb-8">
                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                        <?php the_title(); ?>
                    </h1>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="featured-image mt-8">
                            <?php the_post_thumbnail('large', array('class' => 'w-full max-w-4xl mx-auto h-64 lg:h-96 object-cover rounded-lg shadow-lg')); ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                <!-- Event Details -->
                <div class="event-details mb-8">
                    <?php
                    // Get event details from ACF fields
                    $event_date = '';
                    $event_end_date = '';
                    $event_time = '';
                    $event_location = '';
                    
                    if (function_exists('get_field')) {
                        $event_date = get_field('event_start_date', get_the_ID());
                        $event_end_date = get_field('event_end_date', get_the_ID());
                        $event_time = get_field('event_time', get_the_ID());
                        $event_location = get_field('event_location', get_the_ID());
                    }
                    
                    // Fallback to WordPress meta fields if ACF fields are empty
                    if (empty($event_date)) {
                        $event_date = get_post_meta(get_the_ID(), '_event_start_date', true);
                    }
                    if (empty($event_end_date)) {
                        $event_end_date = get_post_meta(get_the_ID(), '_event_end_date', true);
                    }
                    if (empty($event_time)) {
                        $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                    }
                    if (empty($event_location)) {
                        $event_location = get_post_meta(get_the_ID(), '_event_location', true);
                    }
                    
                    // Format date and time
                    $start_ts = $event_date ? strtotime($event_date) : null;
                    $end_ts = $event_end_date ? strtotime($event_end_date) : null;
                    $time_formatted = $event_time ? date_i18n('g:i A', strtotime($event_time)) : '';
                    
                    $date_display = '';
                    if ($start_ts && $end_ts) {
                        if (date('Y-m', $start_ts) === date('Y-m', $end_ts)) {
                            // Same month & year: Sep 12 - 13, 2025
                            $date_display = date_i18n('M j', $start_ts) . ' - ' . date_i18n('j, Y', $end_ts);
                        } elseif (date('Y', $start_ts) === date('Y', $end_ts)) {
                            // Same year: Sep 30 - Oct 2, 2025
                            $date_display = date_i18n('M j', $start_ts) . ' - ' . date_i18n('M j, Y', $end_ts);
                        } else {
                            // Different years
                            $date_display = date_i18n('M j, Y', $start_ts) . ' - ' . date_i18n('M j, Y', $end_ts);
                        }
                    } elseif ($start_ts) {
                        $date_display = date_i18n('M j, Y', $start_ts);
                    }
                    ?>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if ($date_display) : ?>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <?php echo esc_html($date_display); ?>
                                            <?php if ($time_formatted) : ?>
                                                <span class="text-base font-normal text-gray-600 dark:text-gray-300">at <?php echo esc_html($time_formatted); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($event_location) : ?>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo esc_html($event_location); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Event Categories -->
                <?php
                $event_categories = get_the_terms(get_the_ID(), 'event_category');
                if ($event_categories && !is_wp_error($event_categories)) : ?>
                    <div class="mb-8">
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($event_categories as $category) : ?>
                                <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-800 px-3 py-1 text-sm font-medium dark:bg-blue-900 dark:text-blue-200">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Event Content -->
                <div class="prose prose-lg max-w-none dark:prose-invert">
                    <?php the_content(); ?>
                </div>
                
                <!-- Navigation -->
                <nav class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between">
                        <div>
                            <?php
                            $prev_post = get_previous_post(true, '', 'event_category');
                            if ($prev_post) : ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous Event
                                </a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php
                            $next_post = get_next_post(true, '', 'event_category');
                            if ($next_post) : ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    Next Event
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>

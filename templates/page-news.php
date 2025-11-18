<?php
/**
 * Template Name: News Page
 */

if (!defined('ABSPATH')) exit;

get_header(); 

// Check if we're showing a specific news post
$specific_news_id = isset($_GET['news_id']) ? intval($_GET['news_id']) : null;
$specific_news = null;

if ($specific_news_id && $specific_news_id > 0) {
    $specific_news = get_post($specific_news_id);
    if (!$specific_news || $specific_news->post_type !== 'news') {
        $specific_news = null;
    }
}
?>

<main class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 lg:py-16">
        <header class="mb-8">
            <?php if ($specific_news) : ?>
                <div class="mb-4">
                    <a href="<?php echo remove_query_arg('news_id'); ?>" 
                       class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to News
                    </a>
                </div>
            <?php endif; ?>
            
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                <?php if ($specific_news) : ?>
                    <?php echo esc_html($specific_news->post_title); ?>
                <?php else : ?>
                <?php esc_html_e('News & Updates', 'neways'); ?>
                <?php endif; ?>
            </h1>
        </header>

        <?php
        $today = date('Y-m-d');
        
        // Format date range (for news, usually just single date)
        function neways_format_news_date($date) {
            if ($date) {
                return date_i18n('M j, Y', strtotime($date));
            }
            return '';
        }


        // Display single news post if requested
        if ($specific_news) : ?>
            <article class="max-w-4xl mx-auto">
                <!-- Featured Image -->
                <?php if (has_post_thumbnail($specific_news->ID)) : ?>
                    <div class="mb-8">
                        <?php echo get_the_post_thumbnail($specific_news->ID, 'large', array('class' => 'w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg')); ?>
                    </div>
                <?php endif; ?>

                <!-- Post Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6">
                    <time datetime="<?php echo esc_attr(get_the_date('c', $specific_news->ID)); ?>" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?php echo esc_html(get_the_date('F j, Y', $specific_news->ID)); ?>
                    </time>
                    
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <?php echo get_the_author_meta('display_name', $specific_news->post_author); ?>
                    </span>
                    
                    <?php 
                    $categories = get_the_terms($specific_news->ID, 'news_category'); 
                    if ($categories && !is_wp_error($categories)) : ?>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <?php echo esc_html($categories[0]->name); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- News Meta Information -->
                <?php 
                $news_source = get_post_meta($specific_news->ID, '_news_source', true);
                $news_reporter = get_post_meta($specific_news->ID, '_news_reporter', true);
                ?>
                <?php if ($news_source || $news_reporter) : ?>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">News Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <?php if ($news_source) : ?>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Source:</span>
                                    <span class="text-gray-600 dark:text-gray-400"><?php echo esc_html($news_source); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($news_reporter) : ?>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Reporter:</span>
                                    <span class="text-gray-600 dark:text-gray-400"><?php echo esc_html($news_reporter); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Post Content -->
                <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                    <?php echo apply_filters('the_content', $specific_news->post_content); ?>
                </div>

                <!-- Share Buttons -->
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Share:</span>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($specific_news->post_title); ?>&url=<?php echo urlencode(get_permalink($specific_news->ID)); ?>" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink($specific_news->ID)); ?>" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        <?php else : ?>
            <?php
        // Get only the news marked as "highlighted" using the radio button (show only 1)
        $highlighted_args = array(
            'post_type' => 'news',
            'posts_per_page' => 1, // Show only 1 highlighted news
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_is_highlighted',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        $highlighted_news = new WP_Query($highlighted_args);
        ?>

        <!-- Highlighted News Section - Two Column Layout -->
        <?php if ($highlighted_news->have_posts()) : 
            $highlighted_news->the_post();
            $highlighted_id = get_the_ID();
        ?>
        <section class="mb-12">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12 items-start">
                <!-- Large Image on Left -->
                <div class="w-full lg:w-1/2">
                    <?php if (has_post_thumbnail($highlighted_id)) : ?>
                        <a href="<?php echo get_permalink($highlighted_id); ?>">
                            <?php echo get_the_post_thumbnail($highlighted_id, 'large', array('class' => 'w-full h-80 rounded-lg object-cover')); ?>
                        </a>
                    <?php else : ?>
                        <div class="w-full aspect-[4/3] rounded-lg bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-400 text-sm">No Image</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content on Right -->
                <div class="w-full lg:w-1/2 flex flex-col justify-center">
                    <div class="mb-4">
                        <span class="text-xs uppercase tracking-wider font-semibold" style="color: #242163;">HIGHLIGHTED NEWS</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-900 mb-4">
                        <a href="<?php echo get_permalink($highlighted_id); ?>" class="hover:text-gray-600 transition-colors">
                            <?php echo get_the_title($highlighted_id); ?>
                        </a>
                    </h2>
                    <time datetime="<?php echo esc_attr(get_the_date('c', $highlighted_id)); ?>" class="text-gray-500 text-base">
                        <?php echo esc_html(get_the_date('F j, Y', $highlighted_id)); ?>
                    </time>
                </div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
        <?php endif; ?>


        <?php
        // Show all news EXCEPT highlighted ones in Explore News section
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        
        // Get highlighted post IDs to exclude them from Explore News
        $highlighted_post_ids = array();
        if (isset($highlighted_id) && $highlighted_id > 0) {
            $highlighted_post_ids[] = $highlighted_id;
        }
        
        $explore_args = array(
            'post_type' => 'news',
            'posts_per_page' => 6,
            'paged' => $paged,
            'orderby' => 'date',
            'order' => 'DESC',
            'post__not_in' => $highlighted_post_ids, // Exclude highlighted posts
        );
        $older_news = new WP_Query($explore_args);
        ?>

        <?php if ($older_news->have_posts()) : ?>
        <!-- Past News Section -->
        <section>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                <?php esc_html_e('Explore News', 'neways'); ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($older_news->have_posts()) : $older_news->the_post();
                    $news_date = get_the_date('Y-m-d', get_the_ID());
                    neways_render_news_card(get_the_ID(), $news_date);
                endwhile; ?>
            </div>

            <?php if ($older_news->max_num_pages > 1 && $paged < $older_news->max_num_pages) : ?>
                <div class="text-center mt-8">
                    <button id="load-more-news" 
                            data-page="<?php echo $paged; ?>" 
                            data-max-pages="<?php echo $older_news->max_num_pages; ?>"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span class="button-text"><?php esc_html_e('Load more', 'neways'); ?></span>
                        <span class="loading-text hidden"><?php esc_html_e('Loading...', 'neways'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </section>
        <?php endif; wp_reset_postdata(); ?>


        <?php if (!$highlighted_news->have_posts() && !$older_news->have_posts()) : ?>
        <div class="text-center py-12">
            <p class="text-gray-600 dark:text-gray-400"><?php esc_html_e('No news found.', 'neways'); ?></p>
            <p class="mt-4">
                <a href="<?php echo admin_url('post-new.php?post_type=news'); ?>" class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <?php esc_html_e('Create News', 'neways'); ?>
                </a>
            </p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

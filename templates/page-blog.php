<?php
/**
 * Template Name: Blog Page
 */

if (!defined('ABSPATH')) exit;

get_header(); ?>

<main class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 lg:py-16">
        <header class="mb-8">
            <?php 
            // Check if we're showing a specific blog post
            $specific_blog_id = isset($_GET['blog_id']) ? intval($_GET['blog_id']) : null;
            $specific_blog = null;
            if ($specific_blog_id) {
                $specific_blog = get_post($specific_blog_id);
                if (!$specific_blog || $specific_blog->post_type !== 'blog') {
                    $specific_blog = null;
                }
            }
            ?>
            
            <?php if ($specific_blog) : ?>
                <div class="mb-4">
                    <a href="<?php echo remove_query_arg('blog_id'); ?>" 
                       class="inline-flex items-center gap-2 text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Blog
                    </a>
                </div>
            <?php endif; ?>
            
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                <?php if ($specific_blog) : ?>
                    <?php echo esc_html($specific_blog->post_title); ?>
                <?php else : ?>
                    <?php esc_html_e('Blog & Articles', 'neways'); ?>
                <?php endif; ?>
            </h1>
        </header>

        <?php
        $today = date('Y-m-d');
        
        // Format date range (for blog, usually just single date)
        function neways_format_blog_date($date) {
            if ($date) {
                return date_i18n('M j, Y', strtotime($date));
            }
            return '';
        }

        // Display single blog post if requested
        if ($specific_blog) : ?>
            <article class="max-w-4xl mx-auto">
                <!-- Featured Image -->
                <?php if (has_post_thumbnail($specific_blog->ID)) : ?>
                    <div class="mb-8">
                        <?php echo get_the_post_thumbnail($specific_blog->ID, 'large', array('class' => 'w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg')); ?>
                    </div>
                <?php endif; ?>

                <!-- Post Meta -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-6">
                    <time datetime="<?php echo esc_attr(get_the_date('c', $specific_blog->ID)); ?>" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?php echo esc_html(get_the_date('F j, Y', $specific_blog->ID)); ?>
                    </time>
                    
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <?php 
                        // Get custom author name from ACF or fallback to WordPress author
                        $custom_author_name = '';
                        if (function_exists('get_field')) {
                            $custom_author_name = get_field('blog_author_name', $specific_blog->ID);
                        }
                        if (empty($custom_author_name)) {
                            $custom_author_name = get_post_meta($specific_blog->ID, '_blog_author_name', true);
                        }
                        
                        
                        if (!empty($custom_author_name)) {
                            echo esc_html($custom_author_name);
                        } else {
                            echo esc_html(get_the_author_meta('display_name', $specific_blog->post_author));
                        }
                        ?>
                    </span>
                    
                    <?php 
                    // Get read time from ACF or fallback to WordPress meta field
                    $read_time = '';
                    if (function_exists('get_field')) {
                        $read_time = get_field('blog_read_time', $specific_blog->ID);
                    }
                    if (empty($read_time)) {
                        $read_time = get_post_meta($specific_blog->ID, '_blog_read_time', true);
                    }
                    if ($read_time) : ?>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php echo esc_html($read_time); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php 
                    $categories = get_the_terms($specific_blog->ID, 'blog_category'); 
                    if ($categories && !is_wp_error($categories)) : ?>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <?php echo esc_html($categories[0]->name); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Blog Meta Information -->
                <?php 
                // Get author bio from ACF or fallback to WordPress meta field
                $author_bio = '';
                if (function_exists('get_field')) {
                    $author_bio = get_field('blog_author_bio', $specific_blog->ID);
                }
                if (empty($author_bio)) {
                    $author_bio = get_post_meta($specific_blog->ID, '_blog_author_bio', true);
                }
                
                // Get featured status from ACF or fallback to WordPress meta field
                $is_featured = '';
                if (function_exists('get_field')) {
                    $is_featured = get_field('blog_featured', $specific_blog->ID);
                }
                if (empty($is_featured)) {
                    $is_featured = get_post_meta($specific_blog->ID, '_blog_featured', true);
                }
                ?>
                <?php if ($author_bio || $is_featured) : ?>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Blog Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <?php if ($author_bio) : ?>
                                <div>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Author Bio:</span>
                                    <span class="text-gray-600 dark:text-gray-400"><?php echo esc_html($author_bio); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($is_featured) : ?>
                                <div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                        ⭐ Featured Post
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Post Content -->
                <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                    <?php echo apply_filters('the_content', $specific_blog->post_content); ?>
                </div>

                <!-- Custom ACF Images -->
                <?php 
                // Get custom images from ACF fields
                $custom_images = array();
                if (function_exists('get_field')) {
                    // Single image field
                    $single_image = get_field('blog_custom_image', $specific_blog->ID);
                    if ($single_image) {
                        $custom_images[] = $single_image;
                    }
                    
                    // Gallery field (multiple images)
                    $gallery_images = get_field('blog_image_gallery', $specific_blog->ID);
                    if ($gallery_images && is_array($gallery_images)) {
                        $custom_images = array_merge($custom_images, $gallery_images);
                    }
                }
                
                // Display custom images if any exist
                if (!empty($custom_images)) : ?>
                    <div class="mt-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($custom_images as $image) : 
                                if (is_array($image)) {
                                    $image_url = $image['sizes']['large'] ?? $image['url'];
                                    $image_alt = $image['alt'] ?? 'Blog Image';
                                    $image_caption = $image['caption'] ?? '';
                                } else {
                                    $image_url = $image;
                                    $image_alt = 'Blog Image';
                                    $image_caption = '';
                                }
                            ?>
                                <div class="relative">
                                    <img src="<?php echo esc_url($image_url); ?>" 
                                         alt="<?php echo esc_attr($image_alt); ?>" 
                                         class="w-full h-64 object-cover rounded-lg shadow-lg">
                                    <?php if ($image_caption) : ?>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center italic">
                                            <?php echo esc_html($image_caption); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Share Buttons -->
                <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Share:</span>
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($specific_blog->post_title); ?>&url=<?php echo urlencode(get_permalink($specific_blog->ID)); ?>" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white hover:bg-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink($specific_blog->ID)); ?>" 
                           target="_blank" 
                           class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        <?php else :
            // Latest blog posts (last 30 days)
            $latest_args = array(
                'post_type' => 'blog',
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'date_query' => array(
                    array(
                        'after' => date('Y-m-d', strtotime('-30 days')),
                        'inclusive' => true,
                    )
                )
            );
            $latest_blog = new WP_Query($latest_args);
            ?>

            <?php if ($latest_blog->have_posts()) : ?>
            <!-- Latest Blog Section -->
            <section class="bg-white py-24 sm:py-32 dark:bg-gray-900">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">Recent Blogs</h2>
                    </div>
                    <div class="mx-auto mt-16 max-w-4xl">
                        <?php while ($latest_blog->have_posts()) : $latest_blog->the_post();
                            $post_id = get_the_ID();
                            $blog_date = get_the_date('F j, Y', $post_id);
                            
                            // Get ACF fields
                            $read_time = '';
                            $custom_author_name = '';
                            $author_bio = '';
                            
                            if (function_exists('get_field')) {
                                $read_time = get_field('blog_read_time', $post_id);
                                $custom_author_name = get_field('blog_author_name', $post_id);
                                $author_bio = get_field('blog_author_bio', $post_id);
                            }
                            
                            // Fallback to WordPress meta fields
                            if (empty($read_time)) {
                                $read_time = get_post_meta($post_id, '_blog_read_time', true);
                            }
                            if (empty($custom_author_name)) {
                                $custom_author_name = get_post_meta($post_id, '_blog_author_name', true);
                            }
                            if (empty($author_bio)) {
                                $author_bio = get_post_meta($post_id, '_blog_author_bio', true);
                            }
                            
                            // Use custom author name or fallback to WordPress author
                            if (!empty($custom_author_name)) {
                                $author_name = $custom_author_name;
                            } else {
                                $author_name = get_the_author_meta('display_name', get_post_field('post_author', $post_id));
                            }
                            ?>
                            
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                                <div class="flex flex-col lg:flex-row">
                                    <!-- Image on the left -->
                                    <div class="lg:w-1/2">
                                        <?php if (has_post_thumbnail($post_id)) : ?>
                                            <a href="<?php echo get_permalink($post_id); ?>">
                                                <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'w-full h-64 lg:h-full object-cover')); ?>
                                            </a>
                                        <?php else : ?>
                                            <div class="w-full h-64 lg:h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Content on the right -->
                                    <div class="lg:w-1/2 p-8 flex flex-col justify-center">
                                        <!-- Date and read time -->
                                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            <span><?php echo esc_html($blog_date); ?></span>
                                            <?php if ($read_time) : ?>
                                                <span>|</span>
                                                <span><?php echo esc_html($read_time); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Title -->
                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                            <a href="<?php echo get_permalink($post_id); ?>" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                <?php echo get_the_title($post_id); ?>
                                            </a>
                                        </h3>
                                        
                                        <!-- Excerpt -->
                                        <p class="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                                            <?php echo wp_trim_words(get_the_excerpt($post_id), 25, '...'); ?>
                                        </p>
                                        
                                        <!-- Author info -->
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">By <?php echo esc_html($author_name); ?></p>
                                                <?php if ($author_bio) : ?>
                                                    <p class="text-xs text-gray-600 dark:text-gray-400"><?php echo esc_html($author_bio); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        <?php endwhile; ?>
                    </div>
                </div>
            </section>
            <?php endif; wp_reset_postdata(); ?>

            <?php
            // Get the recent post ID to exclude it from past articles
            $recent_post_id = null;
            if ($latest_blog->have_posts()) {
                $latest_blog->the_post();
                $recent_post_id = get_the_ID();
                wp_reset_postdata();
            }
            
            // Older blog posts with pagination (excluding the recent post)
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $past_args = array(
                'post_type' => 'blog',
                'posts_per_page' => 6,
                'paged' => $paged,
                'orderby' => 'date',
                'order' => 'DESC',
                'date_query' => array(
                    array(
                        'before' => $today,
                        'inclusive' => true,
                    ),
                ),
            );
            
            // Exclude the recent post from past articles
            if ($recent_post_id) {
                $past_args['post__not_in'] = array($recent_post_id);
            }
            
            $older_blog = new WP_Query($past_args);
            ?>

            <?php if ($older_blog->have_posts()) : ?>
            <!-- Past Blog Section -->
            <section class="bg-white py-24 sm:py-32 dark:bg-gray-900">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">Past Articles</h2>
                        <p class="mt-2 text-lg/8 text-gray-600 dark:text-gray-300">Explore our archive of helpful content.</p>
                    </div>
                    <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                        <?php while ($older_blog->have_posts()) : $older_blog->the_post();
                            $blog_date = get_the_date('Y-m-d', get_the_ID());
                            neways_render_blog_card(get_the_ID(), $blog_date, true);
                        endwhile; ?>
                    </div>

                    <?php if ($older_blog->max_num_pages > 1 && $paged < $older_blog->max_num_pages) : ?>
                        <div class="text-center mt-16">
                            <button id="load-more-blog" 
                                    data-page="<?php echo $paged; ?>" 
                                    data-max-pages="<?php echo $older_blog->max_num_pages; ?>"
                                    class="inline-flex items-center gap-x-2 rounded-md bg-orange-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                <span class="button-text"><?php esc_html_e('Load more', 'neways'); ?></span>
                                <span class="loading-text hidden"><?php esc_html_e('Loading...', 'neways'); ?></span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php endif; wp_reset_postdata(); ?>

            <?php if (!$latest_blog->have_posts() && !$older_blog->have_posts()) : ?>
            <div class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400"><?php esc_html_e('No blog posts found.', 'neways'); ?></p>
                <p class="mt-4">
                    <a href="<?php echo admin_url('post-new.php?post_type=blog'); ?>" class="inline-block bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                        <?php esc_html_e('Create Blog Post', 'neways'); ?>
                    </a>
                </p>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

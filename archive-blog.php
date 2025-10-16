<?php
/**
 * The template for displaying blog archive pages
 */

if (!defined('ABSPATH')) exit;

get_header(); ?>

<main class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 lg:py-16">
        <header class="mb-8">
            <h1 class="text-3xl font-semibold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                <?php 
                if (is_tax('blog_category')) {
                    single_term_title('Blog Category: ');
                } else {
                    esc_html_e('All Blog Posts', 'neways');
                }
                ?>
            </h1>
            <?php if (is_tax('blog_category')) : ?>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                    <?php echo term_description(); ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 dark:bg-gray-800">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="relative h-48 overflow-hidden">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover hover:scale-105 transition-transform duration-300')); ?>
                                </a>
                                <div class="absolute top-3 left-3">
                                    <span class="bg-orange-600 text-white px-2 py-1 text-xs font-semibold rounded">BLOG</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex items-center gap-x-4 text-xs mb-3">
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="text-gray-500 dark:text-gray-400">
                                    <?php echo esc_html(get_the_date('M j, Y')); ?>
                                </time>
                                <?php 
                                $read_time = get_post_meta(get_the_ID(), '_blog_read_time', true);
                                if ($read_time) : ?>
                                    <span class="text-gray-500 dark:text-gray-400">•</span>
                                    <span class="text-gray-500 dark:text-gray-400"><?php echo esc_html($read_time); ?></span>
                                <?php endif; ?>
                                <?php 
                                $categories = get_the_terms(get_the_ID(), 'blog_category'); 
                                if ($categories && !is_wp_error($categories)) : ?>
                                    <a href="<?php echo esc_url(get_term_link($categories[0])); ?>" 
                                       class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                <a href="<?php the_permalink(); ?>" class="hover:text-orange-600 transition-colors duration-200">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-x-2">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 24, '', '', ['class'=>'size-6 rounded-full']); ?>
                                    <span class="text-xs text-gray-600 dark:text-gray-400"><?php the_author(); ?></span>
                                </div>
                                <a href="<?php the_permalink(); ?>" 
                                   class="text-sm font-medium text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300">
                                    Read more →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            $pagination_args = array(
                'mid_size'  => 2,
                'prev_text' => __('← Previous', 'neways'),
                'next_text' => __('Next →', 'neways'),
                'class'     => 'flex justify-center items-center gap-2 mt-8'
            );
            echo '<div class="pagination-wrapper">';
            echo paginate_links($pagination_args);
            echo '</div>';
            ?>

        <?php else : ?>
            <div class="text-center py-12">
                <div class="mx-auto max-w-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No blog posts found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <?php if (is_tax('blog_category')) : ?>
                            No blog posts found in this category.
                        <?php else : ?>
                            No blog posts have been published yet.
                        <?php endif; ?>
                    </p>
                    <div class="mt-6">
                        <a href="<?php echo home_url(); ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            Go back home
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

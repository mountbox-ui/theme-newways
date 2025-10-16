<?php
/**
 * The template for displaying all pages
 */

get_header(); ?>

<main class="container-custom py-8">
    <div class="max-w-4xl mx-auto">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('card'); ?>>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="aspect-w-16 aspect-h-9 mb-6">
                        <?php the_post_thumbnail('large', array('class' => 'w-full h-64 lg:h-96 object-cover rounded-lg')); ?>
                    </div>
                <?php endif; ?>
                
                <div class="p-6 lg:p-8">
                    <header class="entry-header mb-8">
                        <h1 class="entry-title text-3xl lg:text-4xl font-bold text-gray-900">
                            <?php the_title(); ?>
                        </h1>
                    </header>
                    
                    <div class="entry-content post-content">
                        <?php the_content(); ?>
                        
                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links flex items-center space-x-2 mt-8 pt-8 border-t border-gray-200"><span class="font-medium">' . esc_html__('Pages:', 'neways-theme') . '</span>',
                            'after'  => '</div>',
                            'link_before' => '<span class="inline-flex items-center justify-center w-8 h-8 text-sm border border-gray-300 rounded hover:bg-primary-50 hover:border-primary-300 transition-colors duration-200">',
                            'link_after' => '</span>',
                        ));
                        ?>
                    </div>
                </div>
            </article>
            
            <?php // Comments disabled site-wide: removed comments_template() call ?>
            
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>

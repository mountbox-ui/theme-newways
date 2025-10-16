<?php
/**
 * The template for displaying custom pages with header only
 */

get_header(); ?>

<main class="container-custom py-8">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('custom-page'); ?>>
            <header class="entry-header text-center mb-8">
                <h1 class="entry-title text-4xl lg:text-5xl font-bold text-gray-900">
                    <?php the_title(); ?>
                </h1>
                
                <?php if (has_post_thumbnail()) : ?>
                    <div class="featured-image mt-8">
                        <?php the_post_thumbnail('large', array('class' => 'w-full max-w-4xl mx-auto h-64 lg:h-96 object-cover rounded-lg shadow-lg')); ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <!-- Optional: Add minimal content if needed -->
            <div class="entry-content text-center max-w-3xl mx-auto">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>

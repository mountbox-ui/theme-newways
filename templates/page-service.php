<?php
/**
 * Template Name: Service Page
 * Template for displaying individual service pages with fixed width
 */

get_header(); ?>

<main class="bg-white py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-10">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header mb-8">
                    <h1 class="entry-title text-3xl lg:text-4xl font-bold text-gray-900">
                        <?php the_title(); ?>
                    </h1>
                </header>
                
                <div class="entry-content prose prose-lg max-w-none">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: Homepage Template
 * 
 * This template can be selected for any page that should use the homepage layout.
 * Perfect for homepage content with shortcodes.
 */

get_header(); ?>

<main class="homepage-content">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('homepage-article'); ?>>
            <div class="homepage-content-wrapper">
                
                <?php
                
                // Display the page content which will contain shortcodes
                the_content();
                ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>

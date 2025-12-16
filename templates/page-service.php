<?php
/**
 * Template Name: Service Page
 * Template for displaying individual service pages
 */

get_header(); ?>

<main class="service-page">
    <?php
    // Start the loop to display page content
    if (have_posts()) :
        while (have_posts()) : the_post();
            // Display the page content added in WordPress editor
            // No max-width constraint here since shortcodes have their own containers
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>

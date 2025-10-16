<?php
/**
 * The template for displaying 404 pages - Minimal Neways Theme
 */

get_header(); ?>

<main class="container-custom py-8">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-bold text-white mb-4">Neways Custom Theme</h1>
        <p class="text-white mb-4">404 - Page Not Found</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="text-primary-400 hover:text-primary-300">
            Back to Home
        </a>
    </div>
</main>

<?php get_footer(); ?>

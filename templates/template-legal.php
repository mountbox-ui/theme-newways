<?php
/**
 * Template Name: Legal Page Template
 * Description: Template for Privacy Policy, Terms & Conditions, and Modern Slavery Act pages
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('legal-page'); ?>>
                
                <!-- Hero Section -->
                <div class="legal-hero bg-gradient-to-br from-[#014854] to-[#0a2540] text-black py-16 sm:py-20 lg:py-[200px]">
                    <div class="max-w-4xl mx-auto px-6 lg:px-8">
                        <h1 class="font-lato text-4xl sm:text-5xl lg:text-6xl text-black font-normal mb-4">
                            <?php the_title(); ?>
                        </h1>
                        <?php if (get_the_modified_date()) : ?>
                            <p class="text-white/80 text-base font-lato">
                                Last Updated: <?php echo get_the_modified_date('F j, Y'); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="legal-content py-12 sm:py-16 lg:py-20 bg-white">
                    <div class="max-w-4xl mx-auto px-6 lg:px-8">
                        <div class="prose prose-lg max-w-none">
                            <?php
                            the_content();
                            
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'neways-theme'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>

                        <?php if (get_edit_post_link()) : ?>
                            <div class="mt-8 pt-8 border-t border-gray-200">
                                <?php
                                edit_post_link(
                                    sprintf(
                                        wp_kses(
                                            __('Edit <span class="screen-reader-text">"%s"</span>', 'neways-theme'),
                                            array(
                                                'span' => array(
                                                    'class' => array(),
                                                ),
                                            )
                                        ),
                                        get_the_title()
                                    ),
                                    '<span class="edit-link text-sm text-gray-600 hover:text-gray-900">',
                                    '</span>'
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back to Home Section -->
                <div class="legal-footer bg-gray-50 py-8">
                    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
                        <a href="<?php echo esc_url(home_url('/')); ?>" 
                           class="inline-flex items-center text-[#014854] hover:text-[#0a2540] transition-colors duration-200 font-lato font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Home
                        </a>
                    </div>
                </div>

            </article>

        <?php endwhile; ?>
    </main>
</div>

<?php
get_footer();


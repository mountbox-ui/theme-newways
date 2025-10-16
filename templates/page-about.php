<?php
/**
 * Template Name: About Page Template
 * 
 * Custom template for About pages with hero section and content areas.
 */

get_header(); ?>

<main class="about-page-content">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('about-page'); ?>>
            
            <!-- Hero Section -->
            <section class="page-hero bg-navy-800 py-16 lg:py-24">
                <div class="container-custom text-center">
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-6">
                        <?php the_title(); ?>
                    </h1>
                    
                    <?php if (has_excerpt()) : ?>
                        <p class="text-xl text-navy-200 max-w-3xl mx-auto">
                            <?php the_excerpt(); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="hero-image mt-8">
                            <?php the_post_thumbnail('large', array('class' => 'w-full max-w-4xl mx-auto h-64 lg:h-96 object-cover rounded-lg shadow-lg')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            
            <!-- About Highlights: 3 columns with heading, image and explanation -->
            <section class="about-highlights py-16 bg-gray-50">
                <div class="container-custom">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900">About Highlights</h2>
                        <p class="text-gray-600 mt-2">Key points about us</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <?php
                            $post_id = get_the_ID();
                            for ($i = 1; $i <= 3; $i++) {
                                $title    = get_post_meta($post_id, "about_block_{$i}_title", true);
                                $text     = get_post_meta($post_id, "about_block_{$i}_text", true);
                                $image_id = get_post_meta($post_id, "about_block_{$i}_image_id", true);

                                // Fallback defaults if custom fields are not set
                                if (empty($title)) {
                                    $defaults = [
                                        1 => ['Our Mission', 'We strive to deliver exceptional value and meaningful impact.'],
                                        2 => ['Our Team', 'A dedicated team committed to quality, innovation and service.'],
                                        3 => ['Our Values', 'Integrity, collaboration, and excellence guide everything we do.'],
                                    ];
                                    $title = $defaults[$i][0];
                                    if (empty($text)) {
                                        $text = $defaults[$i][1];
                                    }
                                }
                        ?>

                        <div class="rounded-lg bg-white border border-gray-200 shadow-sm overflow-hidden">
                            <div class="relative">
                                <?php if (!empty($image_id)) : ?>
                                    <?php echo wp_get_attachment_image($image_id, 'large', false, ['class' => 'w-full h-48 object-cover']); ?>
                                <?php else : ?>
                                    <div class="w-full h-48 bg-navy-800"></div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo esc_html($title); ?></h3>
                                <p class="text-gray-700"><?php echo wp_kses_post($text); ?></p>
                            </div>
                        </div>

                        <?php } ?>
                    </div>

                    <?php if (current_user_can('edit_posts')) : ?>
                        <!-- Editors: Customize these blocks via Custom Fields on this page:
                             about_block_1_title, about_block_1_text, about_block_1_image_id
                             about_block_2_title, about_block_2_text, about_block_2_image_id
                             about_block_3_title, about_block_3_text, about_block_3_image_id -->
                    <?php endif; ?>
                </div>
            </section>
            
            <!-- Content Section -->
            <section class="page-content py-16">
                <div class="container-custom">
                    <div class="max-w-4xl mx-auto prose prose-lg max-w-none">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>
            
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
